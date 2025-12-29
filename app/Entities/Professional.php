<?php

namespace App\Entities;

/**
 * Entity Professional
 * 
 * Representa um profissional do sistema de agendamentos.
 * 
 * =========================================================================
 * PROPRIEDADES (Colunas da Tabela)
 * =========================================================================
 * 
 * @property int         $id
 * @property int|null    $unit_id
 * @property string      $name
 * @property string      $email
 * @property string|null $phone
 * @property string|null $specialty
 * @property string|null $bio
 * @property string|null $avatar
 * @property array|null  $services
 * @property bool        $active
 * @property \CodeIgniter\I18n\Time|null $created_at
 * @property \CodeIgniter\I18n\Time|null $updated_at
 * 
 * @package    App\Entities
 */
class Professional extends MyBaseEntity
{
    /**
     * Tipos de cast automático
     */
    protected $casts = [
        'id'        => 'integer',
        'unit_id'   => '?integer',
        'active'    => 'boolean',
        'services'  => 'json-array',
    ];

    /**
     * Datas que devem ser convertidas para Time
     */
    protected $dates = ['created_at', 'updated_at'];

    // =========================================================================
    // MÉTODOS DE FORMATAÇÃO
    // =========================================================================

    /**
     * Retorna as iniciais do nome para avatar
     * 
     * @return string Ex: "JD" para "João da Silva"
     */
    public function initials(): string
    {
        $parts = explode(' ', $this->name ?? '');
        $initials = '';
        
        if (count($parts) >= 2) {
            $initials = strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        } else {
            $initials = strtoupper(substr($this->name ?? '', 0, 2));
        }
        
        return $initials;
    }

    /**
     * Retorna URL do avatar ou placeholder
     * 
     * @return string URL da imagem
     */
    public function avatarUrl(): string
    {
        if ($this->avatar && file_exists(FCPATH . $this->avatar)) {
            return base_url($this->avatar);
        }
        
        // Placeholder com iniciais usando API externa
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name ?? 'P') . "&background=4e73df&color=fff&size=128";
    }

    /**
     * Retorna a especialidade formatada ou padrão
     * 
     * @return string
     */
    public function specialtyFormatted(): string
    {
        return $this->specialty ?: 'Não informada';
    }

    /**
     * Retorna descrição curta da bio
     * 
     * @param int $length Tamanho máximo
     * @return string
     */
    public function shortBio(int $length = 100): string
    {
        if (empty($this->bio)) {
            return '<span class="text-muted">Sem descrição</span>';
        }
        
        if (strlen($this->bio) <= $length) {
            return $this->bio;
        }
        
        return substr($this->bio, 0, $length) . '...';
    }

    /**
     * Retorna telefone formatado
     * 
     * @return string
     */
    public function phoneFormatted(): string
    {
        if (empty($this->phone)) {
            return '<span class="text-muted">Não informado</span>';
        }
        
        return $this->phone;
    }

    // =========================================================================
    // MÉTODOS DE STATUS (Badges e Botões)
    // =========================================================================

    /**
     * Retorna badge HTML do status
     * 
     * @return string HTML do badge
     */
    public function statusBadge(): string
    {
        if ($this->active) {
            return '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Ativo</span>';
        }
        
        return '<span class="badge badge-secondary"><i class="fas fa-ban mr-1"></i>Inativo</span>';
    }

    /**
     * Texto para botão de ação (toggle status)
     * 
     * @return string
     */
    public function textToAction(): string
    {
        return $this->active ? 'Desativar' : 'Ativar';
    }

    /**
     * Ícone para botão de ação
     * 
     * @return string Nome do ícone FontAwesome
     */
    public function iconToAction(): string
    {
        return $this->active ? 'fa-ban' : 'fa-check';
    }

    /**
     * Classe CSS para botão de ação
     * 
     * @return string
     */
    public function classToAction(): string
    {
        return $this->active ? 'text-warning' : 'text-success';
    }

    // =========================================================================
    // MÉTODOS DE RELACIONAMENTO
    // =========================================================================

    /**
     * Retorna a unidade do profissional
     * 
     * @return \App\Entities\Unit|null
     */
    public function getUnit(): ?\App\Entities\Unit
    {
        if (empty($this->unit_id)) {
            return null;
        }
        
        return model(\App\Models\UnitModel::class)->find($this->unit_id);
    }

    /**
     * Retorna os serviços que o profissional realiza
     * 
     * @return array Array de Service entities
     */
    public function getServices(): array
    {
        $serviceIds = $this->services;
        
        if (empty($serviceIds)) {
            return [];
        }
        
        return model(\App\Models\ServiceModel::class)->findByIds($serviceIds);
    }

    /**
     * Conta quantos serviços o profissional realiza
     * 
     * @return int
     */
    public function servicesCount(): int
    {
        return count($this->services ?? []);
    }

    /**
     * Verifica se o profissional realiza um serviço específico
     * 
     * @param int $serviceId
     * @return bool
     */
    public function hasService(int $serviceId): bool
    {
        return in_array($serviceId, $this->services ?? []);
    }
}
