<?php

namespace App\Entities;

/**
 * Service Entity - Representa um serviço do sistema
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Entity para gerenciamento de serviços oferecidos pelas unidades.
 * Extende MyBaseEntity para herdar métodos de status (active).
 * 
 * FUNCIONALIDADES:
 * - Formatação de preço para exibição
 * - Formatação de duração
 * - Métodos auxiliares para exibição
 * 
 * @package    App\Entities
 * @author     Sistema de Agendamentos
 */
class Service extends MyBaseEntity
{
    /**
     * Campos que devem ser convertidos para tipos específicos
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'id'         => 'integer',
        'duration'   => 'integer',
        'price'      => 'float',
        'active'     => 'int-bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Campos de data que devem ser mutados
     * 
     * @var array<int, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // =========================================================================
    // MÉTODOS DE FORMATAÇÃO
    // =========================================================================

    /**
     * Retorna o preço formatado em Real (R$)
     * 
     * @return string Ex: "R$ 50,00"
     */
    public function priceFormatted(): string
    {
        return 'R$ ' . number_format($this->attributes['price'] ?? 0, 2, ',', '.');
    }

    /**
     * Retorna a duração formatada
     * 
     * @return string Ex: "30 min" ou "1h 30min"
     */
    public function durationFormatted(): string
    {
        $minutes = $this->attributes['duration'] ?? 0;
        
        if ($minutes < 60) {
            return "{$minutes} min";
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes === 0) {
            return "{$hours}h";
        }
        
        return "{$hours}h {$remainingMinutes}min";
    }

    /**
     * Retorna descrição truncada
     * 
     * @param int $length Tamanho máximo
     * @return string
     */
    public function shortDescription(int $length = 100): string
    {
        $description = $this->attributes['description'] ?? '';
        
        if (mb_strlen($description) <= $length) {
            return $description;
        }
        
        return mb_substr($description, 0, $length) . '...';
    }

    /**
     * Verifica se o serviço tem descrição
     * 
     * @return bool
     */
    public function hasDescription(): bool
    {
        return ! empty($this->attributes['description']);
    }

    /**
     * Retorna badge com informações do serviço
     * 
     * @return string HTML do badge
     */
    public function infoBadge(): string
    {
        return sprintf(
            '<span class="badge badge-info">%s</span> <span class="badge badge-success">%s</span>',
            esc($this->durationFormatted()),
            esc($this->priceFormatted())
        );
    }
}
