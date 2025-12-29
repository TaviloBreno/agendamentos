<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter - Filtro de Autenticação
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Protege rotas que requerem autenticação.
 * Redireciona usuários não logados para a página de login.
 * 
 * =========================================================================
 * USO
 * =========================================================================
 * 
 * 1. Registrar em Config/Filters.php:
 *    public array $aliases = [
 *        'auth' => \App\Filters\AuthFilter::class,
 *    ];
 * 
 * 2. Aplicar em rotas (Config/Routes.php):
 *    $routes->group('super', ['filter' => 'auth'], function($routes) {
 *        // Rotas protegidas
 *    });
 * 
 * @package    App\Filters
 * @author     Sistema de Agendamentos
 */
class AuthFilter implements FilterInterface
{
    /**
     * Executado ANTES da requisição chegar ao Controller
     * 
     * Verifica se o usuário está logado.
     * Se não estiver, redireciona para o login.
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return \CodeIgniter\HTTP\RedirectResponse|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Verifica se está logado
        if (! $session->get('isLoggedIn')) {
            // Salva a URL que o usuário tentou acessar
            $session->set('redirect_url', current_url());
            
            return redirect()
                ->to('/')
                ->with('error', 'Você precisa fazer login para acessar esta área.');
        }

        // Verifica se o usuário ainda existe e está ativo (opcional, para segurança extra)
        // Descomente se quiser validar em cada requisição:
        /*
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($session->get('userId'));
        
        if ($user === null || ! $user->isActive()) {
            $session->destroy();
            return redirect()
                ->to('/')
                ->with('error', 'Sua sessão expirou. Faça login novamente.');
        }
        */
    }

    /**
     * Executado APÓS a resposta ser gerada
     * 
     * Não é necessário para autenticação básica.
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer após a requisição
    }
}
