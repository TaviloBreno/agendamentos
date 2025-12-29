<?php

namespace App\Entities;

/**
 * Entity Tenant
 * 
 * Representa uma empresa/tenant no sistema multi-tenant.
 * 
 * @property int         $id
 * @property string      $name
 * @property string      $slug
 * @property string|null $domain
 * @property string      $email
 * @property string|null $phone
 * @property string|null $document
 * @property string|null $logo
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property array|null  $settings
 * @property string      $plan
 * @property string|null $plan_expires_at
 * @property int         $max_users
 * @property int         $max_units
 * @property int         $max_appointments_month
 * @property bool        $whatsapp_enabled
 * @property bool        $payments_enabled
 * @property string      $status
 * @property string|null $trial_ends_at
 * @property \CodeIgniter\I18n\Time|null $created_at
 * @property \CodeIgniter\I18n\Time|null $updated_at
 * @property \CodeIgniter\I18n\Time|null $deleted_at
 */
class Tenant extends MyBaseEntity
{
    protected $casts = [
        'id'                      => 'integer',
        'max_users'               => 'integer',
        'max_units'               => 'integer',
        'max_appointments_month'  => 'integer',
        'whatsapp_enabled'        => 'boolean',
        'payments_enabled'        => 'boolean',
        'settings'                => 'json-array',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'plan_expires_at', 'trial_ends_at'];

    /**
     * Labels dos planos
     */
    protected static array $planLabels = [
        'free'         => 'Gratuito',
        'basic'        => 'Básico',
        'professional' => 'Profissional',
        'enterprise'   => 'Empresarial',
    ];

    /**
     * Labels dos status
     */
    protected static array $statusLabels = [
        'active'    => 'Ativo',
        'inactive'  => 'Inativo',
        'suspended' => 'Suspenso',
        'trial'     => 'Trial',
    ];

    /**
     * Classes CSS dos status
     */
    protected static array $statusClasses = [
        'active'    => 'success',
        'inactive'  => 'secondary',
        'suspended' => 'danger',
        'trial'     => 'info',
    ];

    /**
     * Limites por plano
     */
    protected static array $planLimits = [
        'free' => [
            'users' => 2,
            'units' => 1,
            'appointments_month' => 50,
            'whatsapp' => false,
            'payments' => false,
        ],
        'basic' => [
            'users' => 5,
            'units' => 2,
            'appointments_month' => 200,
            'whatsapp' => true,
            'payments' => false,
        ],
        'professional' => [
            'users' => 15,
            'units' => 5,
            'appointments_month' => 1000,
            'whatsapp' => true,
            'payments' => true,
        ],
        'enterprise' => [
            'users' => -1, // ilimitado
            'units' => -1,
            'appointments_month' => -1,
            'whatsapp' => true,
            'payments' => true,
        ],
    ];

    // =========================================================================
    // MÉTODOS DE FORMATAÇÃO
    // =========================================================================

    /**
     * Retorna URL do logo ou placeholder
     */
    public function logoUrl(int $size = 100): string
    {
        if (!empty($this->logo) && file_exists(FCPATH . $this->logo)) {
            return base_url($this->logo);
        }

        // Placeholder com iniciais
        $initials = $this->getInitials();
        return "https://ui-avatars.com/api/?name={$initials}&size={$size}&background=4e73df&color=fff&bold=true";
    }

    /**
     * Retorna as iniciais do nome
     */
    public function getInitials(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        
        return $initials;
    }

    /**
     * Retorna label do plano
     */
    public function planLabel(): string
    {
        return self::$planLabels[$this->plan] ?? $this->plan;
    }

    /**
     * Retorna label do status
     */
    public function statusLabel(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Retorna badge HTML do status
     */
    public function statusBadge(): string
    {
        $class = self::$statusClasses[$this->status] ?? 'secondary';
        $label = $this->statusLabel();
        return "<span class=\"badge badge-{$class}\">{$label}</span>";
    }

    /**
     * Retorna badge HTML do plano
     */
    public function planBadge(): string
    {
        $colors = [
            'free'         => 'secondary',
            'basic'        => 'primary',
            'professional' => 'success',
            'enterprise'   => 'warning',
        ];
        
        $class = $colors[$this->plan] ?? 'secondary';
        $label = $this->planLabel();
        return "<span class=\"badge badge-{$class}\">{$label}</span>";
    }

    /**
     * Retorna documento formatado (CNPJ/CPF)
     */
    public function documentFormatted(): string
    {
        if (empty($this->document)) {
            return '-';
        }

        $doc = preg_replace('/[^0-9]/', '', $this->document);
        
        if (strlen($doc) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        } elseif (strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        }
        
        return $this->document;
    }

    /**
     * Retorna endereço completo
     */
    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
        ]);

        return implode(', ', $parts) ?: '-';
    }

    // =========================================================================
    // VERIFICAÇÕES DE STATUS
    // =========================================================================

    /**
     * Verifica se está ativo
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verifica se está em trial
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    /**
     * Verifica se o trial expirou
     */
    public function isTrialExpired(): bool
    {
        if (!$this->isTrial() || empty($this->trial_ends_at)) {
            return false;
        }

        return strtotime($this->trial_ends_at) < time();
    }

    /**
     * Verifica se o plano expirou
     */
    public function isPlanExpired(): bool
    {
        if (empty($this->plan_expires_at)) {
            return false;
        }

        return strtotime($this->plan_expires_at) < time();
    }

    /**
     * Dias restantes do trial
     */
    public function trialDaysRemaining(): int
    {
        if (empty($this->trial_ends_at)) {
            return 0;
        }

        $diff = strtotime($this->trial_ends_at) - time();
        return max(0, ceil($diff / 86400));
    }

    // =========================================================================
    // VERIFICAÇÕES DE LIMITES
    // =========================================================================

    /**
     * Retorna limites do plano atual
     */
    public function getPlanLimits(): array
    {
        return self::$planLimits[$this->plan] ?? self::$planLimits['free'];
    }

    /**
     * Verifica se pode adicionar mais usuários
     */
    public function canAddUser(int $currentCount): bool
    {
        $limit = $this->max_users;
        return $limit === -1 || $currentCount < $limit;
    }

    /**
     * Verifica se pode adicionar mais unidades
     */
    public function canAddUnit(int $currentCount): bool
    {
        $limit = $this->max_units;
        return $limit === -1 || $currentCount < $limit;
    }

    /**
     * Verifica se pode criar mais agendamentos no mês
     */
    public function canCreateAppointment(int $currentMonthCount): bool
    {
        $limit = $this->max_appointments_month;
        return $limit === -1 || $currentMonthCount < $limit;
    }

    /**
     * Verifica se tem WhatsApp habilitado
     */
    public function hasWhatsApp(): bool
    {
        return $this->whatsapp_enabled && self::$planLimits[$this->plan]['whatsapp'];
    }

    /**
     * Verifica se tem pagamentos habilitados
     */
    public function hasPayments(): bool
    {
        return $this->payments_enabled && self::$planLimits[$this->plan]['payments'];
    }

    // =========================================================================
    // CONFIGURAÇÕES
    // =========================================================================

    /**
     * Retorna uma configuração específica
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    /**
     * Define uma configuração
     */
    public function setSetting(string $key, $value): self
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        return $this;
    }

    /**
     * Retorna URL pública do tenant
     */
    public function getPublicUrl(): string
    {
        if (!empty($this->domain)) {
            return "https://{$this->domain}";
        }
        
        return base_url("t/{$this->slug}");
    }

    // =========================================================================
    // MÉTODOS ESTÁTICOS
    // =========================================================================

    /**
     * Retorna opções de planos para dropdown
     */
    public static function getPlanOptions(): array
    {
        return self::$planLabels;
    }

    /**
     * Retorna opções de status para dropdown
     */
    public static function getStatusOptions(): array
    {
        return self::$statusLabels;
    }
}
