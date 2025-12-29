<?php

namespace App\Entities;

/**
 * User Entity - Representa um usuário do sistema
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Entity para gerenciamento de usuários com autenticação.
 * Extende MyBaseEntity para herdar métodos de status (active).
 * 
 * FUNCIONALIDADES:
 * - Hash automático de senha no setter
 * - Verificação de senha
 * - Verificação de papéis (roles)
 * - Métodos auxiliares para exibição
 * 
 * @package    App\Entities
 * @author     Sistema de Agendamentos
 */
class User extends MyBaseEntity
{
    /**
     * Campos permitidos para mass assignment
     * 
     * @var array<int, string>
     */
    protected $allowedFields = [
        'name',
        'email', 
        'password',
        'role',
        'active',
        'avatar',
        'phone',
        'bio',
        'settings',
        'last_login_at',
        'last_activity_at',
    ];

    /**
     * Campos que devem ser convertidos para tipos específicos
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'id'         => 'integer',
        'active'     => 'int-bool',
        'settings'   => 'json-array',
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
        'last_login_at',
        'last_activity_at',
    ];

    /**
     * Configurações padrão do usuário
     */
    public static array $defaultSettings = [
        'email_notifications'     => true,
        'push_notifications'      => true,
        'appointment_reminders'   => true,
        'message_notifications'   => true,
        'language'                => 'pt-BR',
        'theme'                   => 'light',
        'sidebar_collapsed'       => false,
    ];

    // =========================================================================
    // SETTERS CUSTOMIZADOS
    // =========================================================================

    /**
     * Setter para password - Aplica hash automaticamente
     * 
     * =========================================================================
     * HASH DE SENHA
     * =========================================================================
     * 
     * Sempre que a senha for definida, ela será automaticamente hasheada
     * usando PASSWORD_DEFAULT (atualmente bcrypt).
     * 
     * IMPORTANTE:
     * - Nunca armazene senhas em texto puro
     * - PASSWORD_DEFAULT garante uso do algoritmo mais seguro disponível
     * - O hash inclui salt automaticamente
     * 
     * @param string $password Senha em texto puro
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->attributes['password'] = password_hash(
            $password, 
            PASSWORD_DEFAULT
        );
        
        return $this;
    }

    // =========================================================================
    // MÉTODOS DE AUTENTICAÇÃO
    // =========================================================================

    /**
     * Verifica se a senha informada confere com o hash armazenado
     * 
     * @param string $password Senha em texto puro para verificar
     * @return bool True se a senha confere
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password'] ?? '');
    }

    // =========================================================================
    // MÉTODOS DE AUTORIZAÇÃO (ROLES)
    // =========================================================================

    /**
     * Verifica se o usuário possui determinado papel
     * 
     * @param string $role Papel a verificar (super, admin, user)
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->attributes['role'] === $role;
    }

    /**
     * Verifica se é Super Admin
     * 
     * @return bool
     */
    public function isSuper(): bool
    {
        return $this->hasRole('super');
    }

    /**
     * Verifica se é Admin (ou superior)
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->attributes['role'], ['super', 'admin'], true);
    }

    /**
     * Retorna o nome do papel formatado para exibição
     * 
     * @return string
     */
    public function roleName(): string
    {
        $roles = [
            'super' => 'Super Admin',
            'admin' => 'Administrador',
            'user'  => 'Usuário',
        ];

        return $roles[$this->attributes['role']] ?? 'Desconhecido';
    }

    /**
     * Retorna badge HTML com o papel do usuário
     * 
     * @return string HTML do badge Bootstrap
     */
    public function roleBadge(): string
    {
        $badges = [
            'super' => '<span class="badge badge-danger">Super Admin</span>',
            'admin' => '<span class="badge badge-warning">Admin</span>',
            'user'  => '<span class="badge badge-info">Usuário</span>',
        ];

        return $badges[$this->attributes['role']] ?? '<span class="badge badge-secondary">N/A</span>';
    }

    // =========================================================================
    // MÉTODOS AUXILIARES
    // =========================================================================

    /**
     * Retorna as iniciais do nome para avatar
     * 
     * @return string Até 2 letras maiúsculas
     */
    public function initials(): string
    {
        $names = explode(' ', $this->attributes['name'] ?? '');
        $initials = '';
        
        foreach ($names as $name) {
            $initials .= mb_substr($name, 0, 1);
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }
        
        return mb_strtoupper($initials);
    }

    /**
     * Retorna o primeiro nome do usuário
     * 
     * @return string
     */
    public function firstName(): string
    {
        $names = explode(' ', $this->attributes['name'] ?? '');
        return $names[0] ?? '';
    }

    /**
     * Retorna URL do avatar usando ui-avatars.com
     * 
     * @param int $size Tamanho do avatar em pixels
     * @return string
     */
    public function avatarUrl(int $size = 64): string
    {
        // Se tem avatar customizado, usar ele
        if (!empty($this->attributes['avatar'])) {
            return base_url('uploads/avatars/' . $this->attributes['avatar']);
        }

        // Fallback para avatar padrão ou ui-avatars
        $defaultAvatar = FCPATH . 'back/img/default-avatar.svg';
        if (file_exists($defaultAvatar)) {
            return base_url('back/img/default-avatar.svg');
        }

        // Último fallback: ui-avatars.com
        $name = urlencode($this->attributes['name'] ?? 'User');
        $colors = [
            'super' => '4e73df',
            'admin' => 'f6c23e',
            'user'  => '1cc88a',
        ];
        $bg = $colors[$this->attributes['role'] ?? 'user'] ?? '858796';
        return "https://ui-avatars.com/api/?name={$name}&size={$size}&background={$bg}&color=fff&bold=true";
    }

    /**
     * Verifica se o usuário tem avatar customizado
     * 
     * @return bool
     */
    public function hasCustomAvatar(): bool
    {
        return !empty($this->attributes['avatar']);
    }

    /**
     * Retorna uma configuração específica do usuário
     * 
     * @param string $key Chave da configuração
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->attributes['settings'] ?? [];

        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?? [];
        }

        return $settings[$key] ?? self::$defaultSettings[$key] ?? $default;
    }

    /**
     * Retorna todas as configurações do usuário com defaults
     * 
     * @return array
     */
    public function getAllSettings(): array
    {
        $settings = $this->attributes['settings'] ?? [];

        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?? [];
        }

        return array_merge(self::$defaultSettings, $settings);
    }

    /**
     * Define uma configuração específica
     * 
     * @param string $key Chave da configuração
     * @param mixed $value Valor a definir
     * @return $this
     */
    public function setSetting(string $key, $value): self
    {
        $settings = $this->getAllSettings();
        $settings[$key] = $value;
        $this->attributes['settings'] = $settings;

        return $this;
    }

    /**
     * Atualiza o último login
     * 
     * @return $this
     */
    public function updateLastLogin(): self
    {
        $this->attributes['last_login_at'] = date('Y-m-d H:i:s');
        return $this;
    }

    /**
     * Atualiza a última atividade
     * 
     * @return $this
     */
    public function updateLastActivity(): self
    {
        $this->attributes['last_activity_at'] = date('Y-m-d H:i:s');
        return $this;
    }

    /**
     * Retorna contagem de notificações não lidas
     * 
     * @return int
     */
    public function unreadNotificationsCount(): int
    {
        return model('NotificationModel')
            ->where('user_id', $this->id)
            ->whereNull('read_at')
            ->countAllResults();
    }

    /**
     * Retorna contagem de mensagens não lidas
     * 
     * @return int
     */
    public function unreadMessagesCount(): int
    {
        $total = 0;
        $conversations = model('ConversationModel')->getByUser($this->id);

        foreach ($conversations as $conversation) {
            $total += $conversation->unreadCount($this->id);
        }

        return $total;
    }

    /**
     * Retorna badge de status com estilo
     * 
     * @return string HTML do badge
     */
    public function statusBadge(): string
    {
        if ($this->isActive()) {
            return '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Ativo</span>';
        }
        
        return '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Inativo</span>';
    }
}
