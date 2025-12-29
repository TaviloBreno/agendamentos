<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\NotificationQueueModel;
use App\Libraries\UltraMsgService;

/**
 * WhatsAppController - Configuração e gerenciamento de WhatsApp
 * 
 * =========================================================================
 * ROTAS MAPEADAS
 * =========================================================================
 * 
 * GET    /super/whatsapp           → index()    Página de configuração
 * POST   /super/whatsapp/save      → save()     Salva configurações
 * GET    /super/whatsapp/status    → status()   Verifica status conexão
 * POST   /super/whatsapp/test      → test()     Envia mensagem de teste
 * GET    /super/whatsapp/queue     → queue()    Lista fila de notificações
 * POST   /super/whatsapp/process   → process()  Processa fila manualmente
 * 
 * @package    App\Controllers\Super
 */
class WhatsAppController extends BaseController
{
    protected UserModel $userModel;
    protected NotificationQueueModel $queueModel;
    
    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        $this->queueModel = model(NotificationQueueModel::class);
    }
    
    /**
     * Página de configuração do WhatsApp
     * 
     * GET /super/whatsapp
     */
    public function index(): string
    {
        $userId = session('userId');
        $user = $this->userModel->find($userId);
        $queueStats = $this->queueModel->getQueueStats();
        
        $data = [
            'title'       => 'Configuração WhatsApp',
            'pageHeading' => 'Configurar WhatsApp',
            'user'        => $user,
            'queueStats'  => $queueStats,
            'providers'   => [
                'ultramsg'  => 'UltraMsg',
                'evolution' => 'Evolution API',
                'twilio'    => 'Twilio',
                'z-api'     => 'Z-API',
            ],
        ];
        
        return view('Back/WhatsApp/index', $data);
    }
    
    /**
     * Salva configurações do WhatsApp
     * 
     * POST /super/whatsapp/save
     */
    public function save()
    {
        $userId = session('userId');
        
        $data = [
            'whatsapp_enabled'     => $this->request->getPost('whatsapp_enabled') ? 1 : 0,
            'whatsapp_provider'    => $this->request->getPost('whatsapp_provider'),
            'whatsapp_instance_id' => $this->request->getPost('whatsapp_instance_id'),
            'whatsapp_token'       => $this->request->getPost('whatsapp_token'),
            'whatsapp_phone'       => $this->request->getPost('whatsapp_phone'),
        ];
        
        // Validação básica
        if ($data['whatsapp_enabled'] && empty($data['whatsapp_instance_id'])) {
            return redirect()->back()
                           ->withInput()
                           ->with('danger', 'Informe o Instance ID para habilitar as notificações.');
        }
        
        if ($data['whatsapp_enabled'] && empty($data['whatsapp_token'])) {
            return redirect()->back()
                           ->withInput()
                           ->with('danger', 'Informe o Token para habilitar as notificações.');
        }
        
        $this->userModel->update($userId, $data);
        
        return redirect()->to(route_to('super.whatsapp'))
                       ->with('success', 'Configurações de WhatsApp salvas com sucesso!');
    }
    
    /**
     * Verifica status da conexão WhatsApp
     * 
     * GET /super/whatsapp/status
     */
    public function status()
    {
        $userId = session('userId');
        $user = $this->userModel->find($userId);
        
        if (!$user->whatsapp_instance_id || !$user->whatsapp_token) {
            return $this->response->setJSON([
                'success' => false,
                'status'  => 'not_configured',
                'message' => 'WhatsApp não configurado',
            ]);
        }
        
        $ultraMsg = new UltraMsgService();
        $ultraMsg->setCredentials($user->whatsapp_instance_id, $user->whatsapp_token);
        
        $result = $ultraMsg->getInstanceStatus();
        
        // Atualizar status no banco
        $status = ($result['success'] && isset($result['status']) && $result['status'] === 'connected') 
                  ? 'connected' 
                  : 'disconnected';
        
        $this->userModel->update($userId, ['whatsapp_status' => $status]);
        
        return $this->response->setJSON([
            'success' => $result['success'],
            'status'  => $status,
            'data'    => $result,
        ]);
    }
    
    /**
     * Envia mensagem de teste
     * 
     * POST /super/whatsapp/test
     */
    public function test()
    {
        $userId = session('userId');
        $user = $this->userModel->find($userId);
        
        $phone = $this->request->getPost('phone');
        $message = $this->request->getPost('message') ?? '🔔 Teste do Sistema de Agendamentos - WhatsApp configurado com sucesso!';
        
        if (!$phone) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Informe o número de telefone',
            ]);
        }
        
        $ultraMsg = new UltraMsgService();
        $ultraMsg->setCredentials($user->whatsapp_instance_id, $user->whatsapp_token);
        
        $result = $ultraMsg->sendMessage($phone, $message);
        
        return $this->response->setJSON($result);
    }
    
    /**
     * Lista fila de notificações
     * 
     * GET /super/whatsapp/queue
     */
    public function queue(): string
    {
        $status = $this->request->getGet('status');
        
        $builder = $this->queueModel;
        
        if ($status) {
            $builder = $builder->where('status', $status);
        }
        
        $notifications = $builder->orderBy('created_at', 'DESC')
                                 ->limit(100)
                                 ->findAll();
        
        $stats = $this->queueModel->getQueueStats();
        
        $data = [
            'title'         => 'Fila de Notificações',
            'pageHeading'   => 'Fila de Notificações',
            'notifications' => $notifications,
            'stats'         => $stats,
            'selectedStatus'=> $status,
        ];
        
        return view('Back/WhatsApp/queue', $data);
    }
    
    /**
     * Processa fila de notificações manualmente
     * 
     * POST /super/whatsapp/process
     */
    public function process()
    {
        $userId = session('userId');
        $user = $this->userModel->find($userId);
        
        if (!$user->whatsapp_enabled || !$user->whatsapp_instance_id) {
            return $this->response->setJSON([
                'success'   => false,
                'message'   => 'WhatsApp não está configurado ou habilitado',
                'processed' => 0,
            ]);
        }
        
        $ultraMsg = new UltraMsgService();
        $ultraMsg->setCredentials($user->whatsapp_instance_id, $user->whatsapp_token);
        
        $notifications = $this->queueModel->getPendingNotifications(10);
        $processed = 0;
        $errors = 0;
        
        foreach ($notifications as $notification) {
            $this->queueModel->markAsProcessing($notification->id);
            
            if ($notification->type === 'whatsapp') {
                $result = $ultraMsg->sendMessage($notification->recipient, $notification->message);
                
                if ($result['success']) {
                    $this->queueModel->markAsSent($notification->id);
                    $processed++;
                } else {
                    $this->queueModel->markAsFailed($notification->id, $result['error'] ?? 'Erro desconhecido');
                    $errors++;
                }
            }
            
            // Pequeno delay para não sobrecarregar a API
            usleep(500000); // 0.5 segundo
        }
        
        return $this->response->setJSON([
            'success'   => true,
            'message'   => "Processamento concluído: {$processed} enviadas, {$errors} erros",
            'processed' => $processed,
            'errors'    => $errors,
        ]);
    }
    
    /**
     * Obtém QR Code para conexão
     * 
     * GET /super/whatsapp/qrcode
     */
    public function qrcode()
    {
        $userId = session('userId');
        $user = $this->userModel->find($userId);
        
        if (!$user->whatsapp_instance_id || !$user->whatsapp_token) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Configure Instance ID e Token primeiro',
            ]);
        }
        
        $ultraMsg = new UltraMsgService();
        $ultraMsg->setCredentials($user->whatsapp_instance_id, $user->whatsapp_token);
        
        $result = $ultraMsg->getQrCode();
        
        return $this->response->setJSON($result);
    }
}
