<?php

namespace App\Libraries;

use App\Entities\Tenant;

/**
 * TenantService - Serviço global para gerenciamento do tenant atual
 * 
 * Permite acesso ao tenant atual em qualquer parte da aplicação.
 */
class TenantService
{
    /**
     * Tenant atual
     */
    protected ?Tenant $tenant = null;

    /**
     * Define o tenant atual
     */
    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }

    /**
     * Retorna o tenant atual
     */
    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * Retorna o ID do tenant atual
     */
    public function getId(): ?int
    {
        return $this->tenant?->id;
    }

    /**
     * Retorna o slug do tenant atual
     */
    public function getSlug(): ?string
    {
        return $this->tenant?->slug;
    }

    /**
     * Verifica se há um tenant ativo
     */
    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Verifica se o tenant pode usar uma funcionalidade
     */
    public function can(string $feature): bool
    {
        if (!$this->tenant) {
            return false;
        }

        return match ($feature) {
            'whatsapp' => $this->tenant->hasWhatsApp(),
            'payments' => $this->tenant->hasPayments(),
            default => true,
        };
    }

    /**
     * Retorna uma configuração do tenant
     */
    public function getSetting(string $key, $default = null)
    {
        return $this->tenant?->getSetting($key, $default) ?? $default;
    }

    /**
     * Aplica filtro de tenant em um query builder
     */
    public function applyFilter($builder)
    {
        if ($this->tenant) {
            $builder->where('tenant_id', $this->tenant->id);
        }
        return $builder;
    }
}
