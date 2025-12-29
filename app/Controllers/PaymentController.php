<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\AppointmentModel;
use App\Libraries\MercadoPagoService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PaymentController - Controller de Pagamentos
 * 
 * Gerencia o fluxo de pagamento para agendamentos.
 */
class PaymentController extends BaseController
{
    protected PaymentModel $paymentModel;
    protected AppointmentModel $appointmentModel;
    protected MercadoPagoService $mercadoPago;

    public function __construct()
    {
        $this->paymentModel = model(PaymentModel::class);
        $this->appointmentModel = model(AppointmentModel::class);
        $this->mercadoPago = new MercadoPagoService();
    }

    /**
     * Página de checkout para um agendamento
     */
    public function checkout(int $appointmentId): string
    {
        $appointment = $this->appointmentModel->find($appointmentId);

        if (!$appointment) {
            return redirect()->to('/')->with('danger', 'Agendamento não encontrado.');
        }

        // Verificar se já foi pago
        $existingPayment = $this->paymentModel
            ->where('appointment_id', $appointmentId)
            ->where('status', 'approved')
            ->first();

        if ($existingPayment) {
            return redirect()->to('/my-schedules')
                ->with('info', 'Este agendamento já foi pago.');
        }

        // Buscar serviço para obter preço
        $serviceModel = model('ServiceModel');
        $service = $serviceModel->find($appointment->service_id);

        $data = [
            'title' => 'Pagamento',
            'appointment' => $appointment,
            'service' => $service,
            'publicKey' => $this->mercadoPago->getPublicKey(),
        ];

        return view('Front/payment/checkout', $data);
    }

    /**
     * Cria pagamento via PIX
     */
    public function createPix()
    {
        $appointmentId = $this->request->getPost('appointment_id');
        $appointment = $this->appointmentModel->find($appointmentId);

        if (!$appointment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Agendamento não encontrado.',
            ]);
        }

        $serviceModel = model('ServiceModel');
        $service = $serviceModel->find($appointment->service_id);

        $pixData = [
            'amount' => $service->price,
            'description' => "Agendamento #{$appointmentId} - {$service->name}",
            'payer_email' => $appointment->client_email,
            'payer_name' => $appointment->client_name,
            'payer_document' => $this->request->getPost('document'),
            'external_reference' => "appointment_{$appointmentId}",
        ];

        $result = $this->mercadoPago->createPix($pixData);

        if (!$result) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao gerar PIX. Tente novamente.',
            ]);
        }

        // Salvar pagamento no banco
        $this->mercadoPago->createPaymentRecord([
            'appointment_id' => $appointmentId,
            'client_id' => null,
            'gateway_payment_id' => $result['payment_id'],
            'amount' => $service->price,
            'payer_email' => $appointment->client_email,
            'payer_name' => $appointment->client_name,
            'description' => $pixData['description'],
            'pix_qr_code' => $result['qr_code'],
            'pix_qr_code_base64' => $result['qr_code_base64'],
            'pix_copy_paste' => $result['qr_code'],
            'expires_at' => $result['expires_at'],
        ]);

        return $this->response->setJSON([
            'success' => true,
            'pix' => [
                'qr_code' => $result['qr_code'],
                'qr_code_base64' => $result['qr_code_base64'],
                'expires_at' => $result['expires_at'],
            ],
        ]);
    }

    /**
     * Cria preferência de pagamento (Checkout Pro)
     */
    public function createPreference()
    {
        $appointmentId = $this->request->getPost('appointment_id');
        $appointment = $this->appointmentModel->find($appointmentId);

        if (!$appointment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Agendamento não encontrado.',
            ]);
        }

        $serviceModel = model('ServiceModel');
        $service = $serviceModel->find($appointment->service_id);

        $preferenceData = [
            'id' => "appointment_{$appointmentId}",
            'title' => $service->name,
            'description' => "Agendamento para {$appointment->dateFormatted()} às {$appointment->start_time}",
            'amount' => $service->price,
            'payer_email' => $appointment->client_email,
            'payer_name' => $appointment->client_name,
            'payer_phone' => $appointment->client_phone,
            'external_reference' => "appointment_{$appointmentId}",
            'success_url' => base_url("payment/success/{$appointmentId}"),
            'failure_url' => base_url("payment/failure/{$appointmentId}"),
            'pending_url' => base_url("payment/pending/{$appointmentId}"),
            'metadata' => [
                'appointment_id' => $appointmentId,
            ],
        ];

        $result = $this->mercadoPago->createPreference($preferenceData);

        if (!$result) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar preferência de pagamento.',
            ]);
        }

        // Salvar referência da preferência
        $this->mercadoPago->createPaymentRecord([
            'appointment_id' => $appointmentId,
            'gateway_preference_id' => $result['id'],
            'amount' => $service->price,
            'payer_email' => $appointment->client_email,
            'payer_name' => $appointment->client_name,
            'description' => $preferenceData['description'],
        ]);

        return $this->response->setJSON([
            'success' => true,
            'preference_id' => $result['id'],
            'init_point' => $result['init_point'],
        ]);
    }

    /**
     * Página de sucesso
     */
    public function success(int $appointmentId): string
    {
        $appointment = $this->appointmentModel->find($appointmentId);

        return view('Front/payment/success', [
            'title' => 'Pagamento Confirmado',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Página de falha
     */
    public function failure(int $appointmentId): string
    {
        $appointment = $this->appointmentModel->find($appointmentId);

        return view('Front/payment/failure', [
            'title' => 'Pagamento não Processado',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Página de pendente
     */
    public function pending(int $appointmentId): string
    {
        $appointment = $this->appointmentModel->find($appointmentId);

        return view('Front/payment/pending', [
            'title' => 'Pagamento Pendente',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Verifica status do pagamento (polling)
     */
    public function checkStatus(int $appointmentId)
    {
        $payment = $this->paymentModel
            ->where('appointment_id', $appointmentId)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$payment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pagamento não encontrado.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'status' => $payment->status,
            'is_approved' => $payment->isApproved(),
            'is_pending' => $payment->isPending(),
        ]);
    }
}
