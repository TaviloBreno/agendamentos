<?php

namespace App\Models;

use App\Entities\Unit;
use CodeIgniter\Model;

/**
 * UnitModel - Model para a tabela units
 * 
 * =========================================================================
 * CRIAÇÃO VIA CLI:
 * =========================================================================
 * php spark make:model UnitModel
 * 
 * Isso cria: app/Models/UnitModel.php
 * 
 * =========================================================================
 * MODEL vs ENTITY NO CODEIGNITER 4:
 * =========================================================================
 * 
 * MODEL (UnitModel):
 * - Responsável pela comunicação com o banco de dados
 * - Operações CRUD: insert, update, delete, find, findAll
 * - Validação de dados antes de persistir
 * - Configurações de tabela, campos permitidos, timestamps
 * 
 * ENTITY (Unit):
 * - Representa uma única linha/registro da tabela
 * - Objeto com propriedades tipadas
 * - Contém regras de negócio e comportamentos
 * - Métodos para formatar/manipular dados
 * 
 * FLUXO:
 * Controller → Model (busca no BD) → Entity (objeto retornado)
 * Controller → Entity (preenche) → Model (persiste no BD)
 * 
 * @package    App\Models
 * @author     Sistema de Agendamentos
 */
class UnitModel extends Model
{
    /**
     * Nome da tabela no banco de dados
     * 
     * @var string
     */
    protected $table = 'units';

    /**
     * Nome da chave primária
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Se a chave primária usa auto incremento
     * 
     * @var bool
     */
    protected $useAutoIncrement = true;

    /**
     * Tipo de retorno das consultas
     * 
     * IMPORTANTE: Ao definir uma Entity como returnType, cada registro
     * retornado do banco é automaticamente convertido em um objeto Unit.
     * 
     * Opções:
     * - 'array'     : Retorna arrays associativos
     * - 'object'    : Retorna objetos stdClass
     * - Unit::class : Retorna instâncias da Entity Unit
     * 
     * @var string
     */
    protected $returnType = Unit::class;

    /**
     * Soft Deletes - Exclusão lógica
     * 
     * Se true, registros não são deletados fisicamente, apenas marcados
     * com data em deleted_at. Requer coluna deleted_at na tabela.
     * 
     * ATENÇÃO: Só habilite se a migration incluir a coluna deleted_at!
     * Nossa migration atual NÃO possui essa coluna, então mantemos false.
     * 
     * @var bool
     */
    protected $useSoftDeletes = false;

    /**
     * Proteção de campos
     * 
     * Se true, apenas os campos listados em $allowedFields podem ser
     * inseridos/atualizados via mass assignment (insert/update com array).
     * 
     * @var bool
     */
    protected $protectFields = true;

    /**
     * Campos permitidos para mass assignment
     * 
     * Lista de colunas que podem ser inseridas/atualizadas.
     * NÃO incluir: id, created_at, updated_at (gerenciados automaticamente)
     * 
     * @var array<string>
     */
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'coordinator',
        'address',
        'services',
        'start_time',
        'end_time',
        'service_time',
        'active',
    ];

    // =========================================================================
    // TIMESTAMPS - Gerenciamento automático de datas
    // =========================================================================

    /**
     * Habilita gerenciamento automático de timestamps
     * 
     * Se true, created_at e updated_at são preenchidos automaticamente
     * 
     * @var bool
     */
    protected $useTimestamps = true;

    /**
     * Nome do campo para data de criação
     * 
     * @var string
     */
    protected $createdField = 'created_at';

    /**
     * Nome do campo para data de atualização
     * 
     * @var string
     */
    protected $updatedField = 'updated_at';

    /**
     * Formato de data para timestamps
     * 
     * @var string
     */
    protected $dateFormat = 'datetime';

    // =========================================================================
    // VALIDAÇÃO - Regras para insert/update
    // =========================================================================

    /**
     * Regras de validação
     * 
     * Aplicadas automaticamente em insert() e update() quando
     * $skipValidation é false (padrão).
     * 
     * @var array<string, string>
     */
    protected $validationRules = [
        'name'         => 'required|min_length[3]|max_length[70]',
        'email'        => 'required|valid_email|max_length[100]',
        'phone'        => 'required|max_length[14]',
        'coordinator'  => 'required|max_length[70]',
        'address'      => 'required|max_length[255]',
        'start_time'   => 'required|max_length[5]',
        'end_time'     => 'required|max_length[5]',
        'service_time' => 'required|max_length[20]',
        'active'       => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Mensagens de erro personalizadas
     * 
     * @var array<string, array<string, string>>
     */
    protected $validationMessages = [
        'name' => [
            'required'   => 'O nome da unidade é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome não pode exceder 70 caracteres.',
        ],
        'email' => [
            'required'    => 'O e-mail é obrigatório.',
            'valid_email' => 'Informe um e-mail válido.',
        ],
        'phone' => [
            'required' => 'O telefone é obrigatório.',
        ],
        'coordinator' => [
            'required' => 'O nome do coordenador é obrigatório.',
        ],
        'address' => [
            'required' => 'O endereço é obrigatório.',
        ],
        'start_time' => [
            'required' => 'O horário de início é obrigatório.',
        ],
        'end_time' => [
            'required' => 'O horário de término é obrigatório.',
        ],
        'service_time' => [
            'required' => 'O tempo de atendimento é obrigatório.',
        ],
    ];

    /**
     * Se deve pular validação (padrão: false)
     * 
     * @var bool
     */
    protected $skipValidation = false;

    // =========================================================================
    // CALLBACKS - Hooks para before/after de operações
    // =========================================================================

    /**
     * Callbacks disponíveis:
     * - beforeInsert, afterInsert
     * - beforeUpdate, afterUpdate  
     * - beforeDelete, afterDelete
     * - beforeFind, afterFind
     */
    protected $allowCallbacks = true;

    protected $beforeInsert = [];
    protected $afterInsert  = [];
    protected $beforeUpdate = [];
    protected $afterUpdate  = [];
    protected $beforeFind   = [];
    protected $afterFind    = [];
    protected $beforeDelete = [];
    protected $afterDelete  = [];

    // =========================================================================
    // MÉTODOS CUSTOMIZADOS - Queries específicas do negócio
    // =========================================================================

    /**
     * Busca um registro por ID ou lança 404
     * 
     * =========================================================================
     * FINDORFAIL PATTERN
     * =========================================================================
     * 
     * Este método é útil quando o registro DEVE existir.
     * Evita verificações manuais de null no controller.
     * 
     * SEM findOrFail (código repetitivo):
     *   $unit = $model->find($id);
     *   if ($unit === null) {
     *       throw PageNotFoundException::forPageNotFound();
     *   }
     * 
     * COM findOrFail (limpo e direto):
     *   $unit = $model->findOrFail($id);  // Lança 404 automaticamente
     * 
     * @param int|string $id ID do registro
     * @return Unit Entity encontrada
     * @throws \CodeIgniter\Exceptions\PageNotFoundException Se não existir
     */
    public function findOrFail(int|string $id): Unit
    {
        $record = $this->find($id);
        
        if ($record === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Registro não encontrado: {$id}"
            );
        }
        
        return $record;
    }

    /**
     * Busca apenas unidades ativas
     * 
     * @return array<Unit>
     */
    public function getActiveUnits(): array
    {
        return $this->where('active', 1)->findAll();
    }

    /**
     * Busca unidade por email
     * 
     * @param string $email
     * @return Unit|null
     */
    public function findByEmail(string $email): ?Unit
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Busca unidades que oferecem determinado serviço
     * 
     * @param int $serviceId
     * @return array<Unit>
     */
    public function getUnitsByService(int $serviceId): array
    {
        // JSON_CONTAINS verifica se o array JSON contém o valor
        return $this->where("JSON_CONTAINS(services, '{$serviceId}')", null, false)
                    ->findAll();
    }
}
