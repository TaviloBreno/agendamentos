<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Libraries\ClientService;
use App\Entities\Client;

/**
 * ClientsController - Gerenciamento de Clientes
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Controller responsável pelo CRUD completo de clientes no painel
 * administrativo. Utiliza ClientService para renderização de dados.
 * 
 * @package    App\Controllers\Super
 * @author     Sistema de Agendamentos
 */
class ClientsController extends BaseController
{
    /**
     * Model de clientes
     */
    protected ClientModel $clientModel;

    /**
     * Serviço de clientes
     */
    protected ClientService $clientService;

    /**
     * Construtor - Inicializa dependências
     */
    public function __construct()
    {
        $this->clientModel = model('ClientModel');
        $this->clientService = new ClientService();
    }

    // =========================================================================
    // CRUD - READ (Listagem)
    // =========================================================================

    /**
     * Lista todos os clientes
     * 
     * GET /super/clients
     */
    public function index()
    {
        $clients = $this->clientModel->orderBy('name', 'ASC')->findAll();
        
        return view('Back/Clients/index', [
            'title'   => 'Clientes',
            'clients' => $clients,
            'stats'   => $this->clientService->getStats(),
            'data'    => $this->clientService->renderClients($clients),
        ]);
    }

    // =========================================================================
    // CRUD - CREATE (Criação)
    // =========================================================================

    /**
     * Exibe formulário de novo cliente
     * 
     * GET /super/clients/new
     */
    public function new()
    {
        return view('Back/Clients/form', [
            'title'  => 'Novo Cliente',
            'client' => new Client(),
            'states' => Client::$states,
        ]);
    }

    /**
     * Processa criação de novo cliente
     * 
     * POST /super/clients
     */
    public function create()
    {
        $data = [
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'phone'      => $this->request->getPost('phone'),
            'cpf'        => $this->request->getPost('cpf'),
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'gender'     => $this->request->getPost('gender') ?: null,
            'address'    => $this->request->getPost('address'),
            'city'       => $this->request->getPost('city'),
            'state'      => $this->request->getPost('state') ?: null,
            'zip_code'   => $this->request->getPost('zip_code'),
            'notes'      => $this->request->getPost('notes'),
            'active'     => $this->request->getPost('active') ?? 1,
        ];

        if (! $this->clientModel->insert($data)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->clientModel->errors());
        }

        return redirect()->to(route_to('super.clients'))
                        ->with('success', 'Cliente cadastrado com sucesso!');
    }

    // =========================================================================
    // CRUD - READ (Detalhes)
    // =========================================================================

    /**
     * Exibe detalhes de um cliente
     * 
     * GET /super/clients/(:num)
     */
    public function show(int $id)
    {
        $client = $this->clientModel->findOrFail($id);
        $recentAppointments = $client->recentAppointments(10);
        
        return view('Back/Clients/show', [
            'title'              => 'Cliente: ' . $client->name,
            'client'             => $client,
            'recentAppointments' => $recentAppointments,
        ]);
    }

    // =========================================================================
    // CRUD - UPDATE (Edição)
    // =========================================================================

    /**
     * Exibe formulário de edição
     * 
     * GET /super/clients/(:num)/edit
     */
    public function edit(int $id)
    {
        $client = $this->clientModel->findOrFail($id);
        
        return view('Back/Clients/edit', [
            'title'  => 'Editar Cliente',
            'client' => $client,
            'states' => Client::$states,
        ]);
    }

    /**
     * Processa atualização do cliente
     * 
     * PUT /super/clients/(:num)
     */
    public function update(int $id)
    {
        $client = $this->clientModel->findOrFail($id);
        
        $data = [
            'id'         => $id,
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'phone'      => $this->request->getPost('phone'),
            'cpf'        => $this->request->getPost('cpf'),
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'gender'     => $this->request->getPost('gender') ?: null,
            'address'    => $this->request->getPost('address'),
            'city'       => $this->request->getPost('city'),
            'state'      => $this->request->getPost('state') ?: null,
            'zip_code'   => $this->request->getPost('zip_code'),
            'notes'      => $this->request->getPost('notes'),
            'active'     => $this->request->getPost('active') ?? $client->active,
        ];

        if (! $this->clientModel->update($id, $data)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->clientModel->errors());
        }

        return redirect()->to(route_to('super.clients'))
                        ->with('success', 'Cliente atualizado com sucesso!');
    }

    // =========================================================================
    // ACTIONS (Toggle Status)
    // =========================================================================

    /**
     * Alterna status ativo/inativo do cliente
     * 
     * PUT /super/clients/(:num)/action
     */
    public function action(int $id)
    {
        $client = $this->clientModel->findOrFail($id);
        
        $newStatus = $client->isActive() ? 0 : 1;
        $this->clientModel->update($id, ['active' => $newStatus]);
        
        $message = $newStatus ? 'Cliente ativado!' : 'Cliente desativado!';
        
        return redirect()->to(route_to('super.clients'))
                        ->with('success', $message);
    }

    // =========================================================================
    // CRUD - DELETE (Exclusão)
    // =========================================================================

    /**
     * Remove cliente (soft delete)
     * 
     * DELETE /super/clients/(:num)
     */
    public function delete(int $id)
    {
        $client = $this->clientModel->findOrFail($id);
        
        // Verifica se o cliente tem agendamentos
        if ($client->appointmentsCount() > 0) {
            return redirect()->to(route_to('super.clients'))
                           ->with('warning', 'Não é possível excluir um cliente com agendamentos. Desative-o ao invés disso.');
        }
        
        $this->clientModel->delete($id);
        
        return redirect()->to(route_to('super.clients'))
                        ->with('success', 'Cliente excluído com sucesso!');
    }

    // =========================================================================
    // API ENDPOINTS
    // =========================================================================

    /**
     * Busca clientes para autocomplete (JSON)
     * 
     * GET /super/clients/search?q=termo
     */
    public function search()
    {
        $query = $this->request->getGet('q');
        
        if (empty($query) || strlen($query) < 2) {
            return $this->response->setJSON([]);
        }
        
        $clients = $this->clientModel
            ->like('name', $query, 'both')
            ->orLike('email', $query, 'both')
            ->where('active', 1)
            ->orderBy('name', 'ASC')
            ->findAll(10);
        
        $results = [];
        foreach ($clients as $client) {
            $results[] = [
                'id'    => $client->id,
                'text'  => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
            ];
        }
        
        return $this->response->setJSON($results);
    }
}
