<?php

namespace App\Models;

use App\Entities\Notification;
use CodeIgniter\Model;

/**
 * NotificationModel - Model para operações com Notificações
 * 
 * @package    App\Models
 */
class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Notification::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'link',
        'data',
        'read_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'type'    => 'required|in_list[appointment,message,system,payment,reminder,alert,success]',
        'title'   => 'required|min_length[3]|max_length[255]',
        'message' => 'permit_empty',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'O usuário é obrigatório',
        ],
        'type' => [
            'required' => 'O tipo de notificação é obrigatório',
            'in_list'  => 'Tipo de notificação inválido',
        ],
        'title' => [
            'required'   => 'O título é obrigatório',
            'min_length' => 'O título deve ter pelo menos 3 caracteres',
        ],
    ];

    // =========================================================================
    // QUERIES CUSTOMIZADAS
    // =========================================================================

    /**
     * Retorna notificações de um usuário
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getByUser(int $userId, int $limit = 20): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Retorna notificações não lidas de um usuário
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUnread(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
            ->where('read_at', null)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Conta notificações não lidas de um usuário
     * 
     * @param int $userId
     * @return int
     */
    public function countUnread(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('read_at', null)
            ->countAllResults();
    }

    /**
     * Marca uma notificação como lida
     * 
     * @param int $notificationId
     * @param int $userId Para garantir que pertence ao usuário
     * @return bool
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        return $this->where('id', $notificationId)
            ->where('user_id', $userId)
            ->set('read_at', date('Y-m-d H:i:s'))
            ->update();
    }

    /**
     * Marca todas as notificações de um usuário como lidas
     * 
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead(int $userId): bool
    {
        return $this->where('user_id', $userId)
            ->where('read_at', null)
            ->set('read_at', date('Y-m-d H:i:s'))
            ->update();
    }

    /**
     * Cria uma notificação
     * 
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @param string|null $link
     * @return int|false
     */
    public function createNotification(
        int $userId,
        string $type,
        string $title,
        string $message = '',
        array $data = [],
        ?string $link = null
    ) {
        $typeConfig = Notification::$typeConfig[$type] ?? Notification::$typeConfig['system'];

        return $this->insert([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'icon'    => $typeConfig['icon'],
            'color'   => $typeConfig['color'],
            'link'    => $link,
            'data'    => !empty($data) ? json_encode($data) : null,
        ]);
    }

    /**
     * Deleta notificações antigas (mais de X dias)
     * 
     * @param int $days
     * @return int Número de registros deletados
     */
    public function deleteOldNotifications(int $days = 30): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->where('created_at <', $date)
            ->delete();
    }

    /**
     * Retorna notificações por tipo
     * 
     * @param int $userId
     * @param string $type
     * @param int $limit
     * @return array
     */
    public function getByType(int $userId, string $type, int $limit = 20): array
    {
        return $this->where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
