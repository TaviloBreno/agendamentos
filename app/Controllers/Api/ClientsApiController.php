<?php

namespace App\Controllers\Api;

use App\Models\ClientModel;

/**
 * ClientsApiController - API REST para Clientes
 */
class ClientsApiController extends BaseApiController
{
    protected ClientModel $clientModel;

    public function __construct()
    {
        $this->clientModel = model('ClientModel');
    }

    /**
     * GET /api/v1/clients
     * Lista clientes com filtros e paginação
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 20);
        
        $builder = $this->clientModel;
        
        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->orLike('cpf', $search)
            ->groupEnd();
        }
        
        $clients = $builder
            ->orderBy('name', 'ASC')
            ->paginate($perPage, 'default', $page);
        
        return $this->paginatedResponse(
            $clients,
            $builder->pager->getTotal(),
            $page,
            $perPage
        );
    }

    /**
     * GET /api/v1/clients/{id}
     * Retorna um cliente específico
     */
    public function show($id = null)
    {
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            return $this->respondError('Cliente não encontrado', 404);
        }
        
        return $this->respondSuccess($client);
    }

    /**
     * POST /api/v1/clients
     * Cria um novo cliente
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Verificar se email já existe
        if (isset($data['email'])) {
            $existing = $this->clientModel->where('email', $data['email'])->first();
            if ($existing) {
                return $this->respondError('Email já cadastrado', 409);
            }
        }
        
        // Verificar se CPF já existe
        if (isset($data['cpf'])) {
            $existing = $this->clientModel->where('cpf', $data['cpf'])->first();
            if ($existing) {
                return $this->respondError('CPF já cadastrado', 409);
            }
        }
        
        if (!$this->clientModel->insert($data)) {
            return $this->respondError('Erro ao criar cliente', 422, $this->clientModel->errors());
        }
        
        $client = $this->clientModel->find($this->clientModel->getInsertID());
        
        return $this->respondSuccess($client, 'Cliente criado com sucesso', 201);
    }

    /**
     * PUT /api/v1/clients/{id}
     * Atualiza um cliente
     */
    public function update($id = null)
    {
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            return $this->respondError('Cliente não encontrado', 404);
        }
        
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        // Verificar email duplicado (exceto o próprio cliente)
        if (isset($data['email'])) {
            $existing = $this->clientModel
                ->where('email', $data['email'])
                ->where('id !=', $id)
                ->first();
            if ($existing) {
                return $this->respondError('Email já cadastrado para outro cliente', 409);
            }
        }
        
        // Verificar CPF duplicado
        if (isset($data['cpf'])) {
            $existing = $this->clientModel
                ->where('cpf', $data['cpf'])
                ->where('id !=', $id)
                ->first();
            if ($existing) {
                return $this->respondError('CPF já cadastrado para outro cliente', 409);
            }
        }
        
        if (!$this->clientModel->update($id, $data)) {
            return $this->respondError('Erro ao atualizar cliente', 422, $this->clientModel->errors());
        }
        
        $client = $this->clientModel->find($id);
        
        return $this->respondSuccess($client, 'Cliente atualizado com sucesso');
    }

    /**
     * DELETE /api/v1/clients/{id}
     * Remove um cliente
     */
    public function delete($id = null)
    {
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            return $this->respondError('Cliente não encontrado', 404);
        }
        
        // Verificar se tem agendamentos
        $appointmentModel = model('AppointmentModel');
        $appointments = $appointmentModel->where('client_id', $id)->countAllResults();
        
        if ($appointments > 0) {
            return $this->respondError(
                'Cliente possui agendamentos e não pode ser removido. Considere desativá-lo.',
                409
            );
        }
        
        $this->clientModel->delete($id);
        
        return $this->respondSuccess(null, 'Cliente removido com sucesso');
    }

    /**
     * GET /api/v1/clients/{id}/appointments
     * Lista agendamentos do cliente
     */
    public function appointments($id = null)
    {
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            return $this->respondError('Cliente não encontrado', 404);
        }
        
        $status = $this->request->getGet('status');
        $upcoming = $this->request->getGet('upcoming');
        
        $appointmentModel = model('AppointmentModel');
        $builder = $appointmentModel->where('client_id', $id);
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        if ($upcoming === 'true') {
            $builder->where('date >=', date('Y-m-d'));
        }
        
        $appointments = $builder
            ->orderBy('date', 'DESC')
            ->orderBy('start_time', 'DESC')
            ->findAll();
        
        return $this->respondSuccess($appointments);
    }

    /**
     * GET /api/v1/clients/search
     * Busca rápida de clientes
     */
    public function search()
    {
        $term = $this->request->getGet('q');
        
        if (!$term || strlen($term) < 2) {
            return $this->respondSuccess([]);
        }
        
        $clients = $this->clientModel
            ->groupStart()
                ->like('name', $term)
                ->orLike('email', $term)
                ->orLike('phone', $term)
            ->groupEnd()
            ->orderBy('name', 'ASC')
            ->limit(10)
            ->findAll();
        
        return $this->respondSuccess($clients);
    }
}
