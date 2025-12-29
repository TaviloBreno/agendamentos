<?php

namespace App\Controllers;

use App\Models\ConversationModel;
use App\Models\ConversationParticipantModel;
use App\Models\MessageModel;
use App\Models\UserModel;

/**
 * MessageController - Gerencia sistema de mensagens entre usuários
 */
class MessageController extends BaseController
{
    protected ConversationModel $conversationModel;
    protected MessageModel $messageModel;
    protected ConversationParticipantModel $participantModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->conversationModel = model('ConversationModel');
        $this->messageModel = model('MessageModel');
        $this->participantModel = model('ConversationParticipantModel');
        $this->userModel = model('UserModel');
    }

    /**
     * Obtém o ID do usuário atual ou redireciona para login
     */
    protected function getCurrentUserId(): ?int
    {
        $userId = session()->get('user_id');
        return $userId ? (int) $userId : null;
    }

    /**
     * Lista as conversas do usuário (inbox)
     */
    public function index()
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $conversations = $this->conversationModel->getByUser($userId);

        // Adiciona informações extras para cada conversa
        $conversationsData = [];
        foreach ($conversations as $conversation) {
            $lastMessage = $conversation->getLastMessage();
            $unreadCount = $conversation->unreadCount($userId);

            $conversationsData[] = [
                'conversation' => $conversation,
                'title'        => $conversation->getTitleFor($userId),
                'lastMessage'  => $lastMessage,
                'unreadCount'  => $unreadCount,
                'users'        => $conversation->getUsers(),
            ];
        }

        return view('back/messages/index', [
            'title'         => 'Mensagens',
            'conversations' => $conversationsData,
        ]);
    }

    /**
     * Exibe uma conversa
     */
    public function show(int $conversationId)
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $conversation = $this->conversationModel->find($conversationId);

        if (!$conversation) {
            return redirect()->to('/admin/messages')->with('error', 'Conversa não encontrada');
        }

        // Verifica se o usuário é participante
        if (!$this->participantModel->isParticipant($conversationId, $userId)) {
            return redirect()->to('/admin/messages')->with('error', 'Você não tem acesso a esta conversa');
        }

        // Marca como lida
        $this->participantModel->markAsRead($conversationId, $userId);

        // Carrega mensagens
        $messages = $this->messageModel->getMessages($conversationId, 50);

        // Carrega participantes
        $users = $conversation->getUsers();

        return view('back/messages/show', [
            'title'        => $conversation->getTitleFor($userId),
            'conversation' => $conversation,
            'messages'     => $messages,
            'users'        => $users,
            'userId'       => $userId,
        ]);
    }

    /**
     * Tela para criar nova conversa
     */
    public function create()
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        // Lista usuários disponíveis para conversa (mesmo tenant)
        $tenantId = session()->get('tenant_id');
        $users = $this->userModel->where('id !=', $userId)
            ->where('active', 1)
            ->findAll();

        return view('back/messages/create', [
            'title' => 'Nova Conversa',
            'users' => $users,
        ]);
    }

    /**
     * Inicia uma conversa direta com um usuário
     */
    public function startDirect(int $targetUserId)
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        // Não pode conversar consigo mesmo
        if ($targetUserId === $userId) {
            return redirect()->to('/admin/messages')->with('error', 'Você não pode iniciar uma conversa consigo mesmo');
        }

        // Verifica se o usuário alvo existe
        $targetUser = $this->userModel->find($targetUserId);
        if (!$targetUser) {
            return redirect()->to('/admin/messages')->with('error', 'Usuário não encontrado');
        }

        // Encontra ou cria conversa direta
        $conversation = $this->conversationModel->findOrCreateDirect($userId, $targetUserId);

        return redirect()->to('/admin/messages/' . $conversation->id);
    }

    /**
     * Cria uma conversa em grupo
     */
    public function createGroup()
    {
        $this->checkMethod('POST');

        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $subject = $this->request->getPost('subject');
        $participantIds = $this->request->getPost('participants');

        if (empty($subject)) {
            return redirect()->back()->withInput()->with('error', 'O assunto é obrigatório');
        }

        if (empty($participantIds) || !is_array($participantIds)) {
            return redirect()->back()->withInput()->with('error', 'Selecione pelo menos um participante');
        }

        $conversation = $this->conversationModel->createGroup($userId, $subject, $participantIds);

        return redirect()->to('/admin/messages/' . $conversation->id)->with('success', 'Grupo criado com sucesso');
    }

    /**
     * Envia uma mensagem
     */
    public function send(int $conversationId)
    {
        $this->checkMethod('POST');

        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
            }
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        // Verifica se o usuário é participante
        if (!$this->participantModel->isParticipant($conversationId, $userId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Você não tem acesso a esta conversa',
                ]);
            }
            return redirect()->to('/admin/messages')->with('error', 'Você não tem acesso a esta conversa');
        }

        $messageText = trim($this->request->getPost('message'));
        $attachment = null;
        $attachmentType = null;

        // Processa anexo se houver
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $result = $this->processAttachment($file);
            if ($result['success']) {
                $attachment = $result['filename'];
                $attachmentType = $result['type'];
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $result['error'],
                    ]);
                }
                return redirect()->back()->with('error', $result['error']);
            }
        }

        // Verifica se tem mensagem ou anexo
        if (empty($messageText) && !$attachment) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Digite uma mensagem ou envie um anexo',
                ]);
            }
            return redirect()->back()->with('error', 'Digite uma mensagem ou envie um anexo');
        }

        // Envia a mensagem
        $message = $this->messageModel->sendMessage(
            $conversationId,
            $userId,
            $messageText,
            $attachment,
            $attachmentType
        );

        if (!$message) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao enviar mensagem',
                ]);
            }
            return redirect()->back()->with('error', 'Erro ao enviar mensagem');
        }

        // Notifica os outros participantes
        $this->notifyParticipants($conversationId, $userId, $message);

        if ($this->request->isAJAX()) {
            $sender = $this->userModel->find($userId);
            return $this->response->setJSON([
                'success' => true,
                'message' => [
                    'id'             => $message->id,
                    'message'        => $message->getFormattedMessage(),
                    'timeAgo'        => $message->timeAgo(),
                    'hasAttachment'  => $message->hasAttachment(),
                    'attachmentUrl'  => $message->getAttachmentUrl(),
                    'attachmentType' => $message->attachment_type,
                    'attachmentIcon' => $message->getAttachmentIcon(),
                    'sender'         => [
                        'id'     => $sender->id,
                        'name'   => $sender->name,
                        'avatar' => $sender->avatarUrl(40),
                    ],
                ],
            ]);
        }

        return redirect()->to('/admin/messages/' . $conversationId);
    }

    /**
     * Carrega mais mensagens (paginação AJAX)
     */
    public function loadMore(int $conversationId)
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        // Verifica se o usuário é participante
        if (!$this->participantModel->isParticipant($conversationId, $userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado',
            ]);
        }

        $beforeId = $this->request->getGet('before');
        $messages = $this->messageModel->getMessages($conversationId, 20, $beforeId ? (int)$beforeId : null);

        $messagesData = [];
        foreach ($messages as $message) {
            $sender = $message->getSender();
            $messagesData[] = [
                'id'             => $message->id,
                'message'        => $message->getFormattedMessage(),
                'timeAgo'        => $message->timeAgo(),
                'isSystem'       => $message->is_system,
                'hasAttachment'  => $message->hasAttachment(),
                'attachmentUrl'  => $message->getAttachmentUrl(),
                'attachmentType' => $message->attachment_type,
                'attachmentIcon' => $message->getAttachmentIcon(),
                'sender'         => $sender ? [
                    'id'     => $sender->id,
                    'name'   => $sender->name,
                    'avatar' => $sender->avatarUrl(40),
                ] : null,
                'isMine'         => $message->sender_id === $userId,
            ];
        }

        return $this->response->setJSON([
            'success'  => true,
            'messages' => $messagesData,
            'hasMore'  => count($messages) === 20,
        ]);
    }

    /**
     * Marca conversa como lida
     */
    public function markRead(int $conversationId)
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        if (!$this->participantModel->isParticipant($conversationId, $userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado',
            ]);
        }

        $this->participantModel->markAsRead($conversationId, $userId);

        return $this->response->setJSON([
            'success' => true,
        ]);
    }

    /**
     * Muta/Desmuta notificações de uma conversa
     */
    public function toggleMute(int $conversationId)
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessão expirada']);
        }

        $participant = $this->participantModel->getParticipant($conversationId, $userId);

        if (!$participant || $participant->left_at) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado',
            ]);
        }

        $newMutedState = !$participant->is_muted;
        $this->participantModel->setMuted($conversationId, $userId, $newMutedState);

        return $this->response->setJSON([
            'success' => true,
            'muted'   => $newMutedState,
            'message' => $newMutedState ? 'Notificações silenciadas' : 'Notificações ativadas',
        ]);
    }

    /**
     * Sair de uma conversa em grupo
     */
    public function leave(int $conversationId)
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $conversation = $this->conversationModel->find($conversationId);

        if (!$conversation || $conversation->type !== 'group') {
            return redirect()->to('/admin/messages')->with('error', 'Ação inválida');
        }

        $this->participantModel->removeParticipant($conversationId, $userId);

        // Envia mensagem de sistema
        $user = $this->userModel->find($userId);
        $this->messageModel->sendSystemMessage($conversationId, "{$user->name} saiu do grupo");

        return redirect()->to('/admin/messages')->with('success', 'Você saiu do grupo');
    }

    /**
     * Processa upload de anexo
     */
    protected function processAttachment($file): array
    {
        $allowedTypes = [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg'],
        ];

        $mimeType = $file->getMimeType();
        $detectedType = null;

        foreach ($allowedTypes as $type => $mimes) {
            if (in_array($mimeType, $mimes)) {
                $detectedType = $type;
                break;
            }
        }

        if (!$detectedType) {
            return [
                'success' => false,
                'error'   => 'Tipo de arquivo não permitido',
            ];
        }

        // Limite de tamanho: 10MB
        if ($file->getSize() > 10 * 1024 * 1024) {
            return [
                'success' => false,
                'error'   => 'Arquivo muito grande (máximo 10MB)',
            ];
        }

        // Move o arquivo
        $newName = $file->getRandomName();
        $uploadPath = WRITEPATH . 'uploads/messages/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $newName);

        return [
            'success'  => true,
            'filename' => $newName,
            'type'     => $detectedType,
        ];
    }

    /**
     * Notifica os outros participantes da conversa
     */
    protected function notifyParticipants(int $conversationId, int $senderId, $message): void
    {
        $participants = $this->participantModel->getActiveParticipants($conversationId);
        $sender = $this->userModel->find($senderId);
        $notificationModel = model('NotificationModel');

        foreach ($participants as $participant) {
            // Não notifica o remetente
            if ($participant->user_id === $senderId) {
                continue;
            }

            // Não notifica se estiver mutado
            if ($participant->is_muted) {
                continue;
            }

            $notificationModel->createNotification(
                $participant->user_id,
                'message',
                'Nova mensagem de ' . $sender->firstName(),
                $message->preview(50),
                ['conversation_id' => $conversationId, 'message_id' => $message->id],
                '/admin/messages/' . $conversationId
            );
        }
    }
}
