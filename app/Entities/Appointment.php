<?php

namespace App\Entities;

/**
 * Entity Appointment
 * 
 * Representa um agendamento do sistema.
 * 
 * =========================================================================
 * PROPRIEDADES (Colunas da Tabela)
 * =========================================================================
 * 
 * @property int         $id
 * @property int         $unit_id
 * @property int         $professional_id
 * @property int         $service_id
 * @property string      $client_name
 * @property string      $client_email
 * @property string|null $client_phone
 * @property string      $date
 * @property string      $start_time
 * @property string      $end_time
 * @property string      $status (scheduled, confirmed, completed, cancelled, no_show)
 * @property string|null $notes
 * @property \CodeIgniter\I18n\Time|null $created_at
 * @property \CodeIgniter\I18n\Time|null $updated_at
 * 
 * @package    App\Entities
 */
class Appointment extends MyBaseEntity
{
    /**
     * Tipos de cast automático
     */
    protected $casts = [
        'id'              => 'integer',
        'unit_id'         => 'integer',
        'professional_id' => 'integer',
        'service_id'      => 'integer',
    ];

    /**
     * Datas que devem ser convertidas para Time
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Mapeamento de status para labels em português
     */
    protected static array $statusLabels = [
        'scheduled'  => 'Agendado',
        'confirmed'  => 'Confirmado',
        'completed'  => 'Concluído',
        'cancelled'  => 'Cancelado',
        'no_show'    => 'Não Compareceu',
    ];

    /**
     * Mapeamento de status para classes CSS
     */
    protected static array $statusClasses = [
        'scheduled'  => 'warning',
        'confirmed'  => 'info',
        'completed'  => 'success',
        'cancelled'  => 'danger',
        'no_show'    => 'secondary',
    ];

    /**
     * Mapeamento de status para ícones
     */
    protected static array $statusIcons = [
        'scheduled'  => 'fa-clock',
        'confirmed'  => 'fa-check-circle',
        'completed'  => 'fa-check-double',
        'cancelled'  => 'fa-times-circle',
        'no_show'    => 'fa-user-slash',
    ];

    // =========================================================================
    // MÉTODOS DE FORMATAÇÃO
    // =========================================================================

    /**
     * Retorna a data formatada
     * 
     * @param string $format Formato da data
     * @return string
     */
    public function dateFormatted(string $format = 'd/m/Y'): string
    {
        if (empty($this->date)) {
            return '-';
        }
        
        return date($format, strtotime($this->date));
    }

    /**
     * Retorna o horário formatado (início - fim)
     * 
     * @return string Ex: "09:00 - 10:00"
     */
    public function timeRange(): string
    {
        $start = substr($this->start_time ?? '00:00', 0, 5);
        $end = substr($this->end_time ?? '00:00', 0, 5);
        
        return "{$start} - {$end}";
    }

    /**
     * Retorna a duração em minutos
     * 
     * @return int
     */
    public function durationMinutes(): int
    {
        $start = strtotime($this->start_time ?? '00:00');
        $end = strtotime($this->end_time ?? '00:00');
        
        return ($end - $start) / 60;
    }

    /**
     * Retorna a duração formatada
     * 
     * @return string Ex: "1h 30min"
     */
    public function durationFormatted(): string
    {
        $minutes = $this->durationMinutes();
        
        if ($minutes < 60) {
            return "{$minutes}min";
        }
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        return $mins > 0 ? "{$hours}h {$mins}min" : "{$hours}h";
    }

    /**
     * Retorna data e hora completa
     * 
     * @return string Ex: "29/12/2025 às 09:00"
     */
    public function dateTimeFormatted(): string
    {
        return $this->dateFormatted() . ' às ' . substr($this->start_time ?? '00:00', 0, 5);
    }

    /**
     * Retorna nome do cliente com limite
     * 
     * @param int $length
     * @return string
     */
    public function clientNameShort(int $length = 30): string
    {
        if (strlen($this->client_name ?? '') <= $length) {
            return $this->client_name ?? '';
        }
        
        return substr($this->client_name, 0, $length) . '...';
    }

    // =========================================================================
    // MÉTODOS DE STATUS
    // =========================================================================

    /**
     * Retorna o label do status em português
     * 
     * @return string
     */
    public function statusLabel(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Retorna badge HTML do status
     * 
     * @return string HTML do badge
     */
    public function statusBadge(): string
    {
        $class = self::$statusClasses[$this->status] ?? 'secondary';
        $icon = self::$statusIcons[$this->status] ?? 'fa-question';
        $label = $this->statusLabel();
        
        return "<span class=\"badge badge-{$class}\"><i class=\"fas {$icon} mr-1\"></i>{$label}</span>";
    }

    /**
     * Verifica se o agendamento pode ser editado
     * 
     * @return bool
     */
    public function canEdit(): bool
    {
        return in_array($this->status, ['scheduled', 'confirmed']);
    }

    /**
     * Verifica se o agendamento pode ser cancelado
     * 
     * @return bool
     */
    public function canCancel(): bool
    {
        return in_array($this->status, ['scheduled', 'confirmed']);
    }

    /**
     * Verifica se o agendamento pode ser confirmado
     * 
     * @return bool
     */
    public function canConfirm(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Verifica se o agendamento pode ser marcado como concluído
     * 
     * @return bool
     */
    public function canComplete(): bool
    {
        return in_array($this->status, ['scheduled', 'confirmed']);
    }

    /**
     * Verifica se é um agendamento futuro
     * 
     * @return bool
     */
    public function isFuture(): bool
    {
        $appointmentDateTime = strtotime("{$this->date} {$this->start_time}");
        return $appointmentDateTime > time();
    }

    /**
     * Verifica se é hoje
     * 
     * @return bool
     */
    public function isToday(): bool
    {
        return $this->date === date('Y-m-d');
    }

    // =========================================================================
    // MÉTODOS DE RELACIONAMENTO
    // =========================================================================

    /**
     * Retorna a unidade do agendamento
     * 
     * @return \App\Entities\Unit|null
     */
    public function getUnit(): ?\App\Entities\Unit
    {
        return model(\App\Models\UnitModel::class)->find($this->unit_id);
    }

    /**
     * Retorna o profissional do agendamento
     * 
     * @return \App\Entities\Professional|null
     */
    public function getProfessional(): ?\App\Entities\Professional
    {
        return model(\App\Models\ProfessionalModel::class)->find($this->professional_id);
    }

    /**
     * Retorna o serviço do agendamento
     * 
     * @return \App\Entities\Service|null
     */
    public function getService(): ?\App\Entities\Service
    {
        return model(\App\Models\ServiceModel::class)->find($this->service_id);
    }

    // =========================================================================
    // MÉTODOS ESTÁTICOS
    // =========================================================================

    /**
     * Retorna os status disponíveis para dropdown
     * 
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return self::$statusLabels;
    }

    /**
     * Retorna cor para FullCalendar baseada no status
     * 
     * @return string Cor hexadecimal
     */
    public function calendarColor(): string
    {
        $colors = [
            'scheduled'  => '#f6c23e', // warning
            'confirmed'  => '#36b9cc', // info
            'completed'  => '#1cc88a', // success
            'cancelled'  => '#e74a3b', // danger
            'no_show'    => '#858796', // secondary
        ];
        
        return $colors[$this->status] ?? '#858796';
    }

    /**
     * Converte para formato FullCalendar
     * 
     * @return array
     */
    public function toCalendarEvent(): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->client_name,
            'start'           => "{$this->date}T{$this->start_time}",
            'end'             => "{$this->date}T{$this->end_time}",
            'backgroundColor' => $this->calendarColor(),
            'borderColor'     => $this->calendarColor(),
            'extendedProps'   => [
                'status'      => $this->status,
                'client_email'=> $this->client_email,
                'client_phone'=> $this->client_phone,
            ],
        ];
    }
}
