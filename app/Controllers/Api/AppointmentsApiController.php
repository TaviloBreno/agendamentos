<?php

namespace App\Controllers\Api;

use App\Models\AppointmentModel;
use App\Libraries\AppointmentService;
use App\Libraries\NotificationService;

/**
 * AppointmentsApiController - API REST para Agendamentos
 */
class AppointmentsApiController extends BaseApiController
{
    protected AppointmentModel $appointmentModel;
    protected AppointmentService $appointmentService;

    public function __construct()
    {
        $this->appointmentModel = model('AppointmentModel');
        $this->appointmentService = new AppointmentService();
    }

    /**
     * GET /api/v1/appointments
     * Lista agendamentos com filtros
     */
    public function index()
    {
        $unitId = $this->request->getGet('unit_id');
        $professionalId = $this->request->getGet('professional_id');
        $clientId = $this->request->getGet('client_id');
        $date = $this->request->getGet('date');
        $dateStart = $this->request->getGet('date_start');
        $dateEnd = $this->request->getGet('date_end');
        $status = $this->request->getGet('status');
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 20);
        
        $builder = $this->appointmentModel;
        
        if ($unitId) {
            $builder->where('unit_id', $unitId);
        }
        
        if ($professionalId) {
            $builder->where('professional_id', $professionalId);
        }
        
        if ($clientId) {
            $builder->where('client_id', $clientId);
        }
        
        if ($date) {
            $builder->where('date', $date);
        } else {
            if ($dateStart) {
                $builder->where('date >=', $dateStart);
            }
            if ($dateEnd) {
                $builder->where('date <=', $dateEnd);
            }
        }
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        $appointments = $builder
            ->orderBy('date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->paginate($perPage, 'default', $page);
        
        return $this->paginatedResponse(
            $appointments,
            $builder->pager->getTotal(),
            $page,
            $perPage
        );
    }

    /**
     * GET /api/v1/appointments/{id}
     * Retorna um agendamento específico
     */
    public function show($id = null)
    {
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return $this->respondError('Agendamento não encontrado', 404);
        }
        
        return $this->respondSuccess($appointment);
    }

    /**
     * POST /api/v1/appointments
     * Cria um novo agendamento
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Calcular horário de término
        if (!isset($data['end_time']) && isset($data['start_time']) && isset($data['service_id'])) {
            $serviceModel = model('ServiceModel');
            $service = $serviceModel->find($data['service_id']);
            if ($service) {
                $startTime = strtotime($data['start_time']);
                $endTime = $startTime + ($service->duration * 60);
                $data['end_time'] = date('H:i:s', $endTime);
            }
        }
        
        // Validar disponibilidade
        if (isset($data['professional_id'], $data['date'], $data['start_time'], $data['end_time'])) {
            if (!$this->appointmentService->isSlotAvailable(
                $data['professional_id'],
                $data['date'],
                $data['start_time'],
                $data['end_time']
            )) {
                return $this->respondError('Horário não disponível', 409);
            }
        }
        
        $data['status'] = $data['status'] ?? 'scheduled';
        
        if (!$this->appointmentModel->insert($data)) {
            return $this->respondError('Erro ao criar agendamento', 422, $this->appointmentModel->errors());
        }
        
        $appointment = $this->appointmentModel->find($this->appointmentModel->getInsertID());
        
        // Enviar confirmação por email
        try {
            $notification = new NotificationService();
            $notification->sendAppointmentConfirmation($appointment);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar notificação: ' . $e->getMessage());
        }
        
        return $this->respondSuccess($appointment, 'Agendamento criado com sucesso', 201);
    }

    /**
     * PUT /api/v1/appointments/{id}
     * Atualiza um agendamento
     */
    public function update($id = null)
    {
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return $this->respondError('Agendamento não encontrado', 404);
        }
        
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $oldStatus = $appointment->status;
        
        if (!$this->appointmentModel->update($id, $data)) {
            return $this->respondError('Erro ao atualizar agendamento', 422, $this->appointmentModel->errors());
        }
        
        $appointment = $this->appointmentModel->find($id);
        
        // Notificar mudança de status
        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            try {
                $notification = new NotificationService();
                
                if ($data['status'] === 'cancelled') {
                    $notification->sendAppointmentCancellation($appointment);
                } else {
                    $notification->sendStatusChange($appointment, $oldStatus);
                }
            } catch (\Exception $e) {
                log_message('error', 'Erro ao enviar notificação: ' . $e->getMessage());
            }
        }
        
        return $this->respondSuccess($appointment, 'Agendamento atualizado com sucesso');
    }

    /**
     * DELETE /api/v1/appointments/{id}
     * Remove/cancela um agendamento
     */
    public function delete($id = null)
    {
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return $this->respondError('Agendamento não encontrado', 404);
        }
        
        // Cancelar ao invés de deletar
        $this->appointmentModel->update($id, ['status' => 'cancelled']);
        
        // Notificar cancelamento
        try {
            $appointment->status = 'cancelled';
            $notification = new NotificationService();
            $notification->sendAppointmentCancellation($appointment);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar notificação: ' . $e->getMessage());
        }
        
        return $this->respondSuccess(null, 'Agendamento cancelado com sucesso');
    }

    /**
     * GET /api/v1/appointments/available-slots
     * Retorna horários disponíveis
     */
    public function availableSlots()
    {
        $professionalId = $this->request->getGet('professional_id');
        $date = $this->request->getGet('date');
        $serviceId = $this->request->getGet('service_id');
        
        if (!$professionalId || !$date) {
            return $this->respondError('professional_id e date são obrigatórios', 400);
        }
        
        $duration = 30; // minutos padrão
        
        if ($serviceId) {
            $serviceModel = model('ServiceModel');
            $service = $serviceModel->find($serviceId);
            if ($service) {
                $duration = $service->duration;
            }
        }
        
        $slots = $this->appointmentService->getAvailableSlots($professionalId, $date, $duration);
        
        return $this->respondSuccess([
            'date' => $date,
            'duration_minutes' => $duration,
            'available_slots' => $slots,
        ]);
    }

    /**
     * POST /api/v1/appointments/{id}/confirm
     * Confirma um agendamento
     */
    public function confirm($id = null)
    {
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return $this->respondError('Agendamento não encontrado', 404);
        }
        
        $this->appointmentModel->update($id, ['status' => 'confirmed']);
        $appointment = $this->appointmentModel->find($id);
        
        return $this->respondSuccess($appointment, 'Agendamento confirmado com sucesso');
    }

    /**
     * POST /api/v1/appointments/{id}/complete
     * Marca agendamento como concluído
     */
    public function complete($id = null)
    {
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return $this->respondError('Agendamento não encontrado', 404);
        }
        
        $this->appointmentModel->update($id, ['status' => 'completed']);
        $appointment = $this->appointmentModel->find($id);
        
        return $this->respondSuccess($appointment, 'Agendamento concluído com sucesso');
    }

    /**
     * POST /api/v1/appointments/{id}/cancel
     * Cancela um agendamento
     */
    public function cancel($id = null)
    {
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return $this->respondError('Agendamento não encontrado', 404);
        }
        
        $data = $this->request->getJSON(true) ?? [];
        $reason = $data['reason'] ?? null;
        
        $updateData = ['status' => 'cancelled'];
        if ($reason) {
            $updateData['notes'] = ($appointment->notes ? $appointment->notes . "\n" : '') . 
                                   "Motivo do cancelamento: {$reason}";
        }
        
        $this->appointmentModel->update($id, $updateData);
        
        // Notificar cancelamento
        try {
            $appointment->status = 'cancelled';
            $notification = new NotificationService();
            $notification->sendAppointmentCancellation($appointment, $reason);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar notificação: ' . $e->getMessage());
        }
        
        $appointment = $this->appointmentModel->find($id);
        
        return $this->respondSuccess($appointment, 'Agendamento cancelado com sucesso');
    }
}
