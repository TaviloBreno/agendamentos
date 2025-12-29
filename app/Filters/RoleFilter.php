<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RoleFilter - Filtro de Autorização por Papel (Role)
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Restringe acesso a rotas baseado no papel (role) do usuário.
 * Deve ser usado em conjunto com o AuthFilter.
 * 
 * =========================================================================
 * ROLES DISPONÍVEIS
 * =========================================================================
 * 
 * - super: Super Admin - Acesso total ao sistema
 * - admin: Administrador/Gerente - Acesso limitado (sem tenants, users, whatsapp)
 * - user:  Usuário comum - Acesso apenas a funcionalidades básicas
 * 
 * =========================================================================
 * USO
 * =========================================================================
 * 
 * 1. Registrar em Config/Filters.php:
 *    public array $aliases = [
 *        'role' => \App\Filters\RoleFilter::class,
 *    ];
 * 
 * 2. Aplicar em rotas (Config/Routes.php):
 *    
 *    // Apenas Super Admin
 *    $routes->group('super/tenants', ['filter' => 'role:super'], function($routes) {
 *        // Rotas de tenants
 *    });
 *    
 *    // Super Admin ou Admin
 *    $routes->group('super/services', ['filter' => 'role:super,admin'], function($routes) {
 *        // Rotas de serviços
 *    });
 * 
 * @package    App\Filters
 * @author     Sistema de Agendamentos
 */
class RoleFilter implements FilterInterface
{
    /**
     * Mapeamento de permissões por funcionalidade
     * 
     * Define quais roles podem acessar cada área do sistema
     */
    public static array $permissions = [
        // Funcionalidades exclusivas do Super Admin
        'super_only' => [
            'tenants',     // Gestão de empresas/tenants
            'users',       // Gestão de usuários do sistema
            'whatsapp',    // Configuração de WhatsApp
        ],
        
        // Funcionalidades para Super Admin e Admin (Gerente)
        'admin_up' => [
            'units',         // Gestão de unidades
            'services',      // Gestão de serviços
            'professionals', // Gestão de profissionais
            'clients',       // Gestão de clientes
            'appointments',  // Gestão de agendamentos
            'reports',       // Relatórios
        ],
        
        // Funcionalidades para todos os usuários autenticados
        'all' => [
            'dashboard',        // Dashboard/Home
            'profile',          // Perfil do usuário
            'notifications',    // Notificações
            'messages',         // Mensagens
            'my_appointments',  // Meus Agendamentos (próprios do usuário)
        ],
    ];

    /**
     * Executado ANTES da requisição chegar ao Controller
     * 
     * Verifica se o usuário possui o papel necessário.
     * Se não possuir, redireciona para o dashboard.
     * 
     * @param RequestInterface $request
     * @param array|null $arguments Roles permitidos (ex: ['super'], ['super', 'admin'])
     * @return \CodeIgniter\HTTP\RedirectResponse|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Se não estiver logado, o AuthFilter já vai redirecionar
        if (! $session->get('isLoggedIn')) {
            return;
        }

        $userRole = $session->get('userRole') ?? 'user';
        
        // Se não foram especificados argumentos, permite qualquer role
        if (empty($arguments)) {
            return;
        }

        // Verifica se o role do usuário está entre os permitidos
        if (in_array($userRole, $arguments, true)) {
            return; // Acesso permitido
        }

        // Super Admin tem acesso a tudo
        if ($userRole === 'super') {
            return;
        }

        // Acesso negado - redireciona com mensagem de erro
        return redirect()
            ->to(route_to('super.home'))
            ->with('error', 'Você não tem permissão para acessar esta área.');
    }

    /**
     * Executado APÓS a resposta ser gerada
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

    /**
     * Verifica se um role específico pode acessar uma funcionalidade
     * 
     * @param string $role Role do usuário
     * @param string $feature Funcionalidade a verificar
     * @return bool
     */
    public static function canAccess(string $role, string $feature): bool
    {
        // Super Admin pode tudo
        if ($role === 'super') {
            return true;
        }

        // Verifica funcionalidades de todos
        if (in_array($feature, self::$permissions['all'], true)) {
            return true;
        }

        // Verifica funcionalidades de admin e superior
        if ($role === 'admin' && in_array($feature, self::$permissions['admin_up'], true)) {
            return true;
        }

        // Funcionalidades super_only - apenas super
        if (in_array($feature, self::$permissions['super_only'], true)) {
            return $role === 'super';
        }

        return false;
    }

    /**
     * Retorna lista de funcionalidades que um role pode acessar
     * 
     * @param string $role
     * @return array
     */
    public static function getPermissions(string $role): array
    {
        $permissions = self::$permissions['all'];

        if ($role === 'admin') {
            $permissions = array_merge($permissions, self::$permissions['admin_up']);
        }

        if ($role === 'super') {
            $permissions = array_merge(
                $permissions,
                self::$permissions['admin_up'],
                self::$permissions['super_only']
            );
        }

        return $permissions;
    }
}
