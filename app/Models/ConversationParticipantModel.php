<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ConversationParticipantModel - Model para participantes de conversas
 * 
 * @package    App\Models
 */
class ConversationParticipantModel extends Model
{
    protected $table            = 'conversation_participants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'conversation_id',
        'user_id',
        'last_read_at',
        'is_muted',
        'joined_at',
        'left_at',
    ];

    protected $useTimestamps = false;

    // =========================================================================
    // QUERIES CUSTOMIZADAS
    // =========================================================================

    /**
     * Adiciona um participante à conversa
     * 
     * @param int $conversationId
     * @param int $userId
     * @return int|false
     */
    public function addParticipant(int $conversationId, int $userId)
    {
        // Verifica se já é participante (mesmo que tenha saído)
        $existing = $this->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            // Se já existe e saiu, reativa
            if ($existing->left_at) {
                return $this->update($existing->id, [
                    'left_at'   => null,
                    'joined_at' => date('Y-m-d H:i:s'),
                ]);
            }
            return $existing->id;
        }

        return $this->insert([
            'conversation_id' => $conversationId,
            'user_id'         => $userId,
            'joined_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Remove um participante da conversa (soft)
     * 
     * @param int $conversationId
     * @param int $userId
     * @return bool
     */
    public function removeParticipant(int $conversationId, int $userId): bool
    {
        return $this->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->set('left_at', date('Y-m-d H:i:s'))
            ->update();
    }

    /**
     * Marca as mensagens como lidas para um participante
     * 
     * @param int $conversationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $conversationId, int $userId): bool
    {
        return $this->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->set('last_read_at', date('Y-m-d H:i:s'))
            ->update();
    }

    /**
     * Muta ou desmuta notificações para um participante
     * 
     * @param int $conversationId
     * @param int $userId
     * @param bool $muted
     * @return bool
     */
    public function setMuted(int $conversationId, int $userId, bool $muted): bool
    {
        return $this->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->set('is_muted', $muted ? 1 : 0)
            ->update();
    }

    /**
     * Retorna os participantes ativos de uma conversa
     * 
     * @param int $conversationId
     * @return array
     */
    public function getActiveParticipants(int $conversationId): array
    {
        return $this->where('conversation_id', $conversationId)
            ->where('left_at', null)
            ->findAll();
    }

    /**
     * Verifica se um usuário é participante ativo
     * 
     * @param int $conversationId
     * @param int $userId
     * @return bool
     */
    public function isParticipant(int $conversationId, int $userId): bool
    {
        return $this->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->where('left_at', null)
            ->countAllResults() > 0;
    }

    /**
     * Retorna o participante específico
     * 
     * @param int $conversationId
     * @param int $userId
     * @return object|null
     */
    public function getParticipant(int $conversationId, int $userId): ?object
    {
        return $this->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();
    }
}
