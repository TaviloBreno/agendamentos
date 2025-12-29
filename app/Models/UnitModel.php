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
     * =========================================================================
     * VALIDAÇÃO PARA INSERT E UPDATE
     * =========================================================================
     * 
     * Aplicadas automaticamente em insert() e update() quando
     * $skipValidation é false (padrão).
     * 
     * -------------------------------------------------------------------------
     * REGRA is_unique COM IGNORE PARA UPDATE
     * -------------------------------------------------------------------------
     * 
     * Formato: is_unique[tabela.coluna,coluna_ignore,{placeholder}]
     * 
     * Exemplo: is_unique[units.name,id,{id}]
     * 
     * Explicação:
     * - units.name     → Verifica unicidade na coluna 'name' da tabela 'units'
     * - id,{id}        → Ignora o registro onde 'id' = valor passado em {id}
     * 
     * O placeholder {id} é substituído automaticamente pelo Model quando:
     * - update($id, $data) → {id} recebe o valor de $id
     * - insert($data) → {id} fica vazio (valida normalmente)
     * 
     * Isso permite que no UPDATE o próprio registro não "viole" a unicidade.
     * 
     * REFERÊNCIA:
     * @link https://codeigniter.com/user_guide/libraries/validation.html#is-unique
     * 
     * @var array<string, string>
     */
    protected $validationRules = [
        // ID: apenas para update, permite vazio no insert
        'id'           => 'permit_empty|is_natural_no_zero',
        
        // Nome: obrigatório, 3-70 chars, único ignorando próprio registro
        'name'         => 'required|min_length[3]|max_length[70]|is_unique[units.name,id,{id}]',
        
        // E-mail: obrigatório, válido, único ignorando próprio registro
        'email'        => 'required|valid_email|max_length[100]|is_unique[units.email,id,{id}]',
        
        // Telefone: obrigatório, formato (XX) XXXXX-XXXX = 14 chars, único
        'phone'        => 'required|exact_length[14]|is_unique[units.phone,id,{id}]',
        
        // Coordenador: opcional, máximo 70 chars
        'coordinator'  => 'permit_empty|max_length[70]',
        
        // Endereço: obrigatório, máximo 128 chars
        'address'      => 'required|max_length[128]',
        
        // Horários: obrigatório, formato HH:MM via regex
        'start_time'   => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
        'end_time'     => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
        
        // Tempo de atendimento: obrigatório, máximo 20 chars
        'service_time' => 'required|max_length[20]',
        
        // Status ativo: obrigatório, apenas 0 ou 1
        'active'       => 'required|in_list[0,1]',
    ];

    /**
     * Mensagens de erro personalizadas
     * 
     * Organizadas por campo, depois por regra.
     * Mensagens em português para melhor UX.
     * 
     * @var array<string, array<string, string>>
     */
    protected $validationMessages = [
        'name' => [
            'required'   => 'O nome da unidade é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome não pode exceder 70 caracteres.',
            'is_unique'  => 'Já existe uma unidade cadastrada com este nome.',
        ],
        'email' => [
            'required'    => 'O e-mail é obrigatório.',
            'valid_email' => 'Informe um endereço de e-mail válido.',
            'max_length'  => 'O e-mail não pode exceder 100 caracteres.',
            'is_unique'   => 'Este e-mail já está sendo usado por outra unidade.',
        ],
        'phone' => [
            'required'     => 'O telefone é obrigatório.',
            'exact_length' => 'O telefone deve ter exatamente 14 caracteres (formato: (XX) XXXXX-XXXX).',
            'is_unique'    => 'Este telefone já está cadastrado em outra unidade.',
        ],
        'coordinator' => [
            'max_length' => 'O nome do coordenador não pode exceder 70 caracteres.',
        ],
        'address' => [
            'required'   => 'O endereço é obrigatório.',
            'max_length' => 'O endereço não pode exceder 128 caracteres.',
        ],
        'start_time' => [
            'required'    => 'O horário de início é obrigatório.',
            'regex_match' => 'O horário de início deve estar no formato HH:MM (ex: 08:00).',
        ],
        'end_time' => [
            'required'    => 'O horário de término é obrigatório.',
            'regex_match' => 'O horário de término deve estar no formato HH:MM (ex: 18:00).',
        ],
        'service_time' => [
            'required'   => 'O tempo de atendimento é obrigatório.',
            'max_length' => 'O tempo de atendimento não pode exceder 20 caracteres.',
        ],
        'active' => [
            'required' => 'O status (ativo/inativo) é obrigatório.',
            'in_list'  => 'O status deve ser 0 (inativo) ou 1 (ativo).',
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

    /**
     * Callbacks executados ANTES de inserir
     * 
     * escapeData: Sanitiza strings para prevenir XSS
     */
    protected $beforeInsert = ['escapeData'];
    protected $afterInsert  = [];

    /**
     * Callbacks executados ANTES de atualizar
     * 
     * escapeData: Sanitiza strings para prevenir XSS
     */
    protected $beforeUpdate = ['escapeData'];
    protected $afterUpdate  = [];

    protected $beforeFind   = [];
    protected $afterFind    = [];
    protected $beforeDelete = [];
    protected $afterDelete  = [];

    // =========================================================================
    // CALLBACK METHODS - Métodos executados pelos callbacks
    // =========================================================================

    /**
     * Sanitiza os dados antes de INSERT ou UPDATE
     * 
     * =========================================================================
     * ESCAPE/SANITIZAÇÃO PARA PREVENÇÃO DE XSS
     * =========================================================================
     * 
     * Este callback é executado automaticamente antes de qualquer INSERT ou UPDATE.
     * Ele aplica a função esc() do CodeIgniter em todos os valores string,
     * convertendo caracteres especiais HTML em entities seguras.
     * 
     * FUNCIONAMENTO DO CALLBACK:
     * - Model passa array com chave 'data' contendo os dados a inserir/atualizar
     * - Iteramos sobre cada campo em $data['data']
     * - Strings são sanitizadas com esc() (default: 'html')
     * - Arrays/objetos/números são mantidos intactos
     * - Retornamos o array modificado para prosseguir a operação
     * 
     * O QUE esc() FAZ:
     * - '<script>'       → '&lt;script&gt;'
     * - '<img onerror>'  → '&lt;img onerror&gt;'
     * - '&'              → '&amp;'
     * - '"'              → '&quot;'
     * - "'"              → '&#039;'
     * 
     * POR QUE USAR:
     * - Previne ataques XSS (Cross-Site Scripting)
     * - Dados são armazenados de forma segura no banco
     * - Quando exibidos, os caracteres especiais são renderizados como texto
     * 
     * NOTA:
     * - Campos JSON (como 'services') são tratados como arrays, não strings
     * - O cast do Entity já serializa arrays para JSON automaticamente
     * - Campos de data/hora e numéricos passam sem alteração
     * 
     * @param array $data Array contendo 'data' com os campos a inserir/atualizar
     * @return array Array modificado com strings sanitizadas
     * 
     * @link https://codeigniter.com/user_guide/general/common_functions.html#esc
     */
    protected function escapeData(array $data): array
    {
        // Verifica se existe o array de dados
        if (! isset($data['data']) || ! is_array($data['data'])) {
            return $data;
        }

        // Itera sobre cada campo nos dados
        foreach ($data['data'] as $field => $value) {
            // Aplica escape apenas em strings não vazias
            if (is_string($value) && $value !== '') {
                $data['data'][$field] = esc($value, 'html');
            }
            // Arrays, objetos, números e booleanos passam sem alteração
        }

        return $data;
    }

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
