<?php

use App\Controllers\AuthController;
use App\Controllers\Super\HomeController;
use App\Controllers\Super\UnitsController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================================================================
// ROTAS DE AUTENTICAÇÃO
// =========================================================================
/**
 * Sistema de Login/Logout
 * 
 * GET  /        → Exibe formulário de login
 * POST /        → Processa tentativa de login
 * GET  /logout  → Encerra sessão
 */
$routes->get('/', 'AuthController::login', ['as' => 'login']);
$routes->post('/', 'AuthController::attempt', ['as' => 'login.attempt']);
$routes->get('logout', 'AuthController::logout', ['as' => 'logout']);

// =========================================================================
// GRUPO DE ROTAS: SUPER (Painel Administrativo)
// =========================================================================
/**
 * Organização com grupos de rotas para:
 * - Evitar repetição de prefixos
 * - Manter legibilidade
 * - Filtro 'auth' aplicado para exigir login
 * 
 * Estrutura:
 * /super           → Dashboard do painel
 * /super/units     → Listagem de unidades
 * /super/units/new → Formulário de nova unidade
 * etc.
 */
$routes->group('super', ['namespace' => 'App\Controllers\Super', 'filter' => 'auth'], static function ($routes) {
    
    // Dashboard do painel administrativo
    // GET /super → Super\HomeController::index
    $routes->get('/', 'HomeController::index', ['as' => 'super.home']);
    
    // -----------------------------------------------------------------
    // Rotas de Unidades (Units)
    // -----------------------------------------------------------------
    // Subgrupo com prefixo 'units' para todas as rotas de unidades
    $routes->group('units', static function ($routes) {
        
        // GET /super/units → Lista todas as unidades
        $routes->get('/', 'UnitsController::index', ['as' => 'super.units']);
        
        // GET /super/units/new → Formulário de nova unidade
        $routes->get('new', 'UnitsController::new', ['as' => 'super.units.new']);
        
        // POST /super/units → Processa criação
        $routes->post('/', 'UnitsController::create', ['as' => 'super.units.create']);
        
        // GET /super/units/(:num) → Exibe detalhes de uma unidade
        $routes->get('(:num)', 'UnitsController::show/$1', ['as' => 'super.units.show']);
        
        // GET /super/units/(:num)/edit → Formulário de edição
        $routes->get('(:num)/edit', 'UnitsController::edit/$1', ['as' => 'super.units.edit']);
        
        // PUT /super/units/(:num) → Processa atualização
        $routes->put('(:num)', 'UnitsController::update/$1', ['as' => 'super.units.update']);
        
        // PUT /super/units/(:num)/action → Toggle ativar/desativar
        $routes->put('(:num)/action', 'UnitsController::action/$1', ['as' => 'super.units.action']);
        
        // DELETE /super/units/(:num) → Remove unidade
        $routes->delete('(:num)', 'UnitsController::delete/$1', ['as' => 'super.units.delete']);
    });
    
    // -----------------------------------------------------------------
    // Rotas de Serviços (Services)
    // -----------------------------------------------------------------
    $routes->group('services', static function ($routes) {
        
        // GET /super/services → Lista todos os serviços
        $routes->get('/', 'ServicesController::index', ['as' => 'super.services']);
        
        // GET /super/services/new → Formulário de novo serviço
        $routes->get('new', 'ServicesController::new', ['as' => 'super.services.new']);
        
        // POST /super/services → Processa criação
        $routes->post('/', 'ServicesController::create', ['as' => 'super.services.create']);
        
        // GET /super/services/(:num) → Exibe detalhes de um serviço
        $routes->get('(:num)', 'ServicesController::show/$1', ['as' => 'super.services.show']);
        
        // GET /super/services/(:num)/edit → Formulário de edição
        $routes->get('(:num)/edit', 'ServicesController::edit/$1', ['as' => 'super.services.edit']);
        
        // PUT /super/services/(:num) → Processa atualização
        $routes->put('(:num)', 'ServicesController::update/$1', ['as' => 'super.services.update']);
        
        // PUT /super/services/(:num)/action → Toggle ativar/desativar
        $routes->put('(:num)/action', 'ServicesController::action/$1', ['as' => 'super.services.action']);
        
        // DELETE /super/services/(:num) → Remove serviço
        $routes->delete('(:num)', 'ServicesController::delete/$1', ['as' => 'super.services.delete']);
    });
    
    // -----------------------------------------------------------------
    // Futuras rotas do painel administrativo
    // -----------------------------------------------------------------
    // $routes->group('users', static function ($routes) { ... });
    // $routes->group('appointments', static function ($routes) { ... });
});

/**
 * VALIDAÇÃO DAS ROTAS:
 * ====================
 * Execute no terminal: php spark routes
 * 
 * Saída esperada:
 * +--------+----------------------+---------------------------+----------------+---------------+
 * | Method | Route                | Name                      | Handler        | Before Filters|
 * +--------+----------------------+---------------------------+----------------+---------------+
 * | GET    | /                    |                           | Home::index    |               |
 * | GET    | super                | super.home                | HomeController | (auth futuramente)
 * | GET    | super/units          | super.units               | UnitsController|               |
 * | GET    | super/units/new      | super.units.new           | UnitsController|               |
 * | POST   | super/units          | super.units.create        | UnitsController|               |
 * | GET    | super/units/([0-9]+) | super.units.show          | UnitsController|               |
 * | ...    | ...                  | ...                       | ...            |               |
 * +--------+----------------------+---------------------------+----------------+---------------+
 * 
 * NOTA: Filtros de autenticação/permissão serão aplicados posteriormente
 * usando a opção 'filter' no grupo ou em rotas individuais.
 */
