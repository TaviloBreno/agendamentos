<?php

namespace App\Libraries;

use App\Entities\Client;
use App\Models\ClientModel;

/**
 * ClientService - Serviço de renderização para Clientes
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Esta classe herda de MyBaseService e fornece métodos específicos
 * para renderização de dados de clientes no painel administrativo.
 * 
 * @package    App\Libraries
 * @author     Sistema de Agendamentos
 */
class ClientService extends MyBaseService
{
    /**
     * Model de clientes
     */
    protected ClientModel $clientModel;

    /**
     * Construtor - Inicializa o model
     */
    public function __construct()
    {
        $this->clientModel = model('ClientModel');
    }

    // =========================================================================
    // RENDERIZAÇÃO DE TABELA
    // =========================================================================

    /**
     * Renderiza array de clientes para formato de tabela DataTables
     * 
     * @param array<Client> $clients Array de entidades Client
     * @return array Dados formatados para DataTables
     */
    public function renderClients(array $clients): array
    {
        $data = [];
        
        foreach ($clients as $client) {
            $data[] = [
                'id'           => $client->id,
                'client'       => $this->renderClientCell($client),
                'contact'      => $this->renderContactCell($client),
                'location'     => $this->renderLocationCell($client),
                'appointments' => $this->renderAppointmentsCell($client),
                'status'       => $client->statusBadge(),
                'created_at'   => $this->formatDate($client->created_at),
                'actions'      => $this->renderBtnActions($client),
            ];
        }
        
        return $data;
    }

    /**
     * Renderiza célula com dados do cliente (avatar + nome + email)
     */
    public function renderClientCell(Client $client): string
    {
        $initials = $client->initials();
        $avatarUrl = $client->avatarUrl(40);
        
        $html = '<div class="d-flex align-items-center">';
        $html .= '<img src="' . $avatarUrl . '" alt="' . esc($client->name) . '" class="rounded-circle mr-2" width="40" height="40">';
        $html .= '<div>';
        $html .= '<div class="font-weight-bold">' . esc($client->name) . '</div>';
        $html .= '<small class="text-muted">' . esc($client->email) . '</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Renderiza célula de contato (telefone + cpf)
     */
    public function renderContactCell(Client $client): string
    {
        $html = '<div>';
        
        if ($client->phone) {
            $html .= '<div><i class="fas fa-phone text-muted mr-1"></i>' . esc($client->phoneFormatted()) . '</div>';
        }
        
        if ($client->cpf) {
            $html .= '<small class="text-muted"><i class="fas fa-id-card mr-1"></i>' . esc($client->cpfFormatted()) . '</small>';
        }
        
        if (!$client->phone && !$client->cpf) {
            $html .= '<span class="text-muted">-</span>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Renderiza célula de localização
     */
    public function renderLocationCell(Client $client): string
    {
        if (!$client->hasAddress()) {
            return '<span class="text-muted">-</span>';
        }
        
        $html = '<div>';
        
        if ($client->city) {
            $html .= '<div>' . esc($client->city);
            if ($client->state) {
                $html .= '/' . esc($client->state);
            }
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Renderiza célula de agendamentos
     */
    public function renderAppointmentsCell(Client $client): string
    {
        $count = $client->appointmentsCount();
        
        if ($count === 0) {
            return '<span class="badge badge-secondary">Nenhum</span>';
        }
        
        $class = $count > 5 ? 'badge-success' : 'badge-info';
        return '<span class="badge ' . $class . '">' . $count . ' agendamento' . ($count > 1 ? 's' : '') . '</span>';
    }

    /**
     * Renderiza botões de ação para cada cliente
     */
    public function renderBtnActions(Client $client): string
    {
        $buttons = $this->renderBtnView($client);
        $buttons .= $this->renderBtnEdit($client);
        $buttons .= $this->renderBtnToggle($client);
        $buttons .= $this->renderBtnDelete($client);
        
        return '<div class="btn-group btn-group-sm" role="group">' . $buttons . '</div>';
    }

    /**
     * Botão visualizar
     */
    protected function renderBtnView(Client $client): string
    {
        $url = route_to('super.clients.show', $client->id);
        return '<a href="' . $url . '" class="btn btn-info btn-sm" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </a>';
    }

    /**
     * Botão editar
     */
    protected function renderBtnEdit(Client $client): string
    {
        $url = route_to('super.clients.edit', $client->id);
        return '<a href="' . $url . '" class="btn btn-primary btn-sm" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>';
    }

    /**
     * Botão toggle status
     */
    protected function renderBtnToggle(Client $client): string
    {
        $url = route_to('super.clients.action', $client->id);
        $icon = $client->isActive() ? 'fa-ban' : 'fa-check';
        $class = $client->isActive() ? 'btn-warning' : 'btn-success';
        $title = $client->isActive() ? 'Desativar' : 'Ativar';
        
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
    protected function renderBtnDelete(Client $client): string
    {
        $url = route_to('super.clients.delete', $client->id);
        
        return '<form action="' . $url . '" method="POST" class="d-inline" 
                      onsubmit="return confirm(\'Tem certeza que deseja excluir este cliente?\')">
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
     * Retorna estatísticas dos clientes
     */
    public function getStats(): array
    {
        $counts = $this->clientModel->countByStatus();
        $birthdaysToday = count($this->clientModel->getBirthdaysToday());
        
        return [
            'total'          => $counts['total'],
            'active'         => $counts['active'],
            'inactive'       => $counts['inactive'],
            'birthdays_today' => $birthdaysToday,
        ];
    }
}
