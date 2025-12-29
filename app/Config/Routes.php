<?php

use App\Controllers\Super\HomeController;
use App\Controllers\Super\UnitsController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================================================================
// ROTA PRINCIPAL (Frontend)
// =========================================================================
$routes->get('/', 'Home::index');

// =========================================================================
// GRUPO DE ROTAS: SUPER (Painel Administrativo)
// =========================================================================
/**
 * Organização com grupos de rotas para:
 * - Evitar repetição de prefixos
 * - Manter legibilidade
 * - Facilitar aplicação de filtros (autenticação/permissão) futuramente
 * 
 * Estrutura:
 * /super           → Dashboard do painel
 * /super/units     → Listagem de unidades
 * /super/units/new → Formulário de nova unidade
 * etc.
 */
$routes->group('super', ['namespace' => 'App\Controllers\Super'], static function ($routes) {
    
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
        
        // DELETE /super/units/(:num) → Remove unidade
        $routes->delete('(:num)', 'UnitsController::delete/$1', ['as' => 'super.units.delete']);
    });
    
    // -----------------------------------------------------------------
    // Futuras rotas do painel administrativo
    // -----------------------------------------------------------------
    // $routes->group('services', static function ($routes) { ... });
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
