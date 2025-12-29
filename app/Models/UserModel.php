<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

/**
 * UserModel - Model para operações com usuários
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Gerencia operações de banco de dados para a tabela `users`.
 * Inclui validação, callbacks e métodos auxiliares para autenticação.
 * 
 * @package    App\Models
 * @author     Sistema de Agendamentos
 */
class UserModel extends Model
{
    // =========================================================================
    // CONFIGURAÇÕES BÁSICAS
    // =========================================================================

    /**
     * Nome da tabela no banco de dados
     */
    protected $table = 'users';

    /**
     * Chave primária da tabela
     */
    protected $primaryKey = 'id';

    /**
     * Usar auto-incremento para ID
     */
    protected $useAutoIncrement = true;

    /**
     * Tipo de retorno das queries
     * 
     * Retorna instâncias de User Entity ao invés de arrays.
     */
    protected $returnType = User::class;

    /**
     * Usar soft deletes (deleted_at)
     */
    protected $useSoftDeletes = true;

    // =========================================================================
    // CAMPOS PERMITIDOS (Mass Assignment Protection)
    // =========================================================================

    /**
     * Campos que podem ser inseridos/atualizados em massa
     * 
     * IMPORTANTE: 'password' está incluído pois o Entity faz o hash
     * automaticamente no setter antes de chegar ao Model.
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

    // =========================================================================
    // TIMESTAMPS
    // =========================================================================

    /**
     * Gerenciar created_at e updated_at automaticamente
     */
    protected $useTimestamps = true;

    /**
     * Formato das datas no banco
     */
    protected $dateFormat = 'datetime';

    /**
     * Nome da coluna created_at
     */
    protected $createdField = 'created_at';

    /**
     * Nome da coluna updated_at
     */
    protected $updatedField = 'updated_at';

    /**
     * Nome da coluna deleted_at (soft delete)
     */
    protected $deletedField = 'deleted_at';

    // =========================================================================
    // VALIDAÇÃO
    // =========================================================================

    /**
     * Regras de validação
     * 
     * @var array<string, string>
     */
    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|max_length[150]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[super,admin,user]',
        'active'   => 'required|in_list[0,1]',
    ];

    /**
     * Mensagens de validação personalizadas
     * 
     * @var array<string, array<string, string>>
     */
    protected $validationMessages = [
        'name' => [
            'required'   => 'O nome é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome não pode exceder 100 caracteres.',
        ],
        'email' => [
            'required'    => 'O e-mail é obrigatório.',
            'valid_email' => 'Informe um e-mail válido.',
            'max_length'  => 'O e-mail não pode exceder 150 caracteres.',
            'is_unique'   => 'Este e-mail já está cadastrado.',
        ],
        'password' => [
            'required'   => 'A senha é obrigatória.',
            'min_length' => 'A senha deve ter pelo menos 6 caracteres.',
        ],
        'role' => [
            'required' => 'O papel do usuário é obrigatório.',
            'in_list'  => 'Papel inválido. Use: super, admin ou user.',
        ],
        'active' => [
            'required' => 'O status é obrigatório.',
            'in_list'  => 'O status deve ser 0 ou 1.',
        ],
    ];

    /**
     * Não pular validação
     */
    protected $skipValidation = false;

    // =========================================================================
    // CALLBACKS
    // =========================================================================

    protected $allowCallbacks = true;

    protected $beforeInsert = ['escapeData'];
    protected $beforeUpdate = ['escapeData'];

    /**
     * Sanitiza dados antes de inserir/atualizar
     * 
     * @param array $data
     * @return array
     */
    protected function escapeData(array $data): array
    {
        if (! isset($data['data']) || ! is_array($data['data'])) {
            return $data;
        }

        // Campos que NÃO devem ser escapados
        $skipFields = ['password', 'email'];

        foreach ($data['data'] as $field => $value) {
            // Pula campos especiais
            if (in_array($field, $skipFields, true)) {
                continue;
            }
            
            // Aplica escape em strings
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
     * Busca usuário por ID ou lança 404
     * 
     * @param int|string $id
     * @return User
     * @throws \CodeIgniter\Exceptions\PageNotFoundException
     */
    public function findOrFail(int|string $id): User
    {
        $user = $this->find($id);
        
        if ($user === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Usuário não encontrado: {$id}"
            );
        }
        
        return $user;
    }

    /**
     * Busca usuário por email para autenticação
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Tenta autenticar um usuário
     * 
     * @param string $email
     * @param string $password
     * @return User|null Retorna o usuário se autenticado, null caso contrário
     */
    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->findByEmail($email);
        
        // Usuário não existe
        if ($user === null) {
            return null;
        }
        
        // Usuário inativo
        if (! $user->isActive()) {
            return null;
        }
        
        // Senha incorreta
        if (! $user->verifyPassword($password)) {
            return null;
        }
        
        return $user;
    }

    /**
     * Busca usuários por papel
     * 
     * @param string $role
     * @return array<User>
     */
    public function findByRole(string $role): array
    {
        return $this->where('role', $role)->findAll();
    }

    /**
     * Busca apenas usuários ativos
     * 
     * @return array<User>
     */
    public function getActiveUsers(): array
    {
        return $this->where('active', 1)->findAll();
    }

    /**
     * Regras de validação para update (sem senha obrigatória)
     * 
     * @return array<string, string>
     */
    public function getValidationRulesForUpdate(): array
    {
        $rules = $this->validationRules;
        $rules['password'] = 'permit_empty|min_length[6]';
        return $rules;
    }
}
