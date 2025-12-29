<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * ProfileController - Gerencia perfil e configurações do usuário
 */
class ProfileController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
    }

    /**
     * Página de perfil do usuário
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        return view('back/profile/index', [
            'title' => 'Meu Perfil',
            'user'  => $user,
        ]);
    }

    /**
     * Página de configurações do usuário
     */
    public function settings()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        return view('back/profile/settings', [
            'title'    => 'Configurações',
            'user'     => $user,
            'settings' => $user->getAllSettings(),
        ]);
    }

    /**
     * Atualiza o perfil do usuário
     */
    public function update()
    {
        $this->checkMethod('POST');

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $data = [
            'name'  => trim($this->request->getPost('name')),
            'phone' => trim($this->request->getPost('phone')),
            'bio'   => trim($this->request->getPost('bio')),
        ];

        // Validação básica
        if (empty($data['name'])) {
            return redirect()->back()->withInput()->with('error', 'O nome é obrigatório');
        }

        if (mb_strlen($data['name']) < 3) {
            return redirect()->back()->withInput()->with('error', 'O nome deve ter pelo menos 3 caracteres');
        }

        // Atualiza
        $this->userModel->update($userId, $data);

        return redirect()->to('/admin/profile')->with('success', 'Perfil atualizado com sucesso');
    }

    /**
     * Upload de avatar
     */
    public function uploadAvatar()
    {
        $this->checkMethod('POST');

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $file = $this->request->getFile('avatar');

        if (!$file || !$file->isValid()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nenhum arquivo enviado ou arquivo inválido',
                ]);
            }
            return redirect()->back()->with('error', 'Nenhum arquivo enviado ou arquivo inválido');
        }

        // Validar tipo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP',
                ]);
            }
            return redirect()->back()->with('error', 'Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP');
        }

        // Validar tamanho (máximo 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Arquivo muito grande. Máximo 2MB',
                ]);
            }
            return redirect()->back()->with('error', 'Arquivo muito grande. Máximo 2MB');
        }

        // Criar diretório se não existir
        $uploadPath = FCPATH . 'uploads/avatars/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Remover avatar antigo se existir
        if ($user->hasCustomAvatar()) {
            $oldAvatar = $uploadPath . $user->attributes['avatar'];
            if (file_exists($oldAvatar)) {
                unlink($oldAvatar);
            }
        }

        // Gerar novo nome
        $newName = 'avatar_' . $userId . '_' . time() . '.' . $file->getExtension();

        // Mover arquivo
        $file->move($uploadPath, $newName);

        // Redimensionar imagem (se necessário)
        $this->resizeAvatar($uploadPath . $newName);

        // Atualizar no banco
        $this->userModel->update($userId, ['avatar' => $newName]);

        // Atualizar sessão
        session()->set('user_avatar', $newName);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Avatar atualizado com sucesso',
                'avatarUrl' => base_url('uploads/avatars/' . $newName),
            ]);
        }

        return redirect()->to('/admin/profile')->with('success', 'Avatar atualizado com sucesso');
    }

    /**
     * Remove o avatar (volta para o padrão)
     */
    public function removeAvatar()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        // Remover arquivo se existir
        if ($user->hasCustomAvatar()) {
            $avatarPath = FCPATH . 'uploads/avatars/' . $user->attributes['avatar'];
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
        }

        // Limpar no banco
        $this->userModel->update($userId, ['avatar' => null]);

        // Limpar sessão
        session()->remove('user_avatar');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Avatar removido com sucesso',
            ]);
        }

        return redirect()->to('/admin/profile')->with('success', 'Avatar removido com sucesso');
    }

    /**
     * Atualiza as configurações
     */
    public function updateSettings()
    {
        $this->checkMethod('POST');

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        // Coleta as configurações do formulário
        $settings = [
            'email_notifications'   => (bool) $this->request->getPost('email_notifications'),
            'push_notifications'    => (bool) $this->request->getPost('push_notifications'),
            'appointment_reminders' => (bool) $this->request->getPost('appointment_reminders'),
            'message_notifications' => (bool) $this->request->getPost('message_notifications'),
            'language'              => $this->request->getPost('language') ?: 'pt-BR',
            'theme'                 => $this->request->getPost('theme') ?: 'light',
            'sidebar_collapsed'     => (bool) $this->request->getPost('sidebar_collapsed'),
        ];

        // Atualiza
        $this->userModel->update($userId, ['settings' => json_encode($settings)]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Configurações salvas com sucesso',
            ]);
        }

        return redirect()->to('/admin/profile/settings')->with('success', 'Configurações salvas com sucesso');
    }

    /**
     * Altera a senha
     */
    public function changePassword()
    {
        $this->checkMethod('POST');

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Verifica senha atual
        if (!$user->verifyPassword($currentPassword)) {
            return redirect()->back()->with('error', 'Senha atual incorreta');
        }

        // Valida nova senha
        if (mb_strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'A nova senha deve ter pelo menos 6 caracteres');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'A confirmação de senha não confere');
        }

        // Atualiza usando o Entity (que faz o hash automaticamente)
        $user->setPassword($newPassword);
        $this->userModel->save($user);

        return redirect()->to('/admin/profile')->with('success', 'Senha alterada com sucesso');
    }

    /**
     * Redimensiona avatar para tamanho padrão
     */
    protected function resizeAvatar(string $filePath, int $maxSize = 200): void
    {
        // Carrega serviço de imagem do CodeIgniter
        $image = \Config\Services::image();

        try {
            $image->withFile($filePath)
                ->fit($maxSize, $maxSize, 'center')
                ->save($filePath, 85);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao redimensionar avatar: ' . $e->getMessage());
        }
    }
}
