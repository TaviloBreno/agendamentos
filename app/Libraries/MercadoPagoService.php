<?php

namespace App\Libraries;

use App\Models\PaymentModel;
use App\Models\AppointmentModel;
use App\Entities\Payment;

/**
 * MercadoPagoService - Integração com Mercado Pago
 * 
 * Documentação: https://www.mercadopago.com.br/developers/pt/docs
 * 
 * Configuração (.env):
 * - MERCADOPAGO_ACCESS_TOKEN = seu_access_token
 * - MERCADOPAGO_PUBLIC_KEY = sua_public_key
 * - MERCADOPAGO_WEBHOOK_SECRET = seu_webhook_secret (opcional)
 */
class MercadoPagoService
{
    protected string $accessToken;
    protected string $publicKey;
    protected string $webhookSecret;
    protected string $apiUrl = 'https://api.mercadopago.com';
    protected bool $sandbox;

    protected PaymentModel $paymentModel;

    public function __construct()
    {
        $this->accessToken = env('MERCADOPAGO_ACCESS_TOKEN', '');
        $this->publicKey = env('MERCADOPAGO_PUBLIC_KEY', '');
        $this->webhookSecret = env('MERCADOPAGO_WEBHOOK_SECRET', '');
        $this->sandbox = env('MERCADOPAGO_SANDBOX', true);
        
        $this->paymentModel = model(PaymentModel::class);
    }

    /**
     * Verifica se está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->accessToken) && !empty($this->publicKey);
    }

    /**
     * Retorna a public key para o frontend
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    // =========================================================================
    // CHECKOUT PRO (REDIRECT)
    // =========================================================================

    /**
     * Cria preferência de pagamento (Checkout Pro)
     * 
     * @param array $data Dados do pagamento
     * @return array|null Preferência criada ou null em caso de erro
     */
    public function createPreference(array $data): ?array
    {
        $preference = [
            'items' => [[
                'id'          => $data['id'] ?? uniqid(),
                'title'       => $data['title'] ?? 'Agendamento',
                'description' => $data['description'] ?? '',
                'quantity'    => 1,
                'currency_id' => 'BRL',
                'unit_price'  => (float) $data['amount'],
            ]],
            'payer' => [
                'name'    => $data['payer_name'] ?? '',
                'email'   => $data['payer_email'] ?? '',
                'phone'   => [
                    'number' => preg_replace('/[^0-9]/', '', $data['payer_phone'] ?? ''),
                ],
                'identification' => [
                    'type'   => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $data['payer_document'] ?? ''),
                ],
            ],
            'back_urls' => [
                'success' => $data['success_url'] ?? base_url('payment/success'),
                'failure' => $data['failure_url'] ?? base_url('payment/failure'),
                'pending' => $data['pending_url'] ?? base_url('payment/pending'),
            ],
            'auto_return' => 'approved',
            'notification_url' => base_url('api/webhooks/mercadopago'),
            'external_reference' => $data['external_reference'] ?? null,
            'expires' => true,
            'expiration_date_from' => date('c'),
            'expiration_date_to' => date('c', strtotime('+24 hours')),
        ];

        // Adicionar metadata
        if (!empty($data['metadata'])) {
            $preference['metadata'] = $data['metadata'];
        }

        $response = $this->request('POST', '/checkout/preferences', $preference);

        if (isset($response['id'])) {
            return [
                'id' => $response['id'],
                'init_point' => $response['init_point'],
                'sandbox_init_point' => $response['sandbox_init_point'] ?? $response['init_point'],
            ];
        }

        log_message('error', 'MercadoPago createPreference error: ' . json_encode($response));
        return null;
    }

    // =========================================================================
    // PIX
    // =========================================================================

    /**
     * Cria pagamento PIX
     * 
     * @param array $data Dados do pagamento
     * @return array|null Dados do PIX ou null em caso de erro
     */
    public function createPix(array $data): ?array
    {
        $payment = [
            'transaction_amount' => (float) $data['amount'],
            'description' => $data['description'] ?? 'Pagamento via PIX',
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $data['payer_email'],
                'first_name' => $data['payer_name'] ?? '',
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $data['payer_document'] ?? ''),
                ],
            ],
            'notification_url' => base_url('api/webhooks/mercadopago'),
            'external_reference' => $data['external_reference'] ?? null,
        ];

        $response = $this->request('POST', '/v1/payments', $payment);

        if (isset($response['id'])) {
            $pixData = $response['point_of_interaction']['transaction_data'] ?? [];
            
            return [
                'payment_id' => $response['id'],
                'status' => $response['status'],
                'qr_code' => $pixData['qr_code'] ?? null,
                'qr_code_base64' => $pixData['qr_code_base64'] ?? null,
                'ticket_url' => $pixData['ticket_url'] ?? null,
                'expires_at' => $response['date_of_expiration'] ?? null,
            ];
        }

        log_message('error', 'MercadoPago createPix error: ' . json_encode($response));
        return null;
    }

    // =========================================================================
    // CARTÃO DE CRÉDITO (CHECKOUT TRANSPARENTE)
    // =========================================================================

    /**
     * Processa pagamento com cartão de crédito
     * 
     * @param array $data Dados do pagamento
     * @return array|null Resultado ou null em caso de erro
     */
    public function processCardPayment(array $data): ?array
    {
        $payment = [
            'transaction_amount' => (float) $data['amount'],
            'token' => $data['card_token'], // Token gerado pelo JS do MP
            'description' => $data['description'] ?? 'Pagamento com cartão',
            'installments' => (int) ($data['installments'] ?? 1),
            'payment_method_id' => $data['payment_method_id'],
            'payer' => [
                'email' => $data['payer_email'],
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $data['payer_document'] ?? ''),
                ],
            ],
            'notification_url' => base_url('api/webhooks/mercadopago'),
            'external_reference' => $data['external_reference'] ?? null,
        ];

        $response = $this->request('POST', '/v1/payments', $payment);

        if (isset($response['id'])) {
            return [
                'payment_id' => $response['id'],
                'status' => $response['status'],
                'status_detail' => $response['status_detail'] ?? null,
                'authorization_code' => $response['authorization_code'] ?? null,
            ];
        }

        log_message('error', 'MercadoPago processCardPayment error: ' . json_encode($response));
        return null;
    }

    // =========================================================================
    // BOLETO
    // =========================================================================

    /**
     * Cria pagamento por boleto
     */
    public function createBoleto(array $data): ?array
    {
        $payment = [
            'transaction_amount' => (float) $data['amount'],
            'description' => $data['description'] ?? 'Pagamento via Boleto',
            'payment_method_id' => 'bolbradesco', // ou 'pec' para Lotérica
            'payer' => [
                'email' => $data['payer_email'],
                'first_name' => $data['payer_name'] ?? '',
                'last_name' => $data['payer_last_name'] ?? '',
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $data['payer_document'] ?? ''),
                ],
                'address' => [
                    'zip_code' => preg_replace('/[^0-9]/', '', $data['payer_zip_code'] ?? ''),
                    'street_name' => $data['payer_street'] ?? '',
                    'street_number' => $data['payer_number'] ?? '',
                    'neighborhood' => $data['payer_neighborhood'] ?? '',
                    'city' => $data['payer_city'] ?? '',
                    'federal_unit' => $data['payer_state'] ?? '',
                ],
            ],
            'notification_url' => base_url('api/webhooks/mercadopago'),
            'external_reference' => $data['external_reference'] ?? null,
        ];

        $response = $this->request('POST', '/v1/payments', $payment);

        if (isset($response['id'])) {
            return [
                'payment_id' => $response['id'],
                'status' => $response['status'],
                'barcode' => $response['barcode']['content'] ?? null,
                'external_resource_url' => $response['transaction_details']['external_resource_url'] ?? null,
                'expires_at' => $response['date_of_expiration'] ?? null,
            ];
        }

        log_message('error', 'MercadoPago createBoleto error: ' . json_encode($response));
        return null;
    }

    // =========================================================================
    // CONSULTAS
    // =========================================================================

    /**
     * Consulta status de um pagamento
     */
    public function getPayment(string $paymentId): ?array
    {
        $response = $this->request('GET', "/v1/payments/{$paymentId}");
        
        return isset($response['id']) ? $response : null;
    }

    /**
     * Consulta preferência
     */
    public function getPreference(string $preferenceId): ?array
    {
        $response = $this->request('GET', "/checkout/preferences/{$preferenceId}");
        
        return isset($response['id']) ? $response : null;
    }

    // =========================================================================
    // REEMBOLSO
    // =========================================================================

    /**
     * Solicita reembolso total
     */
    public function refund(string $paymentId): ?array
    {
        $response = $this->request('POST', "/v1/payments/{$paymentId}/refunds");
        
        if (isset($response['id'])) {
            return [
                'refund_id' => $response['id'],
                'status' => $response['status'],
                'amount' => $response['amount'],
            ];
        }

        log_message('error', 'MercadoPago refund error: ' . json_encode($response));
        return null;
    }

    /**
     * Solicita reembolso parcial
     */
    public function partialRefund(string $paymentId, float $amount): ?array
    {
        $response = $this->request('POST', "/v1/payments/{$paymentId}/refunds", [
            'amount' => $amount,
        ]);
        
        if (isset($response['id'])) {
            return [
                'refund_id' => $response['id'],
                'status' => $response['status'],
                'amount' => $response['amount'],
            ];
        }

        log_message('error', 'MercadoPago partialRefund error: ' . json_encode($response));
        return null;
    }

    // =========================================================================
    // WEBHOOK
    // =========================================================================

    /**
     * Processa webhook do Mercado Pago
     */
    public function processWebhook(array $data): bool
    {
        $type = $data['type'] ?? $data['action'] ?? null;
        $paymentId = $data['data']['id'] ?? null;

        if (!$paymentId) {
            log_message('warning', 'MercadoPago webhook sem payment_id: ' . json_encode($data));
            return false;
        }

        // Buscar dados atualizados do pagamento
        $mpPayment = $this->getPayment($paymentId);
        
        if (!$mpPayment) {
            log_message('error', "MercadoPago webhook: pagamento {$paymentId} não encontrado na API");
            return false;
        }

        // Buscar pagamento local
        $localPayment = $this->paymentModel->findByGatewayId($paymentId);
        
        if (!$localPayment) {
            // Tentar encontrar por external_reference (appointment_id)
            if (!empty($mpPayment['external_reference'])) {
                // Criar pagamento se não existir
                log_message('info', "MercadoPago webhook: criando pagamento local para {$paymentId}");
            } else {
                log_message('warning', "MercadoPago webhook: pagamento local não encontrado para {$paymentId}");
                return false;
            }
        }

        // Mapear status do MP para status local
        $statusMap = [
            'pending'     => 'pending',
            'approved'    => 'approved',
            'authorized'  => 'processing',
            'in_process'  => 'processing',
            'in_mediation'=> 'processing',
            'rejected'    => 'rejected',
            'cancelled'   => 'cancelled',
            'refunded'    => 'refunded',
            'charged_back'=> 'refunded',
        ];

        $newStatus = $statusMap[$mpPayment['status']] ?? 'pending';
        
        // Atualizar pagamento local
        $updateData = [
            'status' => $newStatus,
            'payment_method' => $mpPayment['payment_method_id'] ?? null,
            'gateway_response' => json_encode($mpPayment),
            'webhook_received_at' => date('Y-m-d H:i:s'),
        ];

        if ($newStatus === 'approved') {
            $updateData['paid_at'] = date('Y-m-d H:i:s');
            
            // Atualizar status do agendamento
            if ($localPayment && $localPayment->appointment_id) {
                $appointmentModel = model(AppointmentModel::class);
                $appointmentModel->updateStatus($localPayment->appointment_id, 'confirmed');
            }
        }

        if ($localPayment) {
            $this->paymentModel->update($localPayment->id, $updateData);
        }

        log_message('info', "MercadoPago webhook processado: payment_id={$paymentId}, status={$newStatus}");
        return true;
    }

    /**
     * Valida assinatura do webhook
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        if (empty($this->webhookSecret)) {
            return true; // Se não tem secret configurado, aceita qualquer request
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }

    // =========================================================================
    // MÉTODOS AUXILIARES
    // =========================================================================

    /**
     * Faz requisição à API do Mercado Pago
     */
    protected function request(string $method, string $endpoint, ?array $data = null): array
    {
        $url = $this->apiUrl . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
            'X-Idempotency-Key: ' . uniqid(),
        ];

        $curl = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            if ($data) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        } elseif ($method === 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if ($data) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            log_message('error', "MercadoPago CURL error: {$error}");
            return ['error' => $error];
        }

        $decoded = json_decode($response, true) ?? [];

        if ($httpCode >= 400) {
            log_message('error', "MercadoPago HTTP {$httpCode}: " . $response);
        }

        return $decoded;
    }

    /**
     * Cria pagamento no banco de dados
     */
    public function createPaymentRecord(array $data): ?int
    {
        $paymentData = [
            'tenant_id' => $data['tenant_id'] ?? null,
            'appointment_id' => $data['appointment_id'] ?? null,
            'client_id' => $data['client_id'] ?? null,
            'gateway' => 'mercadopago',
            'gateway_payment_id' => $data['gateway_payment_id'] ?? null,
            'gateway_preference_id' => $data['gateway_preference_id'] ?? null,
            'amount' => $data['amount'],
            'status' => 'pending',
            'payer_email' => $data['payer_email'] ?? null,
            'payer_name' => $data['payer_name'] ?? null,
            'payer_document' => $data['payer_document'] ?? null,
            'description' => $data['description'] ?? null,
            'pix_qr_code' => $data['pix_qr_code'] ?? null,
            'pix_qr_code_base64' => $data['pix_qr_code_base64'] ?? null,
            'pix_copy_paste' => $data['pix_copy_paste'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'metadata' => json_encode($data['metadata'] ?? []),
        ];

        $id = $this->paymentModel->insert($paymentData);
        
        return $id ?: null;
    }
}
