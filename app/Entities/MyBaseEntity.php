<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

/**
 * MyBaseEntity - Entity base com comportamentos comuns
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Centraliza funcionalidades que podem ser reutilizadas por múltiplas
 * Entities do sistema, como:
 * 
 * - Tratamento do campo `active` (ativo/inativo)
 * - Métodos para toggle de status
 * - Badges e textos de status
 * 
 * =========================================================================
 * USO
 * =========================================================================
 * 
 * Suas Entities devem estender esta classe:
 * 
 *   class Unit extends MyBaseEntity { ... }
 *   class Service extends MyBaseEntity { ... }
 * 
 * E automaticamente terão acesso a:
 *   $unit->isActive()        // bool
 *   $unit->textToAction()    // "Ativar" ou "Desativar"
 *   $unit->setAction()       // Toggle do status
 *   $unit->activate()        // Define como ativo
 *   $unit->deactivate()      // Define como inativo
 *   $unit->statusBadge()     // Badge HTML Bootstrap
 * 
 * @package    App\Entities
 * @author     Sistema de Agendamentos
 */
abstract class MyBaseEntity extends Entity
{
    // =========================================================================
    // MÉTODOS DE STATUS (ACTIVE)
    // =========================================================================

    /**
     * Verifica se o registro está ativo
     * 
     * @return bool True se ativo, false se inativo
     */
    public function isActive(): bool
    {
        // Suporta tanto int quanto bool (por causa do cast int-bool)
        return (bool) ($this->attributes['active'] ?? false);
    }

    /**
     * Retorna o texto da ação disponível (toggle)
     * 
     * Se está ATIVO → pode "Desativar"
     * Se está INATIVO → pode "Ativar"
     * 
     * Útil para labels de botões.
     * 
     * @return string "Ativar" ou "Desativar"
     */
    public function textToAction(): string
    {
        return $this->isActive() ? 'Desativar' : 'Ativar';
    }

    /**
     * Retorna o ícone FontAwesome para a ação disponível
     * 
     * @return string Classe do ícone (fa-ban ou fa-check)
     */
    public function iconToAction(): string
    {
        return $this->isActive() ? 'fa-ban' : 'fa-check';
    }

    /**
     * Retorna a classe CSS para o botão de ação
     * 
     * Se está ATIVO → botão de "Desativar" (secundário/warning)
     * Se está INATIVO → botão de "Ativar" (success)
     * 
     * @return string Classe Bootstrap do botão
     */
    public function classToAction(): string
    {
        return $this->isActive() ? 'text-secondary' : 'text-success';
    }

    /**
     * Alterna o status (toggle)
     * 
     * Se está ATIVO → torna INATIVO
     * Se está INATIVO → torna ATIVO
     * 
     * USO:
     *   $unit->setAction();
     *   $model->save($unit);
     * 
     * @return $this
     */
    public function setAction(): self
    {
        $this->attributes['active'] = $this->isActive() ? 0 : 1;
        
        return $this;
    }

    /**
     * Define o registro como ATIVO
     * 
     * @return $this
     */
    public function activate(): self
    {
        $this->attributes['active'] = 1;
        
        return $this;
    }

    /**
     * Define o registro como INATIVO
     * 
     * @return $this
     */
    public function deactivate(): self
    {
        $this->attributes['active'] = 0;
        
        return $this;
    }

    /**
     * Retorna o status como texto legível
     * 
     * @return string "Ativo" ou "Inativo"
     */
    public function statusLabel(): string
    {
        return $this->isActive() ? 'Ativo' : 'Inativo';
    }

    /**
     * Retorna badge HTML Bootstrap para o status
     * 
     * @return string HTML do badge
     */
    public function statusBadge(): string
    {
        if ($this->isActive()) {
            return '<span class="badge badge-success">Ativo</span>';
        }
        
        return '<span class="badge badge-secondary">Inativo</span>';
    }
}
