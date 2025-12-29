<?php

namespace App\Models;

use App\Entities\Client;
use CodeIgniter\Model;

/**
 * ClientModel - Model para operações com clientes
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Gerencia operações de banco de dados para a tabela `clients`.
 * Inclui validação, callbacks e métodos auxiliares.
 * 
 * @package    App\Models
 * @author     Sistema de Agendamentos
 */
class ClientModel extends Model
{
    // =========================================================================
    // CONFIGURAÇÕES BÁSICAS
    // =========================================================================

    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Client::class;
    protected $useSoftDeletes = true;

    // =========================================================================
    // CAMPOS PERMITIDOS
    // =========================================================================

    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'cpf',
        'birth_date',
        'gender',
        'address',
        'city',
        'state',
        'zip_code',
        'notes',
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
        'id'         => 'permit_empty|is_natural_no_zero',
        'name'       => 'required|min_length[3]|max_length[150]',
        'email'      => 'required|valid_email|max_length[150]|is_unique[clients.email,id,{id}]',
        'phone'      => 'permit_empty|max_length[20]',
        'cpf'        => 'permit_empty|max_length[14]|is_unique[clients.cpf,id,{id}]',
        'birth_date' => 'permit_empty|valid_date',
        'gender'     => 'permit_empty|in_list[M,F,O]',
        'address'    => 'permit_empty|max_length[255]',
        'city'       => 'permit_empty|max_length[100]',
        'state'      => 'permit_empty|max_length[2]',
        'zip_code'   => 'permit_empty|max_length[10]',
        'notes'      => 'permit_empty',
        'active'     => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'O nome é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome não pode exceder 150 caracteres.',
        ],
        'email' => [
            'required'    => 'O e-mail é obrigatório.',
            'valid_email' => 'Informe um e-mail válido.',
            'max_length'  => 'O e-mail não pode exceder 150 caracteres.',
            'is_unique'   => 'Este e-mail já está cadastrado.',
        ],
        'cpf' => [
            'is_unique' => 'Este CPF já está cadastrado.',
        ],
        'birth_date' => [
            'valid_date' => 'Informe uma data de nascimento válida.',
        ],
        'gender' => [
            'in_list' => 'Gênero inválido.',
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
    protected $beforeInsert = ['cleanCpf', 'cleanPhone'];
    protected $beforeUpdate = ['cleanCpf', 'cleanPhone'];

    /**
     * Remove formatação do CPF antes de salvar
     */
    protected function cleanCpf(array $data): array
    {
        if (isset($data['data']['cpf'])) {
            $data['data']['cpf'] = preg_replace('/[^0-9]/', '', $data['data']['cpf']);
            if (empty($data['data']['cpf'])) {
                $data['data']['cpf'] = null;
            }
        }
        return $data;
    }

    /**
     * Remove formatação do telefone antes de salvar
     */
    protected function cleanPhone(array $data): array
    {
        if (isset($data['data']['phone'])) {
            $data['data']['phone'] = preg_replace('/[^0-9]/', '', $data['data']['phone']);
            if (empty($data['data']['phone'])) {
                $data['data']['phone'] = null;
            }
        }
        return $data;
    }

    // =========================================================================
    // MÉTODOS CUSTOMIZADOS
    // =========================================================================

    /**
     * Busca cliente por ID ou lança 404
     */
    public function findOrFail(int|string $id): Client
    {
        $client = $this->find($id);
        
        if ($client === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Cliente não encontrado: {$id}"
            );
        }
        
        return $client;
    }

    /**
     * Busca apenas clientes ativos
     */
    public function getActiveClients(): array
    {
        return $this->where('active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Busca cliente por email
     */
    public function findByEmail(string $email): ?Client
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Busca cliente por CPF
     */
    public function findByCpf(string $cpf): ?Client
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return $this->where('cpf', $cpf)->first();
    }

    /**
     * Busca clientes por nome (parcial)
     */
    public function searchByName(string $name): array
    {
        return $this->like('name', $name, 'both')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Retorna clientes para dropdown
     */
    public function getForDropdown(): array
    {
        $clients = $this->where('active', 1)
                        ->orderBy('name', 'ASC')
                        ->findAll();
        
        $dropdown = [];
        foreach ($clients as $client) {
            $dropdown[$client->id] = $client->name;
        }
        
        return $dropdown;
    }

    /**
     * Retorna clientes para dropdown com detalhes
     */
    public function getForDropdownDetailed(): array
    {
        $clients = $this->where('active', 1)
                        ->orderBy('name', 'ASC')
                        ->findAll();
        
        $dropdown = [];
        foreach ($clients as $client) {
            $label = $client->name;
            if ($client->email) {
                $label .= ' (' . $client->email . ')';
            }
            $dropdown[$client->id] = $label;
        }
        
        return $dropdown;
    }

    /**
     * Conta clientes por status
     */
    public function countByStatus(): array
    {
        return [
            'total'    => $this->countAllResults(false),
            'active'   => $this->where('active', 1)->countAllResults(false),
            'inactive' => $this->where('active', 0)->countAllResults(false),
        ];
    }

    /**
     * Busca clientes aniversariantes do mês
     */
    public function getBirthdaysThisMonth(): array
    {
        $month = date('m');
        return $this->where('MONTH(birth_date)', $month)
                    ->where('active', 1)
                    ->orderBy('DAY(birth_date)', 'ASC')
                    ->findAll();
    }

    /**
     * Busca clientes aniversariantes de hoje
     */
    public function getBirthdaysToday(): array
    {
        $today = date('m-d');
        return $this->where("DATE_FORMAT(birth_date, '%m-%d')", $today)
                    ->where('active', 1)
                    ->findAll();
    }

    /**
     * Busca clientes recentes
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Busca ou cria cliente pelo email
     */
    public function findOrCreate(array $data): Client
    {
        $client = $this->findByEmail($data['email'] ?? '');
        
        if ($client !== null) {
            // Atualiza dados se necessário
            if (!empty($data['name']) && $data['name'] !== $client->name) {
                $this->update($client->id, ['name' => $data['name']]);
                $client->name = $data['name'];
            }
            if (!empty($data['phone']) && $data['phone'] !== $client->phone) {
                $this->update($client->id, ['phone' => $data['phone']]);
                $client->phone = $data['phone'];
            }
            return $client;
        }
        
        // Cria novo cliente
        $clientData = [
            'name'   => $data['name'] ?? '',
            'email'  => $data['email'] ?? '',
            'phone'  => $data['phone'] ?? null,
            'active' => 1,
        ];
        
        $id = $this->insert($clientData);
        return $this->find($id);
    }
}
