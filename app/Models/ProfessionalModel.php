<?php

namespace App\Models;

use App\Entities\Professional;
use CodeIgniter\Model;

/**
 * ProfessionalModel - Model para operações com Profissionais
 * 
 * =========================================================================
 * RESPONSABILIDADES
 * =========================================================================
 * 
 * - CRUD de profissionais
 * - Validação de dados
 * - Queries especializadas
 * - Relacionamentos com Unit e Services
 * 
 * @package    App\Models
 */
class ProfessionalModel extends Model
{
    protected $table            = 'professionals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Professional::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'unit_id',
        'name',
        'email',
        'phone',
        'specialty',
        'bio',
        'avatar',
        'services',
        'active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|max_length[100]|is_unique[professionals.email,id,{id}]',
        'phone'    => 'permit_empty|max_length[20]',
        'specialty'=> 'permit_empty|max_length[100]',
        'unit_id'  => 'permit_empty|integer',
        'active'   => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'O nome do profissional é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome deve ter no máximo 100 caracteres.',
        ],
        'email' => [
            'required'    => 'O e-mail é obrigatório.',
            'valid_email' => 'Informe um e-mail válido.',
            'is_unique'   => 'Este e-mail já está cadastrado.',
        ],
    ];

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['prepareServices'];
    protected $beforeUpdate   = ['prepareServices'];

    /**
     * Prepara o campo services para JSON
     */
    protected function prepareServices(array $data): array
    {
        if (isset($data['data']['services']) && is_array($data['data']['services'])) {
            $data['data']['services'] = json_encode($data['data']['services']);
        }
        
        return $data;
    }

    // =========================================================================
    // MÉTODOS CUSTOMIZADOS
    // =========================================================================

    /**
     * Busca profissional ou lança 404
     * 
     * @param int $id
     * @return Professional
     * @throws \CodeIgniter\Exceptions\PageNotFoundException
     */
    public function findOrFail(int $id): Professional
    {
        $professional = $this->find($id);
        
        if ($professional === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Profissional ID {$id} não encontrado."
            );
        }
        
        return $professional;
    }

    /**
     * Retorna apenas profissionais ativos
     * 
     * @return array
     */
    public function getActiveProfessionals(): array
    {
        return $this->where('active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Retorna profissionais de uma unidade específica
     * 
     * @param int $unitId
     * @return array
     */
    public function getByUnit(int $unitId): array
    {
        return $this->where('unit_id', $unitId)
                    ->where('active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Retorna profissionais que realizam um serviço específico
     * 
     * @param int $serviceId
     * @return array
     */
    public function getByService(int $serviceId): array
    {
        return $this->where('active', 1)
                    ->like('services', '"' . $serviceId . '"')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Retorna profissionais para dropdown
     * 
     * @param bool $onlyActive
     * @return array [id => name]
     */
    public function getForDropdown(bool $onlyActive = true): array
    {
        $builder = $this->builder();
        
        if ($onlyActive) {
            $builder->where('active', 1);
        }
        
        $result = $builder->orderBy('name', 'ASC')->get()->getResult();
        
        $dropdown = [];
        foreach ($result as $row) {
            $dropdown[$row->id] = $row->name;
        }
        
        return $dropdown;
    }

    /**
     * Retorna profissionais para dropdown com detalhes
     * 
     * @return array [id => "Nome - Especialidade"]
     */
    public function getForDropdownDetailed(): array
    {
        $professionals = $this->where('active', 1)
                              ->orderBy('name', 'ASC')
                              ->findAll();
        
        $dropdown = [];
        foreach ($professionals as $prof) {
            $label = $prof->name;
            if ($prof->specialty) {
                $label .= " - {$prof->specialty}";
            }
            $dropdown[$prof->id] = $label;
        }
        
        return $dropdown;
    }

    /**
     * Busca profissionais por array de IDs
     * 
     * @param array $ids
     * @return array
     */
    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        
        return $this->whereIn('id', $ids)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Conta profissionais por status
     * 
     * @return array ['active' => x, 'inactive' => y, 'total' => z]
     */
    public function countByStatus(): array
    {
        $active = $this->where('active', 1)->countAllResults(false);
        $inactive = $this->where('active', 0)->countAllResults(false);
        
        return [
            'active'   => $active,
            'inactive' => $inactive,
            'total'    => $active + $inactive,
        ];
    }
}
