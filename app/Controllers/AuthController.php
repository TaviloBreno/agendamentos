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
        // Destrói toda a sessão
        $this->session->destroy();

        return redirect()
            ->to(route_to('login'))
            ->with('success', 'Você saiu do sistema.');
    }
}
