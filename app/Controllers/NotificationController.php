<?php

namespace App\Controllers;

use App\Models\NotificationModel;

/**
 * NotificationController - Gerencia notificações do sistema
 */
class NotificationController extends BaseController
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = model('NotificationModel');
    }

    /**
     * Obtém o ID do usuário atual ou null se não logado
     */
    protected function getCurrentUserId(): ?int
    {
        $userId = session()->get('user_id');
        return $userId ? (int) $userId : null;
    }

    /**
     * Lista todas as notificações do usuário
     */
    public function index()
    {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $filter = $this->request->getGet('filter');

        $builder = $this->notificationModel->where('user_id', $userId);

        // Filtros
        if ($filter === 'unread') {
            $builder->whereNull('read_at');
        } elseif ($filter === 'read') {
            $builder->whereNotNull('read_at');
        }

        $notifications = $builder->orderBy('created_at', 'DESC')
            ->paginate(20);

        return view('back/notifications/index', [
            'title'         => 'Notificações',
            'notifications' => $notifications,
            'pager'         => $this->notificationModel->pager,
            'filter'        => $filter,
            'unreadCount'   => $this->notificationModel->countUnread($userId),
        ]);
    }

    /**
     * Retorna notificações para o dropdown (AJAX)
     */
    public function dropdown()
    {
        $userId = session()->get('user_id');

        $notifications = $this->notificationModel->getUnread($userId, 5);
        $unreadCount = $this->notificationModel->countUnread($userId);

        return $this->response->setJSON([
            'success'       => true,
            'notifications' => array_map(function ($n) {
                return [
                    'id'        => $n->id,
                    'title'     => $n->title,
                    'message'   => character_limiter($n->message, 50),
                    'icon'      => $n->getIcon(),
                    'color'     => $n->getColor(),
                    'link'      => $n->link,
                    'timeAgo'   => $n->timeAgo(),
                    'iconHtml'  => $n->iconHtml(),
                ];
            }, $notifications),
            'unreadCount'   => $unreadCount,
        ]);
    }

    /**
     * Marca uma notificação como lida
     */
    public function markRead(int $id)
    {
        $userId = session()->get('user_id');

        $notification = $this->notificationModel->where('user_id', $userId)
            ->find($id);

        if (!$notification) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Notificação não encontrada',
                ]);
            }
            return redirect()->back()->with('error', 'Notificação não encontrada');
        }

        $this->notificationModel->markAsRead($id, $userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'     => true,
                'unreadCount' => $this->notificationModel->countUnread($userId),
            ]);
        }

        // Se tem link, redireciona para ele
        if ($notification->link) {
            return redirect()->to($notification->link);
        }

        return redirect()->back()->with('success', 'Notificação marcada como lida');
    }

    /**
     * Marca todas as notificações como lidas
     */
    public function markAllRead()
    {
        $userId = session()->get('user_id');

        $this->notificationModel->markAllAsRead($userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'     => true,
                'message'     => 'Todas as notificações foram marcadas como lidas',
                'unreadCount' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Todas as notificações foram marcadas como lidas');
    }

    /**
     * Deleta uma notificação
     */
    public function delete(int $id)
    {
        $userId = session()->get('user_id');

        $notification = $this->notificationModel->where('user_id', $userId)
            ->find($id);

        if (!$notification) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Notificação não encontrada',
                ]);
            }
            return redirect()->back()->with('error', 'Notificação não encontrada');
        }

        $this->notificationModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'     => true,
                'message'     => 'Notificação excluída',
                'unreadCount' => $this->notificationModel->countUnread($userId),
            ]);
        }

        return redirect()->back()->with('success', 'Notificação excluída com sucesso');
    }

    /**
     * Deleta todas as notificações lidas
     */
    public function clearRead()
    {
        $userId = session()->get('user_id');

        $this->notificationModel->where('user_id', $userId)
            ->whereNotNull('read_at')
            ->delete();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notificações lidas foram excluídas',
            ]);
        }

        return redirect()->back()->with('success', 'Notificações lidas foram excluídas');
    }
}
