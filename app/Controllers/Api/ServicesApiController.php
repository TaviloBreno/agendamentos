<?php

namespace App\Controllers\Api;

use App\Models\ServiceModel;

/**
 * ServicesApiController - API REST para Serviços
 */
class ServicesApiController extends BaseApiController
{
    protected ServiceModel $serviceModel;

    public function __construct()
    {
        $this->serviceModel = model('ServiceModel');
    }

    /**
     * GET /api/v1/services
     * Lista todos os serviços
     */
    public function index()
    {
        $unitId = $this->request->getGet('unit_id');
        $onlyActive = $this->request->getGet('active') !== 'false';
        
        if ($unitId) {
            $this->serviceModel->where('unit_id', $unitId);
        }
        
        if ($onlyActive) {
            $this->serviceModel->where('active', 1);
        }
        
        return $this->paginatedResponse($this->serviceModel);
    }

    /**
     * GET /api/v1/services/{id}
     * Retorna um serviço específico
     */
    public function show($id = null)
    {
        $service = $this->serviceModel->find($id);
        
        if (!$service) {
            return $this->respondError('Serviço não encontrado', 404);
        }
        
        return $this->respondSuccess($service);
    }

    /**
     * POST /api/v1/services
     * Cria um novo serviço
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        if (!$this->serviceModel->insert($data)) {
            return $this->respondError('Erro ao criar serviço', 422, $this->serviceModel->errors());
        }
        
        $service = $this->serviceModel->find($this->serviceModel->getInsertID());
        
        return $this->respondSuccess($service, 'Serviço criado com sucesso', 201);
    }

    /**
     * PUT /api/v1/services/{id}
     * Atualiza um serviço
     */
    public function update($id = null)
    {
        $service = $this->serviceModel->find($id);
        
        if (!$service) {
            return $this->respondError('Serviço não encontrado', 404);
        }
        
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        if (!$this->serviceModel->update($id, $data)) {
            return $this->respondError('Erro ao atualizar serviço', 422, $this->serviceModel->errors());
        }
        
        $service = $this->serviceModel->find($id);
        
        return $this->respondSuccess($service, 'Serviço atualizado com sucesso');
    }

    /**
     * DELETE /api/v1/services/{id}
     * Remove um serviço
     */
    public function delete($id = null)
    {
        $service = $this->serviceModel->find($id);
        
        if (!$service) {
            return $this->respondError('Serviço não encontrado', 404);
        }
        
        $this->serviceModel->delete($id);
        
        return $this->respondSuccess(null, 'Serviço removido com sucesso');
    }
}
