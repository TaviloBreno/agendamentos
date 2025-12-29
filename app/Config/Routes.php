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
    // Rotas de Profissionais (Professionals)
    // -----------------------------------------------------------------
    $routes->group('professionals', static function ($routes) {
        
        // GET /super/professionals → Lista todos os profissionais
        $routes->get('/', 'ProfessionalsController::index', ['as' => 'super.professionals']);
        
        // GET /super/professionals/new → Formulário de novo profissional
        $routes->get('new', 'ProfessionalsController::new', ['as' => 'super.professionals.new']);
        
        // POST /super/professionals → Processa criação
        $routes->post('/', 'ProfessionalsController::create', ['as' => 'super.professionals.create']);
        
        // GET /super/professionals/(:num) → Exibe detalhes de um profissional
        $routes->get('(:num)', 'ProfessionalsController::show/$1', ['as' => 'super.professionals.show']);
        
        // GET /super/professionals/(:num)/edit → Formulário de edição
        $routes->get('(:num)/edit', 'ProfessionalsController::edit/$1', ['as' => 'super.professionals.edit']);
        
        // PUT /super/professionals/(:num) → Processa atualização
        $routes->put('(:num)', 'ProfessionalsController::update/$1', ['as' => 'super.professionals.update']);
        
        // PUT /super/professionals/(:num)/action → Toggle ativar/desativar
        $routes->put('(:num)/action', 'ProfessionalsController::action/$1', ['as' => 'super.professionals.action']);
        
        // DELETE /super/professionals/(:num) → Remove profissional
        $routes->delete('(:num)', 'ProfessionalsController::delete/$1', ['as' => 'super.professionals.delete']);
    });
    
    // -----------------------------------------------------------------
    // Rotas de Agendamentos (Appointments / Agenda)
    // -----------------------------------------------------------------
    $routes->group('appointments', static function ($routes) {
        
        // GET /super/appointments → Lista/Calendário de agendamentos
        $routes->get('/', 'AppointmentsController::index', ['as' => 'super.appointments']);
        
        // GET /super/appointments/calendar → Dados JSON para calendário
        $routes->get('calendar', 'AppointmentsController::calendar', ['as' => 'super.appointments.calendar']);
        
        // GET /super/appointments/slots → Horários disponíveis JSON
        $routes->get('slots', 'AppointmentsController::slots', ['as' => 'super.appointments.slots']);
        
        // GET /super/appointments/new → Formulário de novo agendamento
        $routes->get('new', 'AppointmentsController::new', ['as' => 'super.appointments.new']);
        
        // POST /super/appointments → Processa criação
        $routes->post('/', 'AppointmentsController::create', ['as' => 'super.appointments.create']);
        
        // GET /super/appointments/(:num) → Exibe detalhes de um agendamento
        $routes->get('(:num)', 'AppointmentsController::show/$1', ['as' => 'super.appointments.show']);
        
        // GET /super/appointments/(:num)/edit → Formulário de edição
        $routes->get('(:num)/edit', 'AppointmentsController::edit/$1', ['as' => 'super.appointments.edit']);
        
        // PUT /super/appointments/(:num) → Processa atualização
        $routes->put('(:num)', 'AppointmentsController::update/$1', ['as' => 'super.appointments.update']);
        
        // GET /super/appointments/(:num)/status → Altera status
        $routes->get('(:num)/status', 'AppointmentsController::status/$1', ['as' => 'super.appointments.status']);
        
        // DELETE /super/appointments/(:num) → Remove agendamento
        $routes->delete('(:num)', 'AppointmentsController::delete/$1', ['as' => 'super.appointments.delete']);
    });
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
