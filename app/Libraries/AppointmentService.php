<?php

namespace App\Libraries;

use App\Models\AppointmentModel;
use App\Models\NotificationQueueModel;
use App\Entities\Appointment;

/**
 * AppointmentService - Service para lógica de negócio de Agendamentos
 * 
 * =========================================================================
 * RESPONSABILIDADES
 * =========================================================================
 * 
 * - Montar tabelas HTML para listagem
 * - Formatar dados para calendário
 * - Centralizar queries específicas
 * - Verificar disponibilidade
 * - Enviar notificações via fila
 * 
 * @package    App\Libraries
 */
class AppointmentService extends MyBaseService
{
    protected NotificationQueueModel $queueModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->queueModel = model(NotificationQueueModel::class);
    }
    
    /**
     * Renderiza a tabela de Agendamentos para listagem
     * 
     * @param array $appointments Array de Appointment entities
     * @return string HTML da tabela
     */
    public function renderAppointments(array $appointments = []): string
    {
        if (empty($appointments)) {
            $appointmentModel = new AppointmentModel();
            $appointments = $appointmentModel->getWithRelations(100);
        }

        // Configuração da Table Class (herdada da MyBaseService)
        $this->htmlTable->setHeading([
            'ID',
            'Data/Hora',
            'Cliente',
            'Profissional',
            'Serviço',
            'Status',
            'Ações',
        ]);

        // Popula a tabela com os dados
        foreach ($appointments as $appointment) {
            $this->htmlTable->addRow([
                $appointment->id,
                $this->renderDateTimeCell($appointment),
                $this->renderClientCell($appointment),
                esc($appointment->professional_name ?? '-'),
                esc($appointment->service_name ?? '-'),
                $appointment->statusBadge(),
                $this->renderBtnActions($appointment),
            ]);
        }

        return $this->htmlTable->generate();
    }

    /**
     * Renderiza célula de data/hora
     * 
     * @param Appointment $appointment
     * @return string HTML
     */
    protected function renderDateTimeCell(Appointment $appointment): string
    {
        $html = '<strong>' . $appointment->dateFormatted() . '</strong>';
        $html .= '<br><small class="text-muted">' . $appointment->timeRange() . '</small>';
        
        if ($appointment->isToday()) {
            $html .= ' <span class="badge badge-primary badge-sm">Hoje</span>';
        }
        
        return $html;
    }

    /**
     * Renderiza célula do cliente
     * 
     * @param Appointment $appointment
     * @return string HTML
     */
    protected function renderClientCell(Appointment $appointment): string
    {
        $html = '<strong>' . esc($appointment->client_name) . '</strong>';
        $html .= '<br><small class="text-muted">';
        $html .= '<i class="fas fa-envelope fa-fw"></i> ' . esc($appointment->client_email);
        if ($appointment->client_phone) {
            $html .= '<br><i class="fas fa-phone fa-fw"></i> ' . esc($appointment->client_phone);
        }
        $html .= '</small>';
        
        return $html;
    }

    /**
     * Renderiza os botões de ação para cada agendamento
     * 
     * @param Appointment $appointment
     * @return string HTML dos botões
     */
    public function renderBtnActions(Appointment $appointment): string
    {
        $html = '<div class="btn-group">';
        $html .= '<a href="' . route_to('super.appointments.show', $appointment->id) . '" class="btn btn-sm btn-info">';
        $html .= '<i class="fas fa-eye"></i>';
        $html .= '</a>';
        $html .= '<button type="button" class="btn btn-sm btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false">';
        $html .= '<span class="sr-only">Toggle Dropdown</span>';
        $html .= '</button>';
        $html .= '<div class="dropdown-menu dropdown-menu-right">';
        
        // Link Editar (se puder editar)
        if ($appointment->canEdit()) {
            $html .= '<a class="dropdown-item" href="' . route_to('super.appointments.edit', $appointment->id) . '">';
            $html .= '<i class="fas fa-edit fa-fw mr-2 text-primary"></i> Editar';
            $html .= '</a>';
        }
        
        // Confirmar (se puder)
        if ($appointment->canConfirm()) {
            $html .= '<a class="dropdown-item" href="' . route_to('super.appointments.status', $appointment->id) . '?status=confirmed">';
            $html .= '<i class="fas fa-check-circle fa-fw mr-2 text-info"></i> Confirmar';
            $html .= '</a>';
        }
        
        // Concluir (se puder)
        if ($appointment->canComplete()) {
            $html .= '<a class="dropdown-item" href="' . route_to('super.appointments.status', $appointment->id) . '?status=completed">';
            $html .= '<i class="fas fa-check-double fa-fw mr-2 text-success"></i> Concluir';
            $html .= '</a>';
        }
        
        $html .= '<div class="dropdown-divider"></div>';
        
        // Cancelar (se puder)
        if ($appointment->canCancel()) {
            $html .= '<a class="dropdown-item text-warning" href="' . route_to('super.appointments.status', $appointment->id) . '?status=cancelled" onclick="return confirm(\'Deseja cancelar este agendamento?\');">';
            $html .= '<i class="fas fa-times-circle fa-fw mr-2"></i> Cancelar';
            $html .= '</a>';
        }
        
        $html .= '</div>'; // dropdown-menu
        $html .= '</div>'; // btn-group

        return $html;
    }

    /**
     * Retorna dados para cards de estatísticas
     * 
     * @param int|null $unitId Filtrar por unidade
     * @return array
     */
    public function getStats(?int $unitId = null): array
    {
        $model = new AppointmentModel();
        
        if ($unitId) {
            $model = $model->where('unit_id', $unitId);
        }
        
        $todayCounts = $model->countByStatus(date('Y-m-d'));
        
        // Reset para pegar total geral
        $model = new AppointmentModel();
        if ($unitId) {
            $model = $model->where('unit_id', $unitId);
        }
        $totalCounts = $model->countByStatus();
        
        return [
            'today' => [
                'total'     => $todayCounts['total'],
                'scheduled' => $todayCounts['scheduled'],
                'confirmed' => $todayCounts['confirmed'],
                'completed' => $todayCounts['completed'],
            ],
            'all' => [
                'total'     => $totalCounts['total'],
                'scheduled' => $totalCounts['scheduled'],
                'completed' => $totalCounts['completed'],
                'cancelled' => $totalCounts['cancelled'],
            ],
        ];
    }

    /**
     * Retorna dropdown de status
     * 
     * @param string|null $selected
     * @return string HTML
     */
    public function renderStatusDropdown(?string $selected = null): string
    {
        $options = Appointment::getStatusOptions();
        
        $html = '<select name="status" id="status" class="form-control" required>';
        $html .= '<option value="">Selecione o status...</option>';
        
        foreach ($options as $value => $label) {
            $isSelected = ($selected === $value) ? 'selected' : '';
            $html .= '<option value="' . $value . '" ' . $isSelected . '>' . esc($label) . '</option>';
        }
        
        $html .= '</select>';
        
        return $html;
    }
    
    // =========================================================================
    // NOTIFICAÇÕES
    // =========================================================================
    
    /**
     * Agenda notificação de confirmação de agendamento
     * 
     * @param Appointment $appointment
     */
    public function sendConfirmationNotification(Appointment $appointment): void
    {
        if (!$appointment->client_phone) {
            return;
        }
        
        $message = $this->buildConfirmationMessage($appointment);
        $this->queueModel->queueWhatsApp($appointment->client_phone, $message, [
            'appointment_id' => $appointment->id,
            'type' => 'confirmation',
        ]);
    }
    
    /**
     * Agenda lembrete para 24h antes do agendamento
     * 
     * @param Appointment $appointment
     */
    public function sendReminderNotification(Appointment $appointment): void
    {
        if (!$appointment->client_phone) {
            return;
        }
        
        // Agendar para 24h antes
        $appointmentDateTime = strtotime($appointment->date . ' ' . $appointment->start_time);
        $reminderTime = date('Y-m-d H:i:s', $appointmentDateTime - (24 * 60 * 60));
        
        // Só agenda se ainda falta mais de 24h
        if (strtotime($reminderTime) > time()) {
            $message = $this->buildReminderMessage($appointment);
            $this->queueModel->queueWhatsApp($appointment->client_phone, $message, [
                'appointment_id' => $appointment->id,
                'type' => 'reminder',
            ], $reminderTime);
        }
    }
    
    /**
     * Agenda notificação de cancelamento
     * 
     * @param Appointment $appointment
     */
    public function sendCancellationNotification(Appointment $appointment): void
    {
        if (!$appointment->client_phone) {
            return;
        }
        
        $message = $this->buildCancellationMessage($appointment);
        $this->queueModel->queueWhatsApp($appointment->client_phone, $message, [
            'appointment_id' => $appointment->id,
            'type' => 'cancellation',
        ]);
    }
    
    /**
     * Agenda notificação de mudança de status
     * 
     * @param Appointment $appointment
     * @param string $newStatus
     */
    public function sendStatusChangeNotification(Appointment $appointment, string $newStatus): void
    {
        if (!$appointment->client_phone) {
            return;
        }
        
        $message = $this->buildStatusChangeMessage($appointment, $newStatus);
        $this->queueModel->queueWhatsApp($appointment->client_phone, $message, [
            'appointment_id' => $appointment->id,
            'type' => 'status_change',
            'new_status' => $newStatus,
        ]);
    }
    
    /**
     * Monta mensagem de confirmação
     */
    protected function buildConfirmationMessage(Appointment $appointment): string
    {
        return "✅ *Agendamento Confirmado!*\n\n" .
               "Olá, {$appointment->client_name}!\n\n" .
               "Seu agendamento foi realizado com sucesso:\n\n" .
               "📅 *Data:* {$appointment->dateFormatted()}\n" .
               "⏰ *Horário:* {$appointment->timeRange()}\n" .
               "👨‍⚕️ *Profissional:* {$appointment->professional_name}\n" .
               "💼 *Serviço:* {$appointment->service_name}\n\n" .
               "Em caso de dúvidas ou para cancelar, entre em contato conosco.\n\n" .
               "Até logo! 😊";
    }
    
    /**
     * Monta mensagem de lembrete
     */
    protected function buildReminderMessage(Appointment $appointment): string
    {
        return "🔔 *Lembrete de Agendamento*\n\n" .
               "Olá, {$appointment->client_name}!\n\n" .
               "Lembramos que você tem um agendamento *amanhã*:\n\n" .
               "📅 *Data:* {$appointment->dateFormatted()}\n" .
               "⏰ *Horário:* {$appointment->timeRange()}\n" .
               "👨‍⚕️ *Profissional:* {$appointment->professional_name}\n" .
               "💼 *Serviço:* {$appointment->service_name}\n\n" .
               "Não se esqueça! Contamos com sua presença. 😊";
    }
    
    /**
     * Monta mensagem de cancelamento
     */
    protected function buildCancellationMessage(Appointment $appointment): string
    {
        return "❌ *Agendamento Cancelado*\n\n" .
               "Olá, {$appointment->client_name}!\n\n" .
               "Seu agendamento foi cancelado:\n\n" .
               "📅 *Data:* {$appointment->dateFormatted()}\n" .
               "⏰ *Horário:* {$appointment->timeRange()}\n\n" .
               "Se precisar reagendar, entre em contato conosco.\n\n" .
               "Atenciosamente.";
    }
    
    /**
     * Monta mensagem de mudança de status
     */
    protected function buildStatusChangeMessage(Appointment $appointment, string $newStatus): string
    {
        $statusLabels = [
            'confirmed' => 'Confirmado ✅',
            'completed' => 'Concluído ✔️',
            'cancelled' => 'Cancelado ❌',
            'no_show'   => 'Não Compareceu 🚫',
        ];
        
        $statusLabel = $statusLabels[$newStatus] ?? $newStatus;
        
        return "📝 *Atualização de Agendamento*\n\n" .
               "Olá, {$appointment->client_name}!\n\n" .
               "O status do seu agendamento foi atualizado:\n\n" .
               "📅 *Data:* {$appointment->dateFormatted()}\n" .
               "⏰ *Horário:* {$appointment->timeRange()}\n" .
               "📊 *Novo Status:* {$statusLabel}\n\n" .
               "Em caso de dúvidas, entre em contato conosco.";
    }
}
