<?php

namespace App\Entities;

/**
 * Entity Conversation
 * 
 * Representa uma conversa entre usuários
 */
class Conversation extends MyBaseEntity
{
    protected $casts = [
        'id'         => 'integer',
        'created_by' => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Retorna os participantes da conversa
     */
    public function getParticipants(): array
    {
        return model('ConversationParticipantModel')
            ->where('conversation_id', $this->id)
            ->whereNull('left_at')
            ->findAll();
    }

    /**
     * Retorna os usuários da conversa
     */
    public function getUsers(): array
    {
        $participants = $this->getParticipants();
        $userIds = array_column($participants, 'user_id');

        if (empty($userIds)) {
            return [];
        }

        return model('UserModel')->whereIn('id', $userIds)->findAll();
    }

    /**
     * Retorna a última mensagem
     */
    public function getLastMessage(): ?Message
    {
        return model('MessageModel')
            ->where('conversation_id', $this->id)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Conta mensagens não lidas para um usuário
     */
    public function unreadCount(int $userId): int
    {
        $participant = model('ConversationParticipantModel')
            ->where('conversation_id', $this->id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant || !$participant->last_read_at) {
            return model('MessageModel')
                ->where('conversation_id', $this->id)
                ->where('sender_id !=', $userId)
                ->countAllResults();
        }

        return model('MessageModel')
            ->where('conversation_id', $this->id)
            ->where('sender_id !=', $userId)
            ->where('created_at >', $participant->last_read_at)
            ->countAllResults();
    }

    /**
     * Retorna o título da conversa para um usuário
     */
    public function getTitleFor(int $userId): string
    {
        if ($this->subject) {
            return $this->subject;
        }

        // Para conversas diretas, mostra o nome do outro participante
        if ($this->type === 'direct') {
            $users = $this->getUsers();
            foreach ($users as $user) {
                if ($user->id !== $userId) {
                    return $user->name;
                }
            }
        }

        return 'Conversa #' . $this->id;
    }

    /**
     * Verifica se o usuário é participante
     */
    public function hasParticipant(int $userId): bool
    {
        return model('ConversationParticipantModel')
            ->where('conversation_id', $this->id)
            ->where('user_id', $userId)
            ->whereNull('left_at')
            ->countAllResults() > 0;
    }
}
