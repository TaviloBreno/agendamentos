<?php

namespace App\Models;

use App\Entities\Service;
use CodeIgniter\Model;

/**
 * ServiceModel - Model para operações com serviços
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Gerencia operações de banco de dados para a tabela `services`.
 * Inclui validação, callbacks e métodos auxiliares.
 * 
 * @package    App\Models
 * @author     Sistema de Agendamentos
 */
class ServiceModel extends Model
{
    // =========================================================================
    // CONFIGURAÇÕES BÁSICAS
    // =========================================================================

    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Service::class;
    protected $useSoftDeletes = true;

    // =========================================================================
    // CAMPOS PERMITIDOS
    // =========================================================================

    protected $allowedFields = [
        'name',
        'description',
        'duration',
        'price',
        'active',
    ];

    // =========================================================================
    // TIMESTAMPS
    // =========================================================================

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // =========================================================================
    // VALIDAÇÃO
    // =========================================================================

    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'name'        => 'required|min_length[3]|max_length[100]|is_unique[services.name,id,{id}]',
        'description' => 'permit_empty|max_length[1000]',
        'duration'    => 'required|is_natural_no_zero|less_than_equal_to[480]',
        'price'       => 'required|decimal|greater_than_equal_to[0]',
        'active'      => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'O nome do serviço é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome não pode exceder 100 caracteres.',
            'is_unique'  => 'Já existe um serviço com este nome.',
        ],
        'description' => [
            'max_length' => 'A descrição não pode exceder 1000 caracteres.',
        ],
        'duration' => [
            'required'            => 'A duração é obrigatória.',
            'is_natural_no_zero'  => 'A duração deve ser um número inteiro positivo.',
            'less_than_equal_to'  => 'A duração não pode exceder 480 minutos (8 horas).',
        ],
        'price' => [
            'required'              => 'O preço é obrigatório.',
            'decimal'               => 'O preço deve ser um valor decimal válido.',
            'greater_than_equal_to' => 'O preço não pode ser negativo.',
        ],
        'active' => [
            'required' => 'O status é obrigatório.',
            'in_list'  => 'O status deve ser 0 ou 1.',
        ],
    ];

    protected $skipValidation = false;

    // =========================================================================
    // CALLBACKS
    // =========================================================================

    protected $allowCallbacks = true;
    protected $beforeInsert = ['escapeData'];
    protected $beforeUpdate = ['escapeData'];

    /**
     * Sanitiza dados antes de inserir/atualizar
     */
    protected function escapeData(array $data): array
    {
        if (! isset($data['data']) || ! is_array($data['data'])) {
            return $data;
        }

        // Campos que NÃO devem ser escapados
        $skipFields = ['price', 'duration', 'active'];

        foreach ($data['data'] as $field => $value) {
            if (in_array($field, $skipFields, true)) {
                continue;
            }
            
            if (is_string($value) && $value !== '') {
                $data['data'][$field] = esc($value, 'html');
            }
        }

        return $data;
    }

    // =========================================================================
    // MÉTODOS CUSTOMIZADOS
    // =========================================================================

    /**
     * Busca serviço por ID ou lança 404
     */
    public function findOrFail(int|string $id): Service
    {
        $service = $this->find($id);
        
        if ($service === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Serviço não encontrado: {$id}"
            );
        }
        
        return $service;
    }

    /**
     * Busca apenas serviços ativos
     */
    public function getActiveServices(): array
    {
        return $this->where('active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Busca serviços por IDs (para associação com unidades)
     * 
     * @param array $ids Array de IDs de serviços
     * @return array<Service>
     */
    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->whereIn('id', $ids)->findAll();
    }

    /**
     * Retorna serviços formatados para select/dropdown
     * 
     * @return array [id => name]
     */
    public function getForDropdown(): array
    {
        $services = $this->where('active', 1)
                         ->orderBy('name', 'ASC')
                         ->findAll();
        
        $options = [];
        foreach ($services as $service) {
            $options[$service->id] = $service->name;
        }
        
        return $options;
    }

    /**
     * Retorna serviços com informações completas para select
     * 
     * @return array [id => "Nome - Duração - Preço"]
     */
    public function getForDropdownDetailed(): array
    {
        $services = $this->where('active', 1)
                         ->orderBy('name', 'ASC')
                         ->findAll();
        
        $options = [];
        foreach ($services as $service) {
            $options[$service->id] = sprintf(
                '%s (%s - %s)',
                $service->name,
                $service->durationFormatted(),
                $service->priceFormatted()
            );
        }
        
        return $options;
    }
}
