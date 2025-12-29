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
    ];

    /**
     * Campos que devem ser convertidos para tipos específicos
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'id'         => 'integer',
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
}
