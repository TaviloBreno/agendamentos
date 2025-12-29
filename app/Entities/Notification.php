<?php

namespace App\Entities;

/**
 * Entity Notification
 * 
 * Representa uma notificação do sistema
 */
class Notification extends MyBaseEntity
{
    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
        'data'    => 'json-array',
    ];

    protected $dates = ['created_at', 'updated_at', 'read_at'];

    /**
     * Tipos de notificação e seus ícones
     */
    protected static array $typeConfig = [
        'appointment' => ['icon' => 'fa-calendar-check', 'color' => 'primary'],
        'message'     => ['icon' => 'fa-envelope', 'color' => 'info'],
        'system'      => ['icon' => 'fa-cog', 'color' => 'secondary'],
        'payment'     => ['icon' => 'fa-credit-card', 'color' => 'success'],
        'reminder'    => ['icon' => 'fa-bell', 'color' => 'warning'],
        'alert'       => ['icon' => 'fa-exclamation-triangle', 'color' => 'danger'],
        'success'     => ['icon' => 'fa-check-circle', 'color' => 'success'],
    ];

    /**
     * Verifica se a notificação foi lida
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Retorna o ícone da notificação
     */
    public function getIcon(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        return self::$typeConfig[$this->type]['icon'] ?? 'fa-bell';
    }

    /**
     * Retorna a cor da notificação
     */
    public function getColor(): string
    {
        if ($this->color) {
            return $this->color;
        }

        return self::$typeConfig[$this->type]['color'] ?? 'primary';
    }

    /**
     * Retorna o tempo relativo (ex: "há 5 minutos")
     */
    public function timeAgo(): string
    {
        if (!$this->created_at) {
            return '';
        }

        $time = $this->created_at;
        if (is_string($time)) {
            $time = new \DateTime($time);
        }

        $now = new \DateTime();
        $diff = $now->diff($time);

        if ($diff->y > 0) {
            return $diff->y . ' ano' . ($diff->y > 1 ? 's' : '') . ' atrás';
        }
        if ($diff->m > 0) {
            return $diff->m . ' ' . ($diff->m > 1 ? 'meses' : 'mês') . ' atrás';
        }
        if ($diff->d > 0) {
            return $diff->d . ' dia' . ($diff->d > 1 ? 's' : '') . ' atrás';
        }
        if ($diff->h > 0) {
            return $diff->h . 'h atrás';
        }
        if ($diff->i > 0) {
            return $diff->i . ' min atrás';
        }

        return 'Agora';
    }

    /**
     * Retorna o HTML do ícone
     */
    public function iconHtml(): string
    {
        $color = $this->getColor();
        $icon = $this->getIcon();

        return sprintf(
            '<div class="icon-circle bg-%s"><i class="fas %s text-white"></i></div>',
            $color,
            $icon
        );
    }
}
