<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\UserService;
use App\Entities\User;

/**
 * UsersController - Gerenciamento de Usuários
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Controller responsável pelo CRUD completo de usuários no painel
 * administrativo. Utiliza UserService para renderização de dados.
 * 
 * @package    App\Controllers\Super
 * @author     Sistema de Agendamentos
 */
class UsersController extends BaseController
{
    /**
     * Model de usuários
     */
    protected UserModel $userModel;

    /**
     * Serviço de usuários
     */
    protected UserService $userService;

    /**
     * Construtor - Inicializa dependências
     */
    public function __construct()
    {
        $this->userModel = model('UserModel');
        $this->userService = new UserService();
    }

    // =========================================================================
    // CRUD - READ (Listagem)
    // =========================================================================

    /**
     * Lista todos os usuários
     * 
     * GET /super/users
     */
    public function index()
    {
        $users = $this->userModel->orderBy('name', 'ASC')->findAll();
        
        return view('Back/Users/index', [
            'title' => 'Usuários',
            'users' => $users,
            'stats' => $this->userService->getStats(),
            'data'  => $this->userService->renderUsers($users),
        ]);
    }

    // =========================================================================
    // CRUD - CREATE (Criação)
    // =========================================================================

    /**
     * Exibe formulário de novo usuário
     * 
     * GET /super/users/new
     */
    public function new()
    {
        return view('Back/Users/form', [
            'title' => 'Novo Usuário',
            'user'  => new User(),
            'roles' => [
                'super' => 'Super Admin',
                'admin' => 'Administrador',
                'user'  => 'Usuário',
            ],
        ]);
    }

    /**
     * Processa criação de novo usuário
     * 
     * POST /super/users
     */
    public function create()
    {
        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role'     => $this->request->getPost('role'),
            'active'   => $this->request->getPost('active') ?? 1,
        ];

        if (! $this->userModel->insert($data)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->userModel->errors());
        }

        return redirect()->to(route_to('super.users'))
                        ->with('success', 'Usuário cadastrado com sucesso!');
    }

    // =========================================================================
    // CRUD - READ (Detalhes)
    // =========================================================================

    /**
     * Exibe detalhes de um usuário
     * 
     * GET /super/users/(:num)
     */
    public function show(int $id)
    {
        $user = $this->userModel->findOrFail($id);
        
        return view('Back/Users/show', [
            'title' => 'Usuário: ' . $user->name,
            'user'  => $user,
        ]);
    }

    // =========================================================================
    // CRUD - UPDATE (Edição)
    // =========================================================================

    /**
     * Exibe formulário de edição
     * 
     * GET /super/users/(:num)/edit
     */
    public function edit(int $id)
    {
        $user = $this->userModel->findOrFail($id);
        
        return view('Back/Users/edit', [
            'title' => 'Editar Usuário',
            'user'  => $user,
            'roles' => [
                'super' => 'Super Admin',
                'admin' => 'Administrador',
                'user'  => 'Usuário',
            ],
        ]);
    }

    /**
     * Processa atualização do usuário
     * 
     * PUT /super/users/(:num)
     */
    public function update(int $id)
    {
        $user = $this->userModel->findOrFail($id);
        
        // Usar regras de validação para update (senha opcional)
        $this->userModel->setValidationRules(
            $this->userModel->getValidationRulesForUpdate()
        );
        
        $data = [
            'id'     => $id,
            'name'   => $this->request->getPost('name'),
            'email'  => $this->request->getPost('email'),
            'role'   => $this->request->getPost('role'),
            'active' => $this->request->getPost('active') ?? $user->active,
        ];

        // Só atualiza senha se foi informada
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        if (! $this->userModel->update($id, $data)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->userModel->errors());
        }

        return redirect()->to(route_to('super.users'))
                        ->with('success', 'Usuário atualizado com sucesso!');
    }

    // =========================================================================
    // ACTIONS (Toggle Status)
    // =========================================================================

    /**
     * Alterna status ativo/inativo do usuário
     * 
     * PUT /super/users/(:num)/action
     */
    public function action(int $id)
    {
        $user = $this->userModel->findOrFail($id);
        
        // Não permite desativar o próprio usuário
        $currentUser = session()->get('user');
        if ($currentUser && $currentUser['id'] == $id) {
            return redirect()->to(route_to('super.users'))
                           ->with('warning', 'Você não pode desativar seu próprio usuário.');
        }
        
        $newStatus = $user->isActive() ? 0 : 1;
        $this->userModel->update($id, ['active' => $newStatus]);
        
        $message = $newStatus ? 'Usuário ativado!' : 'Usuário desativado!';
        
        return redirect()->to(route_to('super.users'))
                        ->with('success', $message);
    }

    // =========================================================================
    // CRUD - DELETE (Exclusão)
    // =========================================================================

    /**
     * Remove usuário (soft delete)
     * 
     * DELETE /super/users/(:num)
     */
    public function delete(int $id)
    {
        $user = $this->userModel->findOrFail($id);
        
        // Não permite excluir o próprio usuário
        $currentUser = session()->get('user');
        if ($currentUser && $currentUser['id'] == $id) {
            return redirect()->to(route_to('super.users'))
                           ->with('warning', 'Você não pode excluir seu próprio usuário.');
        }
        
        // Não permite excluir o último super admin
        if ($user->isSuper()) {
            $superCount = $this->userModel->where('role', 'super')
                                         ->where('id !=', $id)
                                         ->countAllResults();
            if ($superCount === 0) {
                return redirect()->to(route_to('super.users'))
                               ->with('warning', 'Não é possível excluir o último Super Admin.');
            }
        }
        
        $this->userModel->delete($id);
        
        return redirect()->to(route_to('super.users'))
                        ->with('success', 'Usuário excluído com sucesso!');
    }
}
