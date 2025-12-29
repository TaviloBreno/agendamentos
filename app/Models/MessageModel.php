<?php

namespace App\Models;

use App\Entities\Message;
use CodeIgniter\Model;

/**
 * MessageModel - Model para operações com Mensagens
 * 
 * @package    App\Models
 */
class MessageModel extends Model
{
    protected $table            = 'messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Message::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'conversation_id',
        'sender_id',
        'message',
        'attachment',
        'attachment_type',
        'is_system',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'conversation_id' => 'required|integer',
        'message'         => 'permit_empty',
    ];

    // =========================================================================
    // QUERIES CUSTOMIZADAS
    // =========================================================================

    /**
     * Retorna mensagens de uma conversa
     * 
     * @param int $conversationId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getByConversation(int $conversationId, int $limit = 50, int $offset = 0): array
    {
        return $this->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Retorna mensagens de uma conversa em ordem cronológica
     * 
     * @param int $conversationId
     * @param int $limit
     * @param int $beforeId Para paginação (mensagens antes de um ID)
     * @return array
     */
    public function getMessages(int $conversationId, int $limit = 50, ?int $beforeId = null): array
    {
        $builder = $this->where('conversation_id', $conversationId);

        if ($beforeId) {
            $builder->where('id <', $beforeId);
        }

        $messages = $builder->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();

        // Retorna em ordem cronológica (mais antiga primeiro)
        return array_reverse($messages);
    }

    /**
     * Envia uma mensagem
     * 
     * @param int $conversationId
     * @param int $senderId
     * @param string $message
     * @param string|null $attachment
     * @param string|null $attachmentType
     * @return Message|false
     */
    public function sendMessage(
        int $conversationId,
        int $senderId,
        string $message,
        ?string $attachment = null,
        ?string $attachmentType = null
    ) {
        $data = [
            'conversation_id' => $conversationId,
            'sender_id'       => $senderId,
            'message'         => $message,
            'is_system'       => false,
        ];

        if ($attachment) {
            $data['attachment'] = $attachment;
            $data['attachment_type'] = $attachmentType;
        }

        $messageId = $this->insert($data);

        if ($messageId) {
            // Atualiza o updated_at da conversa
            model('ConversationModel')->touch($conversationId);

            return $this->find($messageId);
        }

        return false;
    }

    /**
     * Envia uma mensagem de sistema
     * 
     * @param int $conversationId
     * @param string $message
     * @return Message|false
     */
    public function sendSystemMessage(int $conversationId, string $message)
    {
        $messageId = $this->insert([
            'conversation_id' => $conversationId,
            'sender_id'       => null,
            'message'         => $message,
            'is_system'       => true,
        ]);

        if ($messageId) {
            model('ConversationModel')->touch($conversationId);
            return $this->find($messageId);
        }

        return false;
    }

    /**
     * Conta mensagens não lidas após uma data
     * 
     * @param int $conversationId
     * @param int $userId
     * @param string|null $lastReadAt
     * @return int
     */
    public function countUnreadAfter(int $conversationId, int $userId, ?string $lastReadAt): int
    {
        $builder = $this->where('conversation_id', $conversationId)
            ->where('sender_id !=', $userId);

        if ($lastReadAt) {
            $builder->where('created_at >', $lastReadAt);
        }

        return $builder->countAllResults();
    }

    /**
     * Retorna a última mensagem de uma conversa
     * 
     * @param int $conversationId
     * @return Message|null
     */
    public function getLastMessage(int $conversationId): ?Message
    {
        return $this->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Busca mensagens por texto
     * 
     * @param int $userId ID do usuário (para buscar apenas em suas conversas)
     * @param string $search Texto a buscar
     * @param int $limit
     * @return array
     */
    public function search(int $userId, string $search, int $limit = 20): array
    {
        // Busca os IDs das conversas do usuário
        $conversations = model('ConversationModel')->getByUser($userId);
        $conversationIds = array_column($conversations, 'id');

        if (empty($conversationIds)) {
            return [];
        }

        return $this->whereIn('conversation_id', $conversationIds)
            ->like('message', $search)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
