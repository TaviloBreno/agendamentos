<?php

namespace App\Models;

use App\Entities\Conversation;
use CodeIgniter\Model;

/**
 * ConversationModel - Model para operações com Conversas
 * 
 * @package    App\Models
 */
class ConversationModel extends Model
{
    protected $table            = 'conversations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Conversation::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'subject',
        'type',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'type'       => 'required|in_list[direct,group]',
        'created_by' => 'required|integer',
    ];

    // =========================================================================
    // QUERIES CUSTOMIZADAS
    // =========================================================================

    /**
     * Retorna as conversas de um usuário
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getByUser(int $userId, int $limit = 50): array
    {
        $builder = $this->db->table('conversations c')
            ->select('c.*')
            ->join('conversation_participants cp', 'cp.conversation_id = c.id')
            ->where('cp.user_id', $userId)
            ->whereNull('cp.left_at')
            ->orderBy('c.updated_at', 'DESC')
            ->limit($limit);

        $results = $builder->get()->getResultArray();
        $conversations = [];

        foreach ($results as $row) {
            $conversations[] = new Conversation($row);
        }

        return $conversations;
    }

    /**
     * Encontra ou cria uma conversa direta entre dois usuários
     * 
     * @param int $userId1
     * @param int $userId2
     * @return Conversation
     */
    public function findOrCreateDirect(int $userId1, int $userId2): Conversation
    {
        // Busca conversa direta existente entre os dois usuários
        $builder = $this->db->table('conversations c')
            ->select('c.*')
            ->join('conversation_participants cp1', 'cp1.conversation_id = c.id')
            ->join('conversation_participants cp2', 'cp2.conversation_id = c.id')
            ->where('c.type', 'direct')
            ->where('cp1.user_id', $userId1)
            ->where('cp2.user_id', $userId2)
            ->whereNull('cp1.left_at')
            ->whereNull('cp2.left_at');

        $existing = $builder->get()->getRowArray();

        if ($existing) {
            return new Conversation($existing);
        }

        // Cria nova conversa direta
        $this->db->transStart();

        $conversationId = $this->insert([
            'type'       => 'direct',
            'created_by' => $userId1,
        ]);

        // Adiciona participantes
        $participantModel = model('ConversationParticipantModel');
        $participantModel->addParticipant($conversationId, $userId1);
        $participantModel->addParticipant($conversationId, $userId2);

        $this->db->transComplete();

        return $this->find($conversationId);
    }

    /**
     * Cria uma conversa em grupo
     * 
     * @param int $createdBy
     * @param string $subject
     * @param array $participantIds
     * @return Conversation
     */
    public function createGroup(int $createdBy, string $subject, array $participantIds): Conversation
    {
        $this->db->transStart();

        $conversationId = $this->insert([
            'subject'    => $subject,
            'type'       => 'group',
            'created_by' => $createdBy,
        ]);

        // Adiciona o criador como participante
        $participantModel = model('ConversationParticipantModel');
        $participantModel->addParticipant($conversationId, $createdBy);

        // Adiciona os outros participantes
        foreach ($participantIds as $userId) {
            if ($userId !== $createdBy) {
                $participantModel->addParticipant($conversationId, $userId);
            }
        }

        $this->db->transComplete();

        return $this->find($conversationId);
    }

    /**
     * Atualiza o updated_at da conversa (quando nova mensagem é enviada)
     * 
     * @param int $conversationId
     * @return bool
     */
    public function touch(int $conversationId): bool
    {
        return $this->update($conversationId, ['updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Conta conversas não lidas para um usuário
     * 
     * @param int $userId
     * @return int
     */
    public function countUnread(int $userId): int
    {
        $conversations = $this->getByUser($userId);
        $count = 0;

        foreach ($conversations as $conversation) {
            if ($conversation->unreadCount($userId) > 0) {
                $count++;
            }
        }

        return $count;
    }
}
