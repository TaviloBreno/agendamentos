<?php

namespace App\Controllers\Api;

use App\Models\UnitModel;

/**
 * UnitsApiController - API REST para Unidades
 */
class UnitsApiController extends BaseApiController
{
    protected UnitModel $unitModel;

    public function __construct()
    {
        $this->unitModel = model('UnitModel');
    }

    /**
     * GET /api/v1/units
     * Lista todas as unidades
     */
    public function index()
    {
        $onlyActive = $this->request->getGet('active') !== 'false';
        
        if ($onlyActive) {
            $this->unitModel->where('active', 1);
        }
        
        return $this->paginatedResponse($this->unitModel);
    }

    /**
     * GET /api/v1/units/{id}
     * Retorna uma unidade específica
     */
    public function show($id = null)
    {
        $unit = $this->unitModel->find($id);
        
        if (!$unit) {
            return $this->respondError('Unidade não encontrada', 404);
        }
        
        return $this->respondSuccess($unit);
    }

    /**
     * POST /api/v1/units
     * Cria uma nova unidade
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        if (!$this->unitModel->insert($data)) {
            return $this->respondError('Erro ao criar unidade', 422, $this->unitModel->errors());
        }
        
        $unit = $this->unitModel->find($this->unitModel->getInsertID());
        
        return $this->respondSuccess($unit, 'Unidade criada com sucesso', 201);
    }

    /**
     * PUT /api/v1/units/{id}
     * Atualiza uma unidade
     */
    public function update($id = null)
    {
        $unit = $this->unitModel->find($id);
        
        if (!$unit) {
            return $this->respondError('Unidade não encontrada', 404);
        }
        
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        if (!$this->unitModel->update($id, $data)) {
            return $this->respondError('Erro ao atualizar unidade', 422, $this->unitModel->errors());
        }
        
        $unit = $this->unitModel->find($id);
        
        return $this->respondSuccess($unit, 'Unidade atualizada com sucesso');
    }

    /**
     * DELETE /api/v1/units/{id}
     * Remove uma unidade
     */
    public function delete($id = null)
    {
        $unit = $this->unitModel->find($id);
        
        if (!$unit) {
            return $this->respondError('Unidade não encontrada', 404);
        }
        
        $this->unitModel->delete($id);
        
        return $this->respondSuccess(null, 'Unidade removida com sucesso');
    }
}
