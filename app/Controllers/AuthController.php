<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Email\Email;

/**
 * AuthController - Controlador de Autenticação
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Gerencia o fluxo de autenticação do sistema:
 * - Exibição do formulário de login
 * - Processamento de login
 * - Logout e destruição de sessão
 * - Recuperação de senha
 * 
 * =========================================================================
 * ROTAS
 * =========================================================================
 * 
 * GET  /login            → login()        → Exibe formulário
 * POST /login            → attempt()      → Processa login
 * GET  /logout           → logout()       → Encerra sessão
 * GET  /password/forgot  → forgotPassword() → Formulário esqueci senha
 * POST /password/forgot  → sendResetLink()  → Envia link de reset
 * GET  /password/reset   → resetPassword()  → Formulário nova senha
 * POST /password/reset   → updatePassword() → Atualiza senha
 * 
 * @package    App\Controllers
 * @author     Sistema de Agendamentos
 */
class AuthController extends BaseController
{
    /**
     * Model de usuários
     */
    protected UserModel $userModel;

    /**
     * Serviço de sessão
     */
    protected $session;

    /**
     * Nome do cookie "Lembrar-me"
     */
    protected string $rememberCookieName = 'remember_token';

    /**
     * Construtor - Inicializa dependências
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
        
        // Verificar cookie "Lembrar-me" ao inicializar
        $this->checkRememberMeCookie();
    }

    /**
     * Verifica e processa o cookie "Lembrar-me"
     */
    protected function checkRememberMeCookie(): void
    {
        // Se já está logado, não precisa verificar
        if ($this->session->get('isLoggedIn')) {
            return;
        }

        $rememberToken = get_cookie($this->rememberCookieName);
        
        if ($rememberToken) {
            // Buscar usuário pelo token
            $user = $this->userModel->where('remember_token', $rememberToken)->first();
            
            if ($user && $user->status === 'active') {
                // Criar sessão automaticamente
                $this->session->set([
                    'userId'     => $user->id,
                    'userName'   => $user->name,
                    'userEmail'  => $user->email,
                    'userRole'   => $user->role,
                    'isLoggedIn' => true,
                ]);
                
                $this->session->regenerate();
            } else {
                // Token inválido, remover cookie
                delete_cookie($this->rememberCookieName);
            }
        }
    }

    /**
     * Gera e salva token "Lembrar-me"
     */
    protected function setRememberMeCookie(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        
        // Salvar token no banco
        $this->userModel->update($userId, ['remember_token' => $token]);
        
        // Criar cookie (válido por 30 dias)
        set_cookie([
            'name'     => $this->rememberCookieName,
            'value'    => $token,
            'expire'   => 60 * 60 * 24 * 30, // 30 dias
            'httponly' => true,
            'secure'   => (ENVIRONMENT === 'production'),
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Remove cookie e token "Lembrar-me"
     */
    protected function clearRememberMeCookie(): void
    {
        $userId = $this->session->get('userId');
        
        if ($userId) {
            $this->userModel->update($userId, ['remember_token' => null]);
        }
        
        delete_cookie($this->rememberCookieName);
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    /**
     * Exibe o formulário de login
     * 
     * GET /
     * 
     * Se o usuário já estiver logado, redireciona para o painel.
     * 
     * @return string|RedirectResponse
     */
    public function login(): string|RedirectResponse
    {
        // Se já está logado, redireciona para o painel
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(route_to('super.home'));
        }

        return view('Back/Auth/login', [
            'title' => 'Login | Sistema de Agendamentos',
        ]);
    }

    /**
     * Processa tentativa de login
     * 
     * POST /
     * 
     * Valida credenciais e cria sessão do usuário.
     * 
     * @return RedirectResponse
     */
    public function attempt(): RedirectResponse
    {
        // Se já está logado, redireciona
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(route_to('super.home'));
        }

        // Validação dos campos
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        $messages = [
            'email' => [
                'required'    => 'Informe seu e-mail.',
                'valid_email' => 'E-mail inválido.',
            ],
            'password' => [
                'required'   => 'Informe sua senha.',
                'min_length' => 'Senha deve ter pelo menos 6 caracteres.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Tenta autenticar
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->authenticate($email, $password);

        if ($user === null) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'E-mail ou senha incorretos.');
        }

        // Cria sessão do usuário
        $this->session->set([
            'userId'     => $user->id,
            'userName'   => $user->name,
            'userEmail'  => $user->email,
            'userRole'   => $user->role,
            'isLoggedIn' => true,
        ]);

        // Regenera o ID da sessão por segurança
        $this->session->regenerate();

        // Se marcou "Lembrar-me", criar cookie
        if ($this->request->getPost('remember_me')) {
            $this->setRememberMeCookie($user->id);
        }

        return redirect()
            ->to(route_to('super.home'))
            ->with('success', "Bem-vindo(a), {$user->firstName()}!");
    }

    /**
     * Encerra a sessão do usuário
     * 
     * GET /logout
     * 
     * Destrói a sessão e redireciona para o login.
     * 
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        // Limpar cookie "Lembrar-me"
        $this->clearRememberMeCookie();
        
        // Destrói toda a sessão
        $this->session->destroy();

        return redirect()
            ->to(route_to('login'))
            ->with('success', 'Você saiu do sistema.');
    }

    // =========================================================================
    // RECUPERAÇÃO DE SENHA
    // =========================================================================

    /**
     * Exibe formulário de recuperação de senha
     * 
     * GET /password/forgot
     */
    public function forgotPassword(): string
    {
        return view('Back/Auth/forgot_password', [
            'title' => 'Esqueci Minha Senha | Sistema de Agendamentos',
        ]);
    }

    /**
     * Processa envio de link de recuperação
     * 
     * POST /password/forgot
     */
    public function sendResetLink(): RedirectResponse
    {
        $rules = [
            'email' => 'required|valid_email',
        ];

        $messages = [
            'email' => [
                'required'    => 'Informe seu e-mail.',
                'valid_email' => 'E-mail inválido.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        // Não revelar se o e-mail existe ou não (segurança)
        if ($user) {
            // Gerar token de reset
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $this->userModel->update($user->id, [
                'reset_token'      => $token,
                'reset_expires_at' => $expiresAt,
            ]);

            // Enviar e-mail
            $this->sendPasswordResetEmail($user, $token);
        }

        return redirect()
            ->to(route_to('password.forgot'))
            ->with('success', 'Se o e-mail estiver cadastrado, você receberá um link para redefinir sua senha.');
    }

    /**
     * Envia e-mail com link de recuperação
     */
    protected function sendPasswordResetEmail($user, string $token): void
    {
        $resetLink = base_url("password/reset?token={$token}");
        
        $email = \Config\Services::email();
        
        $email->setFrom(config('Email')->fromEmail ?? 'noreply@sistema.com', config('Email')->fromName ?? 'Sistema de Agendamentos');
        $email->setTo($user->email);
        $email->setSubject('Recuperação de Senha - Sistema de Agendamentos');
        
        $message = "
            <h2>Olá, {$user->name}!</h2>
            <p>Recebemos uma solicitação para redefinir sua senha.</p>
            <p>Clique no link abaixo para criar uma nova senha:</p>
            <p><a href='{$resetLink}' style='background: #4e73df; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Redefinir Senha</a></p>
            <p>Este link é válido por 1 hora.</p>
            <p>Se você não solicitou esta alteração, ignore este e-mail.</p>
            <br>
            <p>Atenciosamente,<br>Sistema de Agendamentos</p>
        ";
        
        $email->setMessage($message);
        $email->setMailType('html');
        
        $email->send();
    }

    /**
     * Exibe formulário para nova senha
     * 
     * GET /password/reset?token=xxx
     */
    public function resetPassword(): string|RedirectResponse
    {
        $token = $this->request->getGet('token');
        
        if (! $token) {
            return redirect()
                ->to(route_to('login'))
                ->with('error', 'Token inválido.');
        }

        // Verificar se token é válido
        $user = $this->userModel
            ->where('reset_token', $token)
            ->where('reset_expires_at >', date('Y-m-d H:i:s'))
            ->first();
        
        if (! $user) {
            return redirect()
                ->to(route_to('password.forgot'))
                ->with('error', 'Token expirado ou inválido. Solicite um novo link.');
        }

        return view('Back/Auth/reset_password', [
            'title' => 'Nova Senha | Sistema de Agendamentos',
            'token' => $token,
        ]);
    }

    /**
     * Processa atualização de senha
     * 
     * POST /password/reset
     */
    public function updatePassword(): RedirectResponse
    {
        $rules = [
            'token'            => 'required',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'token' => [
                'required' => 'Token inválido.',
            ],
            'password' => [
                'required'   => 'Informe a nova senha.',
                'min_length' => 'A senha deve ter pelo menos 6 caracteres.',
            ],
            'password_confirm' => [
                'required' => 'Confirme a nova senha.',
                'matches'  => 'As senhas não coincidem.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        
        // Verificar se token é válido
        $user = $this->userModel
            ->where('reset_token', $token)
            ->where('reset_expires_at >', date('Y-m-d H:i:s'))
            ->first();
        
        if (! $user) {
            return redirect()
                ->to(route_to('password.forgot'))
                ->with('error', 'Token expirado ou inválido. Solicite um novo link.');
        }

        // Atualizar senha
        $this->userModel->update($user->id, [
            'password'         => $this->request->getPost('password'),
            'reset_token'      => null,
            'reset_expires_at' => null,
        ]);

        return redirect()
            ->to(route_to('login'))
            ->with('success', 'Senha alterada com sucesso! Faça login com sua nova senha.');
    }
}
