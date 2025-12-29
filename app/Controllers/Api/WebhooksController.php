<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\MercadoPagoService;

/**
 * WebhooksController - Recebe webhooks de serviços externos
 */
class WebhooksController extends ResourceController
{
    protected $format = 'json';

    /**
     * Webhook do Mercado Pago
     * 
     * POST /api/webhooks/mercadopago
     */
    public function mercadopago()
    {
        // Log da requisição
        log_message('info', 'MercadoPago Webhook received: ' . file_get_contents('php://input'));

        $mercadoPago = new MercadoPagoService();

        // Validar assinatura (se configurado)
        $signature = $this->request->getHeaderLine('X-Signature');
        $payload = file_get_contents('php://input');
        
        if (!$mercadoPago->validateWebhookSignature($payload, $signature)) {
            log_message('warning', 'MercadoPago Webhook: assinatura inválida');
            return $this->respond(['status' => 'invalid_signature'], 401);
        }

        // Processar dados
        $data = $this->request->getJSON(true) ?? [];
        
        if (empty($data)) {
            return $this->respond(['status' => 'empty_payload'], 400);
        }

        // Tipos de notificação do MP
        $type = $data['type'] ?? $data['action'] ?? null;

        // Apenas processar notificações de pagamento
        if (!in_array($type, ['payment', 'payment.created', 'payment.updated'])) {
            return $this->respond(['status' => 'ignored', 'type' => $type]);
        }

        // Processar webhook
        $processed = $mercadoPago->processWebhook($data);

        if ($processed) {
            return $this->respond(['status' => 'processed']);
        }

        return $this->respond(['status' => 'error'], 500);
    }

    /**
     * Webhook do Stripe (futuro)
     */
    public function stripe()
    {
        // TODO: Implementar
        return $this->respond(['status' => 'not_implemented'], 501);
    }

    /**
     * Webhook do PagSeguro (futuro)
     */
    public function pagseguro()
    {
        // TODO: Implementar
        return $this->respond(['status' => 'not_implemented'], 501);
    }
}
