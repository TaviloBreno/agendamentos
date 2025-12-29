<?php

namespace App\Entities;

/**
 * Entity Message
 * 
 * Representa uma mensagem em uma conversa
 */
class Message extends MyBaseEntity
{
    protected $casts = [
        'id'              => 'integer',
        'conversation_id' => 'integer',
        'sender_id'       => 'integer',
        'is_system'       => 'boolean',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Tipos de anexo permitidos
     */
    public static array $allowedAttachmentTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
        'audio' => ['mp3', 'wav', 'ogg'],
    ];

    /**
     * Retorna o remetente da mensagem
     */
    public function getSender(): ?User
    {
        if (!$this->sender_id) {
            return null;
        }

        return model('UserModel')->find($this->sender_id);
    }

    /**
     * Retorna a conversa
     */
    public function getConversation(): ?Conversation
    {
        return model('ConversationModel')->find($this->conversation_id);
    }

    /**
     * Verifica se tem anexo
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment);
    }

    /**
     * Retorna a URL do anexo
     */
    public function getAttachmentUrl(): ?string
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        return base_url('uploads/messages/' . $this->attachment);
    }

    /**
     * Verifica se o anexo é uma imagem
     */
    public function isImageAttachment(): bool
    {
        if (!$this->hasAttachment() || !$this->attachment_type) {
            return false;
        }

        return $this->attachment_type === 'image';
    }

    /**
     * Retorna o ícone do tipo de anexo
     */
    public function getAttachmentIcon(): string
    {
        $icons = [
            'image' => 'fa-image',
            'document' => 'fa-file-alt',
            'audio' => 'fa-volume-up',
        ];

        return $icons[$this->attachment_type] ?? 'fa-paperclip';
    }

    /**
     * Formata o tempo da mensagem
     */
    public function timeAgo(): string
    {
        $timestamp = strtotime($this->created_at);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Agora';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . 'min';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . 'h';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . 'd';
        } else {
            return date('d/m/Y', $timestamp);
        }
    }

    /**
     * Retorna a mensagem formatada (com links clicáveis, etc)
     */
    public function getFormattedMessage(): string
    {
        $message = esc($this->message);

        // Converter URLs em links clicáveis
        $message = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener">$1</a>',
            $message
        );

        // Converter quebras de linha
        $message = nl2br($message);

        return $message;
    }

    /**
     * Preview curto da mensagem
     */
    public function preview(int $length = 50): string
    {
        if ($this->is_system) {
            return '📢 ' . $this->message;
        }

        if ($this->hasAttachment()) {
            $attachmentLabels = [
                'image' => '📷 Imagem',
                'document' => '📄 Documento',
                'audio' => '🎵 Áudio',
            ];
            $label = $attachmentLabels[$this->attachment_type] ?? '📎 Anexo';

            if ($this->message) {
                return $label . ': ' . character_limiter($this->message, $length);
            }
            return $label;
        }

        return character_limiter($this->message, $length);
    }
}
