<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * NotificationQueueModel - Model para filas de notificações
 * 
 * @package    App\Models
 */
class NotificationQueueModel extends Model
{
    protected $table = 'notification_queue';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'type',
        'recipient',
        'subject',
        'message',
        'data',
        'status',
        'attempts',
        'max_attempts',
        'error_message',
        'scheduled_at',
        'sent_at',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // =========================================================================
    // MÉTODOS AUXILIARES
    // =========================================================================
    
    /**
     * Adiciona uma notificação à fila
     */
    public function addToQueue(string $type, string $recipient, string $message, array $data = [], ?string $scheduledAt = null): int|false
    {
        return $this->insert([
            'type'         => $type,
            'recipient'    => $recipient,
            'message'      => $message,
            'data'         => json_encode($data),
            'status'       => 'pending',
            'scheduled_at' => $scheduledAt ?? date('Y-m-d H:i:s'),
        ]);
    }
    
    /**
     * Adiciona notificação WhatsApp à fila
     */
    public function queueWhatsApp(string $phone, string $message, array $data = [], ?string $scheduledAt = null): int|false
    {
        return $this->addToQueue('whatsapp', $phone, $message, $data, $scheduledAt);
    }
    
    /**
     * Adiciona notificação Email à fila
     */
    public function queueEmail(string $email, string $subject, string $message, array $data = [], ?string $scheduledAt = null): int|false
    {
        return $this->insert([
            'type'         => 'email',
            'recipient'    => $email,
            'subject'      => $subject,
            'message'      => $message,
            'data'         => json_encode($data),
            'status'       => 'pending',
            'scheduled_at' => $scheduledAt ?? date('Y-m-d H:i:s'),
        ]);
    }
    
    /**
     * Obtém próximas notificações pendentes para processamento
     */
    public function getPendingNotifications(int $limit = 50): array
    {
        return $this->where('status', 'pending')
                    ->where('scheduled_at <=', date('Y-m-d H:i:s'))
                    ->where('attempts <', 3) // Usar valor fixo, não a coluna
                    ->orderBy('scheduled_at', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Marca notificação como em processamento
     */
    public function markAsProcessing(int $id): bool
    {
        return $this->update($id, [
            'status'   => 'processing',
            'attempts' => $this->db->query("SELECT attempts + 1 as a FROM notification_queue WHERE id = ?", [$id])->getRow()->a ?? 1,
        ]);
    }
    
    /**
     * Marca notificação como enviada
     */
    public function markAsSent(int $id): bool
    {
        return $this->update($id, [
            'status'  => 'sent',
            'sent_at' => date('Y-m-d H:i:s'),
        ]);
    }
    
    /**
     * Marca notificação como falha
     */
    public function markAsFailed(int $id, string $errorMessage): bool
    {
        // Verificar se atingiu máximo de tentativas
        $notification = $this->find($id);
        $attempts = ($notification->attempts ?? 0) + 1;
        $maxAttempts = $notification->max_attempts ?? 3;
        
        return $this->update($id, [
            'status'        => $attempts >= $maxAttempts ? 'failed' : 'pending',
            'attempts'      => $attempts,
            'error_message' => $errorMessage,
        ]);
    }
    
    /**
     * Obtém estatísticas da fila
     */
    public function getQueueStats(): array
    {
        $stats = $this->select('status, COUNT(*) as total')
                      ->groupBy('status')
                      ->findAll();
        
        $result = [
            'pending'    => 0,
            'processing' => 0,
            'sent'       => 0,
            'failed'     => 0,
        ];
        
        foreach ($stats as $stat) {
            $result[$stat->status] = $stat->total;
        }
        
        return $result;
    }
    
    /**
     * Limpa notificações antigas (enviadas há mais de X dias)
     */
    public function cleanOldNotifications(int $days = 30): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('status', 'sent')
                    ->where('sent_at <', $cutoffDate)
                    ->delete();
    }
}
