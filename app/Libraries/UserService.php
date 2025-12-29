<?php

namespace App\Libraries;

use App\Entities\User;
use App\Models\UserModel;

/**
 * UserService - Serviço de renderização para Usuários
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Esta classe herda de MyBaseService e fornece métodos específicos
 * para renderização de dados de usuários no painel administrativo.
 * 
 * @package    App\Libraries
 * @author     Sistema de Agendamentos
 */
class UserService extends MyBaseService
{
    /**
     * Model de usuários
     */
    protected UserModel $userModel;

    /**
     * Construtor - Inicializa o model
     */
    public function __construct()
    {
        $this->userModel = model('UserModel');
    }

    // =========================================================================
    // RENDERIZAÇÃO DE TABELA
    // =========================================================================

    /**
     * Renderiza array de usuários para formato de tabela DataTables
     * 
     * @param array<User> $users Array de entidades User
     * @return array Dados formatados para DataTables
     */
    public function renderUsers(array $users): array
    {
        $data = [];
        
        foreach ($users as $user) {
            $data[] = [
                'id'         => $user->id,
                'user'       => $this->renderUserCell($user),
                'email'      => esc($user->email),
                'role'       => $user->roleBadge(),
                'status'     => $user->statusBadge(),
                'created_at' => $this->formatDate($user->created_at),
                'actions'    => $this->renderBtnActions($user),
            ];
        }
        
        return $data;
    }

    /**
     * Renderiza célula com dados do usuário (avatar + nome)
     */
    public function renderUserCell(User $user): string
    {
        $initials = $user->initials();
        $avatarUrl = $user->avatarUrl(40);
        
        $html = '<div class="d-flex align-items-center">';
        $html .= '<img src="' . $avatarUrl . '" alt="' . esc($user->name) . '" class="rounded-circle mr-2" width="40" height="40">';
        $html .= '<div>';
        $html .= '<div class="font-weight-bold">' . esc($user->name) . '</div>';
        $html .= '<small class="text-muted">' . esc($user->email) . '</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Renderiza botões de ação para cada usuário
     */
    public function renderBtnActions(User $user): string
    {
        // Não permite ações no próprio usuário logado
        $currentUser = session()->get('user');
        $isSelf = $currentUser && $currentUser['id'] == $user->id;
        
        $buttons = $this->renderBtnView($user);
        $buttons .= $this->renderBtnEdit($user);
        
        if (!$isSelf) {
            $buttons .= $this->renderBtnToggle($user);
            $buttons .= $this->renderBtnDelete($user);
        }
        
        return '<div class="btn-group btn-group-sm" role="group">' . $buttons . '</div>';
    }

    /**
     * Botão visualizar
     */
    protected function renderBtnView(User $user): string
    {
        $url = route_to('super.users.show', $user->id);
        return '<a href="' . $url . '" class="btn btn-info btn-sm" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </a>';
    }

    /**
     * Botão editar
     */
    protected function renderBtnEdit(User $user): string
    {
        $url = route_to('super.users.edit', $user->id);
        return '<a href="' . $url . '" class="btn btn-primary btn-sm" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>';
    }

    /**
     * Botão toggle status
     */
    protected function renderBtnToggle(User $user): string
    {
        $url = route_to('super.users.action', $user->id);
        $icon = $user->isActive() ? 'fa-ban' : 'fa-check';
        $class = $user->isActive() ? 'btn-warning' : 'btn-success';
        $title = $user->isActive() ? 'Desativar' : 'Ativar';
        
        return '<form action="' . $url . '" method="POST" class="d-inline">
                    ' . csrf_field() . '
                    <input type="hidden" name="_method" value="PUT">
                    <button type="submit" class="btn ' . $class . ' btn-sm" title="' . $title . '">
                        <i class="fas ' . $icon . '"></i>
                    </button>
                </form>';
    }

    /**
     * Botão excluir
     */
    protected function renderBtnDelete(User $user): string
    {
        $url = route_to('super.users.delete', $user->id);
        
        return '<form action="' . $url . '" method="POST" class="d-inline" 
                      onsubmit="return confirm(\'Tem certeza que deseja excluir este usuário?\')">
                    ' . csrf_field() . '
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';
    }

    // =========================================================================
    // ESTATÍSTICAS
    // =========================================================================

    /**
     * Retorna estatísticas dos usuários
     */
    public function getStats(): array
    {
        $total = $this->userModel->countAllResults(false);
        $active = $this->userModel->where('active', 1)->countAllResults(false);
        $inactive = $this->userModel->where('active', 0)->countAllResults(false);
        
        $superCount = $this->userModel->where('role', 'super')->countAllResults(false);
        $adminCount = $this->userModel->where('role', 'admin')->countAllResults(false);
        $userCount = $this->userModel->where('role', 'user')->countAllResults(false);
        
        return [
            'total'    => $total,
            'active'   => $active,
            'inactive' => $inactive,
            'by_role'  => [
                'super' => $superCount,
                'admin' => $adminCount,
                'user'  => $userCount,
            ],
        ];
    }
}
