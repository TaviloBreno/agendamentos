<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\NotificationQueueModel;
use App\Models\UserModel;
use App\Libraries\UltraMsgService;

/**
 * Comando para processar fila de notificações
 * 
 * Uso: php spark notifications:process
 * 
 * Recomendado configurar no CRON para executar a cada minuto:
 * * * * * * cd /path/to/project && php spark notifications:process >> /dev/null 2>&1
 */
class ProcessNotifications extends BaseCommand
{
    protected $group       = 'Notifications';
    protected $name        = 'notifications:process';
    protected $description = 'Processa a fila de notificações (WhatsApp, Email, SMS)';
    protected $usage       = 'notifications:process [options]';
    protected $arguments   = [];
    protected $options     = [
        '-l' => 'Limite de notificações a processar (padrão: 50)',
    ];
    
    public function run(array $params)
    {
        $limit = (int) ($params['l'] ?? 50);
        
        CLI::write('🔔 Processando fila de notificações...', 'yellow');
        CLI::newLine();
        
        $queueModel = model(NotificationQueueModel::class);
        $userModel = model(UserModel::class);
        
        // Buscar notificações pendentes
        $notifications = $queueModel->getPendingNotifications($limit);
        
        if (empty($notifications)) {
            CLI::write('✓ Nenhuma notificação pendente.', 'green');
            return;
        }
        
        CLI::write(count($notifications) . ' notificações encontradas.', 'cyan');
        CLI::newLine();
        
        $sent = 0;
        $failed = 0;
        
        // Buscar configuração WhatsApp do admin (primeiro usuário super)
        $admin = $userModel->where('role', 'super')
                           ->where('whatsapp_enabled', 1)
                           ->first();
        
        $ultraMsg = null;
        if ($admin && $admin->whatsapp_instance_id && $admin->whatsapp_token) {
            $ultraMsg = new UltraMsgService();
            $ultraMsg->setCredentials($admin->whatsapp_instance_id, $admin->whatsapp_token);
        }
        
        foreach ($notifications as $notification) {
            CLI::write("Processando #{$notification->id} ({$notification->type})...", 'white');
            
            $queueModel->markAsProcessing($notification->id);
            $success = false;
            $error = '';
            
            switch ($notification->type) {
                case 'whatsapp':
                    if (!$ultraMsg) {
                        $error = 'WhatsApp não configurado';
                        break;
                    }
                    
                    $result = $ultraMsg->sendMessage($notification->recipient, $notification->message);
                    $success = $result['success'] ?? false;
                    $error = $result['error'] ?? 'Erro desconhecido';
                    break;
                    
                case 'email':
                    $success = $this->sendEmail($notification);
                    if (!$success) {
                        $error = 'Falha ao enviar email';
                    }
                    break;
                    
                case 'sms':
                    $error = 'SMS não implementado';
                    break;
                    
                default:
                    $error = 'Tipo de notificação não suportado';
            }
            
            if ($success) {
                $queueModel->markAsSent($notification->id);
                CLI::write("  ✓ Enviada com sucesso", 'green');
                $sent++;
            } else {
                $queueModel->markAsFailed($notification->id, $error);
                CLI::write("  ✗ Falha: {$error}", 'red');
                $failed++;
            }
            
            // Delay entre mensagens para não sobrecarregar API
            usleep(500000); // 0.5 segundo
        }
        
        CLI::newLine();
        CLI::write("========================================", 'cyan');
        CLI::write("Resumo:", 'yellow');
        CLI::write("  ✓ Enviadas: {$sent}", 'green');
        CLI::write("  ✗ Falhas: {$failed}", 'red');
        CLI::write("========================================", 'cyan');
    }
    
    /**
     * Envia notificação por email
     */
    protected function sendEmail(object $notification): bool
    {
        try {
            $email = \Config\Services::email();
            
            $email->setFrom(config('Email')->fromEmail ?? 'noreply@sistema.com', config('Email')->fromName ?? 'Sistema de Agendamentos');
            $email->setTo($notification->recipient);
            $email->setSubject($notification->subject ?? 'Notificação - Sistema de Agendamentos');
            $email->setMessage($notification->message);
            $email->setMailType('html');
            
            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Email Error: ' . $e->getMessage());
            return false;
        }
    }
}
