<?php

namespace App\Controllers\Api;

use App\Models\ProfessionalModel;

/**
 * ProfessionalsApiController - API REST para Profissionais
 */
class ProfessionalsApiController extends BaseApiController
{
    protected ProfessionalModel $professionalModel;

    public function __construct()
    {
        $this->professionalModel = model('ProfessionalModel');
    }

    /**
     * GET /api/v1/professionals
     * Lista todos os profissionais
     */
    public function index()
    {
        $unitId = $this->request->getGet('unit_id');
        $serviceId = $this->request->getGet('service_id');
        $onlyActive = $this->request->getGet('active') !== 'false';
        
        if ($unitId) {
            $this->professionalModel->where('unit_id', $unitId);
        }
        
        if ($onlyActive) {
            $this->professionalModel->where('active', 1);
        }
        
        $professionals = $this->professionalModel->findAll();
        
        // Filtrar por serviço se necessário
        if ($serviceId) {
            $professionals = array_filter($professionals, function($p) use ($serviceId) {
                return $p->hasService((int)$serviceId);
            });
            $professionals = array_values($professionals);
        }
        
        // Retorna no formato paginado para consistência
        return $this->respondSuccess([
            'items' => $professionals,
            'pagination' => [
                'current_page' => 1,
                'per_page' => count($professionals),
                'total_items' => count($professionals),
                'total_pages' => 1,
            ]
        ]);
    }

    /**
     * GET /api/v1/professionals/{id}
     * Retorna um profissional específico
     */
    public function show($id = null)
    {
        $professional = $this->professionalModel->find($id);
        
        if (!$professional) {
            return $this->respondError('Profissional não encontrado', 404);
        }
        
        return $this->respondSuccess($professional);
    }

    /**
     * POST /api/v1/professionals
     * Cria um novo profissional
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        if (!$this->professionalModel->insert($data)) {
            return $this->respondError('Erro ao criar profissional', 422, $this->professionalModel->errors());
        }
        
        $professional = $this->professionalModel->find($this->professionalModel->getInsertID());
        
        return $this->respondSuccess($professional, 'Profissional criado com sucesso', 201);
    }

    /**
     * PUT /api/v1/professionals/{id}
     * Atualiza um profissional
     */
    public function update($id = null)
    {
        $professional = $this->professionalModel->find($id);
        
        if (!$professional) {
            return $this->respondError('Profissional não encontrado', 404);
        }
        
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        if (!$this->professionalModel->update($id, $data)) {
            return $this->respondError('Erro ao atualizar profissional', 422, $this->professionalModel->errors());
        }
        
        $professional = $this->professionalModel->find($id);
        
        return $this->respondSuccess($professional, 'Profissional atualizado com sucesso');
    }

    /**
     * DELETE /api/v1/professionals/{id}
     * Remove um profissional
     */
    public function delete($id = null)
    {
        $professional = $this->professionalModel->find($id);
        
        if (!$professional) {
            return $this->respondError('Profissional não encontrado', 404);
        }
        
        $this->professionalModel->delete($id);
        
        return $this->respondSuccess(null, 'Profissional removido com sucesso');
    }

    /**
     * GET /api/v1/professionals/{id}/availability
     * Retorna disponibilidade do profissional
     */
    public function availability($id = null)
    {
        $professional = $this->professionalModel->find($id);
        
        if (!$professional) {
            return $this->respondError('Profissional não encontrado', 404);
        }
        
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        
        $appointmentModel = model('AppointmentModel');
        $appointments = $appointmentModel
            ->where('professional_id', $id)
            ->where('date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->findAll();
        
        $busySlots = [];
        foreach ($appointments as $apt) {
            $busySlots[] = [
                'start' => substr($apt->start_time, 0, 5),
                'end'   => substr($apt->end_time, 0, 5),
            ];
        }
        
        return $this->respondSuccess([
            'date'       => $date,
            'busy_slots' => $busySlots,
        ]);
    }
}
