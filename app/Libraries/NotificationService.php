<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use App\Entities\Appointment;

/**
 * NotificationService - Serviço de Notificações por Email
 * 
 * Gerencia o envio de emails para confirmação, lembretes e cancelamentos
 * de agendamentos.
 */
class NotificationService
{
    protected Email $email;
    protected array $config;

    public function __construct()
    {
        $this->email = service('email');
        $this->config = [
            'fromEmail' => env('email.fromEmail', 'noreply@sistema.com'),
            'fromName'  => env('email.fromName', 'Sistema de Agendamentos'),
        ];
    }

    /**
     * Envia email de confirmação de agendamento
     */
    public function sendAppointmentConfirmation(Appointment $appointment): bool
    {
        $subject = 'Confirmação de Agendamento #' . $appointment->id;
        
        $message = $this->buildConfirmationEmail($appointment);
        
        return $this->send($appointment->client_email, $subject, $message);
    }

    /**
     * Envia email de lembrete (24h antes)
     */
    public function sendAppointmentReminder(Appointment $appointment): bool
    {
        $subject = 'Lembrete: Seu agendamento é amanhã!';
        
        $message = $this->buildReminderEmail($appointment);
        
        return $this->send($appointment->client_email, $subject, $message);
    }

    /**
     * Envia email de cancelamento
     */
    public function sendAppointmentCancellation(Appointment $appointment): bool
    {
        $subject = 'Agendamento Cancelado #' . $appointment->id;
        
        $message = $this->buildCancellationEmail($appointment);
        
        return $this->send($appointment->client_email, $subject, $message);
    }

    /**
     * Envia email de alteração de status
     */
    public function sendStatusChange(Appointment $appointment, string $oldStatus): bool
    {
        $subject = 'Atualização do seu Agendamento #' . $appointment->id;
        
        $message = $this->buildStatusChangeEmail($appointment, $oldStatus);
        
        return $this->send($appointment->client_email, $subject, $message);
    }

    /**
     * Método base para envio de email
     */
    protected function send(string $to, string $subject, string $message): bool
    {
        try {
            $this->email->clear();
            $this->email->setFrom($this->config['fromEmail'], $this->config['fromName']);
            $this->email->setTo($to);
            $this->email->setSubject($subject);
            $this->email->setMessage($message);
            $this->email->setMailType('html');
            
            $result = $this->email->send(false);
            
            if (!$result) {
                log_message('error', 'Falha ao enviar email: ' . $this->email->printDebugger(['headers']));
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Exceção ao enviar email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Constrói email de confirmação
     */
    protected function buildConfirmationEmail(Appointment $appointment): string
    {
        $dateFormatted = date('d/m/Y', strtotime($appointment->date));
        $timeFormatted = substr($appointment->start_time, 0, 5);
        
        return $this->getEmailTemplate('confirmation', [
            'clientName'   => $appointment->client_name,
            'serviceName'  => $appointment->service_name ?? 'Serviço',
            'professional' => $appointment->professional_name ?? 'Profissional',
            'unit'         => $appointment->unit_name ?? 'Unidade',
            'date'         => $dateFormatted,
            'time'         => $timeFormatted,
            'appointmentId'=> $appointment->id,
        ]);
    }

    /**
     * Constrói email de lembrete
     */
    protected function buildReminderEmail(Appointment $appointment): string
    {
        $dateFormatted = date('d/m/Y', strtotime($appointment->date));
        $timeFormatted = substr($appointment->start_time, 0, 5);
        
        return $this->getEmailTemplate('reminder', [
            'clientName'   => $appointment->client_name,
            'serviceName'  => $appointment->service_name ?? 'Serviço',
            'professional' => $appointment->professional_name ?? 'Profissional',
            'unit'         => $appointment->unit_name ?? 'Unidade',
            'date'         => $dateFormatted,
            'time'         => $timeFormatted,
        ]);
    }

    /**
     * Constrói email de cancelamento
     */
    protected function buildCancellationEmail(Appointment $appointment): string
    {
        $dateFormatted = date('d/m/Y', strtotime($appointment->date));
        $timeFormatted = substr($appointment->start_time, 0, 5);
        
        return $this->getEmailTemplate('cancellation', [
            'clientName'   => $appointment->client_name,
            'serviceName'  => $appointment->service_name ?? 'Serviço',
            'date'         => $dateFormatted,
            'time'         => $timeFormatted,
            'appointmentId'=> $appointment->id,
        ]);
    }

    /**
     * Constrói email de mudança de status
     */
    protected function buildStatusChangeEmail(Appointment $appointment, string $oldStatus): string
    {
        $statusLabels = [
            'scheduled' => 'Agendado',
            'confirmed' => 'Confirmado',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado',
            'no_show'   => 'Não Compareceu',
        ];
        
        return $this->getEmailTemplate('status_change', [
            'clientName' => $appointment->client_name,
            'oldStatus'  => $statusLabels[$oldStatus] ?? $oldStatus,
            'newStatus'  => $statusLabels[$appointment->status] ?? $appointment->status,
            'appointmentId' => $appointment->id,
        ]);
    }

    /**
     * Retorna template de email formatado
     */
    protected function getEmailTemplate(string $type, array $data): string
    {
        $templates = [
            'confirmation' => '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                        .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
                        .info-row { display: flex; margin: 10px 0; }
                        .info-label { font-weight: bold; width: 120px; color: #666; }
                        .footer { text-align: center; margin-top: 20px; color: #888; font-size: 12px; }
                        .btn { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>✅ Agendamento Confirmado!</h1>
                        </div>
                        <div class="content">
                            <p>Olá <strong>{clientName}</strong>,</p>
                            <p>Seu agendamento foi realizado com sucesso! Confira os detalhes abaixo:</p>
                            
                            <div class="info-box">
                                <div class="info-row">
                                    <span class="info-label">📋 Serviço:</span>
                                    <span>{serviceName}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">👨‍⚕️ Profissional:</span>
                                    <span>{professional}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">🏢 Unidade:</span>
                                    <span>{unit}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">📅 Data:</span>
                                    <span>{date}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">🕐 Horário:</span>
                                    <span>{time}</span>
                                </div>
                            </div>
                            
                            <p><strong>Código do agendamento:</strong> #{appointmentId}</p>
                            
                            <p>Em caso de imprevistos, por favor entre em contato conosco ou cancele pelo sistema com antecedência.</p>
                            
                            <div class="footer">
                                <p>Este é um email automático, por favor não responda.</p>
                                <p>© ' . date('Y') . ' Sistema de Agendamentos</p>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            ',
            
            'reminder' => '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                        .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f5576c; }
                        .footer { text-align: center; margin-top: 20px; color: #888; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>⏰ Lembrete de Agendamento</h1>
                        </div>
                        <div class="content">
                            <p>Olá <strong>{clientName}</strong>,</p>
                            <p>Este é um lembrete do seu agendamento para <strong>amanhã</strong>!</p>
                            
                            <div class="info-box">
                                <p><strong>📋 Serviço:</strong> {serviceName}</p>
                                <p><strong>👨‍⚕️ Profissional:</strong> {professional}</p>
                                <p><strong>🏢 Unidade:</strong> {unit}</p>
                                <p><strong>📅 Data:</strong> {date}</p>
                                <p><strong>🕐 Horário:</strong> {time}</p>
                            </div>
                            
                            <p>Não se esqueça de chegar com 10 minutos de antecedência.</p>
                            
                            <div class="footer">
                                <p>Este é um email automático, por favor não responda.</p>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            ',
            
            'cancellation' => '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                        .info-box { background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107; }
                        .footer { text-align: center; margin-top: 20px; color: #888; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>❌ Agendamento Cancelado</h1>
                        </div>
                        <div class="content">
                            <p>Olá <strong>{clientName}</strong>,</p>
                            <p>Seu agendamento foi cancelado conforme solicitado.</p>
                            
                            <div class="info-box">
                                <p><strong>📋 Serviço:</strong> {serviceName}</p>
                                <p><strong>📅 Data:</strong> {date}</p>
                                <p><strong>🕐 Horário:</strong> {time}</p>
                                <p><strong>Código:</strong> #{appointmentId}</p>
                            </div>
                            
                            <p>Se desejar, você pode realizar um novo agendamento a qualquer momento.</p>
                            
                            <div class="footer">
                                <p>Este é um email automático, por favor não responda.</p>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            ',
            
            'status_change' => '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                        .status-change { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
                        .old-status { color: #888; text-decoration: line-through; }
                        .new-status { color: #28a745; font-weight: bold; font-size: 1.2em; }
                        .arrow { font-size: 2em; margin: 0 15px; }
                        .footer { text-align: center; margin-top: 20px; color: #888; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>🔄 Atualização de Status</h1>
                        </div>
                        <div class="content">
                            <p>Olá <strong>{clientName}</strong>,</p>
                            <p>O status do seu agendamento #{appointmentId} foi atualizado:</p>
                            
                            <div class="status-change">
                                <span class="old-status">{oldStatus}</span>
                                <span class="arrow">→</span>
                                <span class="new-status">{newStatus}</span>
                            </div>
                            
                            <div class="footer">
                                <p>Este é um email automático, por favor não responda.</p>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            ',
        ];

        $template = $templates[$type] ?? '';
        
        // Substituir placeholders pelos valores
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }

    /**
     * Envia lembretes para agendamentos do dia seguinte
     * 
     * Este método deve ser chamado por um CRON job diariamente
     */
    public function sendDailyReminders(): array
    {
        $appointmentModel = model('AppointmentModel');
        
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        $appointments = $appointmentModel
            ->where('date', $tomorrow)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->findAll();
        
        $results = [
            'total'   => count($appointments),
            'sent'    => 0,
            'failed'  => 0,
        ];
        
        foreach ($appointments as $appointment) {
            if ($this->sendAppointmentReminder($appointment)) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }
}
