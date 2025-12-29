<?php

namespace App\Entities;

/**
 * Entity Payment
 * 
 * Representa um pagamento no sistema.
 * 
 * @property int         $id
 * @property int|null    $tenant_id
 * @property int|null    $appointment_id
 * @property int|null    $client_id
 * @property string      $gateway
 * @property string|null $gateway_payment_id
 * @property string|null $gateway_preference_id
 * @property float       $amount
 * @property string      $currency
 * @property string      $status
 * @property string|null $payment_method
 * @property int         $installments
 * @property string|null $payer_email
 * @property string|null $payer_name
 * @property string|null $payer_document
 * @property string|null $description
 * @property string|null $pix_qr_code
 * @property string|null $pix_qr_code_base64
 * @property string|null $pix_copy_paste
 * @property string|null $boleto_url
 * @property string|null $boleto_barcode
 * @property array|null  $gateway_response
 * @property \CodeIgniter\I18n\Time|null $webhook_received_at
 * @property \CodeIgniter\I18n\Time|null $paid_at
 * @property \CodeIgniter\I18n\Time|null $expires_at
 * @property \CodeIgniter\I18n\Time|null $refunded_at
 * @property string|null $refund_reason
 * @property array|null  $metadata
 */
class Payment extends MyBaseEntity
{
    protected $casts = [
        'id'              => 'integer',
        'tenant_id'       => 'integer',
        'appointment_id'  => 'integer',
        'client_id'       => 'integer',
        'amount'          => 'float',
        'installments'    => 'integer',
        'gateway_response'=> 'json-array',
        'metadata'        => 'json-array',
    ];

    protected $dates = [
        'created_at', 
        'updated_at', 
        'webhook_received_at', 
        'paid_at', 
        'expires_at', 
        'refunded_at'
    ];

    /**
     * Labels dos status
     */
    protected static array $statusLabels = [
        'pending'    => 'Pendente',
        'processing' => 'Processando',
        'approved'   => 'Aprovado',
        'rejected'   => 'Rejeitado',
        'cancelled'  => 'Cancelado',
        'refunded'   => 'Reembolsado',
        'expired'    => 'Expirado',
    ];

    /**
     * Classes CSS dos status
     */
    protected static array $statusClasses = [
        'pending'    => 'warning',
        'processing' => 'info',
        'approved'   => 'success',
        'rejected'   => 'danger',
        'cancelled'  => 'secondary',
        'refunded'   => 'dark',
        'expired'    => 'secondary',
    ];

    /**
     * Labels dos gateways
     */
    protected static array $gatewayLabels = [
        'mercadopago' => 'Mercado Pago',
        'stripe'      => 'Stripe',
        'pagseguro'   => 'PagSeguro',
        'pix'         => 'PIX Manual',
        'manual'      => 'Manual',
    ];

    /**
     * Labels dos métodos de pagamento
     */
    protected static array $methodLabels = [
        'credit_card' => 'Cartão de Crédito',
        'debit_card'  => 'Cartão de Débito',
        'pix'         => 'PIX',
        'boleto'      => 'Boleto',
        'account_money' => 'Saldo Mercado Pago',
    ];

    // =========================================================================
    // MÉTODOS DE FORMATAÇÃO
    // =========================================================================

    /**
     * Retorna valor formatado
     */
    public function amountFormatted(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Retorna label do status
     */
    public function statusLabel(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Retorna badge HTML do status
     */
    public function statusBadge(): string
    {
        $class = self::$statusClasses[$this->status] ?? 'secondary';
        $label = $this->statusLabel();
        return "<span class=\"badge badge-{$class}\">{$label}</span>";
    }

    /**
     * Retorna label do gateway
     */
    public function gatewayLabel(): string
    {
        return self::$gatewayLabels[$this->gateway] ?? $this->gateway;
    }

    /**
     * Retorna label do método de pagamento
     */
    public function methodLabel(): string
    {
        return self::$methodLabels[$this->payment_method] ?? $this->payment_method ?? '-';
    }

    /**
     * Retorna parcelas formatadas
     */
    public function installmentsFormatted(): string
    {
        if ($this->installments <= 1) {
            return 'À vista';
        }
        
        $valuePerInstallment = $this->amount / $this->installments;
        return "{$this->installments}x de R$ " . number_format($valuePerInstallment, 2, ',', '.');
    }

    /**
     * Retorna documento do pagador formatado
     */
    public function payerDocumentFormatted(): string
    {
        if (empty($this->payer_document)) {
            return '-';
        }

        $doc = preg_replace('/[^0-9]/', '', $this->payer_document);
        
        if (strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        }
        
        return $this->payer_document;
    }

    // =========================================================================
    // VERIFICAÇÕES DE STATUS
    // =========================================================================

    /**
     * Verifica se está pendente
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Verifica se foi aprovado
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Verifica se foi rejeitado
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Verifica se pode ser reembolsado
     */
    public function canRefund(): bool
    {
        return $this->status === 'approved' && $this->refunded_at === null;
    }

    /**
     * Verifica se expirou
     */
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if ($this->expires_at && strtotime($this->expires_at) < time()) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se é PIX
     */
    public function isPix(): bool
    {
        return $this->payment_method === 'pix';
    }

    /**
     * Verifica se é boleto
     */
    public function isBoleto(): bool
    {
        return $this->payment_method === 'boleto';
    }

    // =========================================================================
    // MÉTODOS ESTÁTICOS
    // =========================================================================

    /**
     * Retorna opções de status para dropdown
     */
    public static function getStatusOptions(): array
    {
        return self::$statusLabels;
    }

    /**
     * Retorna opções de gateway para dropdown
     */
    public static function getGatewayOptions(): array
    {
        return self::$gatewayLabels;
    }
}
