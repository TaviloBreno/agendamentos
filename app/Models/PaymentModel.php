<?php

namespace App\Models;

use App\Entities\Payment;
use CodeIgniter\Model;

/**
 * PaymentModel - Model para gerenciamento de pagamentos
 */
class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Payment::class;
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'tenant_id',
        'appointment_id',
        'client_id',
        'gateway',
        'gateway_payment_id',
        'gateway_preference_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'installments',
        'payer_email',
        'payer_name',
        'payer_document',
        'description',
        'pix_qr_code',
        'pix_qr_code_base64',
        'pix_copy_paste',
        'boleto_url',
        'boleto_barcode',
        'gateway_response',
        'webhook_received_at',
        'paid_at',
        'expires_at',
        'refunded_at',
        'refund_reason',
        'metadata',
    ];

    protected $validationRules = [
        'gateway' => 'required|in_list[mercadopago,stripe,pagseguro,pix,manual]',
        'amount'  => 'required|numeric|greater_than[0]',
        'status'  => 'required|in_list[pending,processing,approved,rejected,cancelled,refunded,expired]',
    ];

    // =========================================================================
    // QUERIES ESPECIAIS
    // =========================================================================

    /**
     * Busca por ID do gateway
     */
    public function findByGatewayId(string $gatewayId, string $gateway = 'mercadopago'): ?Payment
    {
        return $this->where('gateway_payment_id', $gatewayId)
            ->where('gateway', $gateway)
            ->first();
    }

    /**
     * Busca por preferência/checkout ID
     */
    public function findByPreferenceId(string $preferenceId): ?Payment
    {
        return $this->where('gateway_preference_id', $preferenceId)->first();
    }

    /**
     * Busca pagamentos de um agendamento
     */
    public function findByAppointment(int $appointmentId): array
    {
        return $this->where('appointment_id', $appointmentId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Busca pagamentos de um cliente
     */
    public function findByClient(int $clientId, int $limit = 50): array
    {
        return $this->where('client_id', $clientId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Busca pagamentos pendentes
     */
    public function getPending(): array
    {
        return $this->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Busca pagamentos expirados para atualizar
     */
    public function getExpired(): array
    {
        return $this->whereIn('status', ['pending', 'processing'])
            ->where('expires_at <', date('Y-m-d H:i:s'))
            ->findAll();
    }

    /**
     * Atualiza status do pagamento
     */
    public function updateStatus(int $id, string $status, ?array $extraData = null): bool
    {
        $data = ['status' => $status];
        
        if ($status === 'approved') {
            $data['paid_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'refunded') {
            $data['refunded_at'] = date('Y-m-d H:i:s');
        }
        
        if ($extraData) {
            $data = array_merge($data, $extraData);
        }
        
        return $this->update($id, $data);
    }

    /**
     * Marca como recebido webhook
     */
    public function markWebhookReceived(int $id, array $response): bool
    {
        return $this->update($id, [
            'webhook_received_at' => date('Y-m-d H:i:s'),
            'gateway_response' => json_encode($response),
        ]);
    }

    // =========================================================================
    // ESTATÍSTICAS
    // =========================================================================

    /**
     * Retorna estatísticas de pagamentos
     */
    public function getStats(?int $tenantId = null, ?string $period = 'month'): array
    {
        $builder = $this->builder();
        
        if ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        }
        
        // Filtro de período
        $startDate = match ($period) {
            'today' => date('Y-m-d'),
            'week'  => date('Y-m-d', strtotime('-7 days')),
            'month' => date('Y-m-01'),
            'year'  => date('Y-01-01'),
            default => null,
        };
        
        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }
        
        $stats = [
            'total_count' => 0,
            'total_amount' => 0,
            'approved_count' => 0,
            'approved_amount' => 0,
            'pending_count' => 0,
            'pending_amount' => 0,
            'rejected_count' => 0,
            'refunded_amount' => 0,
        ];
        
        // Total
        $total = clone $builder;
        $stats['total_count'] = $total->countAllResults();
        
        $totalAmount = clone $builder;
        $stats['total_amount'] = (float) $totalAmount->selectSum('amount')->get()->getRow()->amount ?? 0;
        
        // Aprovados
        $approved = clone $builder;
        $stats['approved_count'] = $approved->where('status', 'approved')->countAllResults();
        
        $approvedAmount = clone $builder;
        $stats['approved_amount'] = (float) $approvedAmount->where('status', 'approved')->selectSum('amount')->get()->getRow()->amount ?? 0;
        
        // Pendentes
        $pending = clone $builder;
        $stats['pending_count'] = $pending->whereIn('status', ['pending', 'processing'])->countAllResults();
        
        $pendingAmount = clone $builder;
        $stats['pending_amount'] = (float) $pendingAmount->whereIn('status', ['pending', 'processing'])->selectSum('amount')->get()->getRow()->amount ?? 0;
        
        // Rejeitados
        $rejected = clone $builder;
        $stats['rejected_count'] = $rejected->where('status', 'rejected')->countAllResults();
        
        // Reembolsados
        $refunded = clone $builder;
        $stats['refunded_amount'] = (float) $refunded->where('status', 'refunded')->selectSum('amount')->get()->getRow()->amount ?? 0;
        
        return $stats;
    }

    /**
     * Retorna receita por período (para gráficos)
     */
    public function getRevenueByPeriod(?int $tenantId = null, int $days = 30): array
    {
        $builder = $this->builder();
        
        if ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        }
        
        $result = $builder
            ->select("DATE(paid_at) as date, SUM(amount) as total")
            ->where('status', 'approved')
            ->where('paid_at >=', date('Y-m-d', strtotime("-{$days} days")))
            ->groupBy('DATE(paid_at)')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();
        
        // Preencher dias sem dados
        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $data[$date] = 0;
        }
        
        foreach ($result as $row) {
            $data[$row['date']] = (float) $row['total'];
        }
        
        return $data;
    }
}
