<?php

use App\Controllers\AuthController;
use App\Controllers\SchedulesController;
use App\Controllers\Super\HomeController;
use App\Controllers\Super\UnitsController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================================================================
// ROTAS PÚBLICAS (Área do Cliente)
// =========================================================================
/**
 * Área pública para agendamento de horários
 * 
 * GET  /                    → Home pública com unidades
 * GET  /agendar             → Wizard de agendamento
 * GET  /meus-agendamentos   → Lista de agendamentos do usuário logado
 */
$routes->get('/', 'SchedulesController::home', ['as' => 'home']);
$routes->get('agendar', 'SchedulesController::schedule', ['as' => 'schedule']);
$routes->get('meus-agendamentos', 'SchedulesController::mySchedules', ['as' => 'my.schedules']);

// =========================================================================
// API PÚBLICA (AJAX para Wizard)
// =========================================================================
/**
 * Endpoints JSON para o wizard de agendamento
 */
$routes->group('api', static function ($routes) {
    // Dados para seleção no wizard
    $routes->get('services', 'SchedulesController::getServices', ['as' => 'api.services']);
    $routes->get('professionals', 'SchedulesController::getProfessionals', ['as' => 'api.professionals']);
    $routes->get('months', 'SchedulesController::getMonths', ['as' => 'api.months']);
    $routes->get('calendar', 'SchedulesController::getCalendar', ['as' => 'api.calendar']);
    $routes->get('hours', 'SchedulesController::getAvailableHours', ['as' => 'api.hours']);
    
    // Criação e cancelamento de agendamentos
    $routes->post('schedule', 'SchedulesController::createSchedule', ['as' => 'api.schedule.create']);
    $routes->post('schedule/cancel/(:num)', 'SchedulesController::cancelSchedule/$1', ['as' => 'api.schedule.cancel']);
});

// =========================================================================
// API REST v1 (Para integrações externas)
// =========================================================================
/**
 * API RESTful para integrações com sistemas externos
 * 
 * Recursos disponíveis:
 * - /api/v1/units         → Unidades
 * - /api/v1/services      → Serviços
 * - /api/v1/professionals → Profissionais
 * - /api/v1/appointments  → Agendamentos
 * - /api/v1/clients       → Clientes
 */
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    
    // Unidades
    $routes->get('units', 'UnitsApiController::index');
    $routes->get('units/(:num)', 'UnitsApiController::show/$1');
    $routes->post('units', 'UnitsApiController::create');
    $routes->put('units/(:num)', 'UnitsApiController::update/$1');
    $routes->delete('units/(:num)', 'UnitsApiController::delete/$1');
    
    // Serviços
    $routes->get('services', 'ServicesApiController::index');
    $routes->get('services/(:num)', 'ServicesApiController::show/$1');
    $routes->post('services', 'ServicesApiController::create');
    $routes->put('services/(:num)', 'ServicesApiController::update/$1');
    $routes->delete('services/(:num)', 'ServicesApiController::delete/$1');
    
    // Profissionais
    $routes->get('professionals', 'ProfessionalsApiController::index');
    $routes->get('professionals/(:num)', 'ProfessionalsApiController::show/$1');
    $routes->get('professionals/(:num)/availability', 'ProfessionalsApiController::availability/$1');
    $routes->post('professionals', 'ProfessionalsApiController::create');
    $routes->put('professionals/(:num)', 'ProfessionalsApiController::update/$1');
    $routes->delete('professionals/(:num)', 'ProfessionalsApiController::delete/$1');
    
    // Agendamentos
    $routes->get('appointments', 'AppointmentsApiController::index');
    $routes->get('appointments/available-slots', 'AppointmentsApiController::availableSlots');
    $routes->get('appointments/(:num)', 'AppointmentsApiController::show/$1');
    $routes->post('appointments', 'AppointmentsApiController::create');
    $routes->post('appointments/(:num)/confirm', 'AppointmentsApiController::confirm/$1');
    $routes->post('appointments/(:num)/complete', 'AppointmentsApiController::complete/$1');
    $routes->post('appointments/(:num)/cancel', 'AppointmentsApiController::cancel/$1');
    $routes->put('appointments/(:num)', 'AppointmentsApiController::update/$1');
    $routes->delete('appointments/(:num)', 'AppointmentsApiController::delete/$1');
    
    // Clientes
    $routes->get('clients', 'ClientsApiController::index');
    $routes->get('clients/search', 'ClientsApiController::search');
    $routes->get('clients/(:num)', 'ClientsApiController::show/$1');
    $routes->get('clients/(:num)/appointments', 'ClientsApiController::appointments/$1');
    $routes->post('clients', 'ClientsApiController::create');
    $routes->put('clients/(:num)', 'ClientsApiController::update/$1');
    $routes->delete('clients/(:num)', 'ClientsApiController::delete/$1');
});

// =========================================================================
// ROTAS DE AUTENTICAÇÃO
// =========================================================================
/**
 * Sistema de Login/Logout
 * 
 * GET  /login   → Exibe formulário de login
 * POST /login   → Processa tentativa de login
 * GET  /logout  → Encerra sessão
 */
$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attempt', ['as' => 'login.attempt']);
$routes->get('logout', 'AuthController::logout', ['as' => 'logout']);

// Recuperação de Senha
$routes->get('password/forgot', 'AuthController::forgotPassword', ['as' => 'password.forgot']);
$routes->post('password/forgot', 'AuthController::sendResetLink', ['as' => 'password.send']);
$routes->get('password/reset', 'AuthController::resetPassword', ['as' => 'password.reset']);
$routes->post('password/reset', 'AuthController::updatePassword', ['as' => 'password.update']);

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
    
    // -----------------------------------------------------------------
    // Rotas de Clientes (Clients)
    // -----------------------------------------------------------------
    $routes->group('clients', static function ($routes) {
        
        // GET /super/clients → Lista todos os clientes
        $routes->get('/', 'ClientsController::index', ['as' => 'super.clients']);
        
        // GET /super/clients/search → Busca clientes (JSON)
        $routes->get('search', 'ClientsController::search', ['as' => 'super.clients.search']);
        
        // GET /super/clients/new → Formulário de novo cliente
        $routes->get('new', 'ClientsController::new', ['as' => 'super.clients.new']);
        
        // POST /super/clients → Processa criação
        $routes->post('/', 'ClientsController::create', ['as' => 'super.clients.create']);
        
        // GET /super/clients/(:num) → Exibe detalhes de um cliente
        $routes->get('(:num)', 'ClientsController::show/$1', ['as' => 'super.clients.show']);
        
        // GET /super/clients/(:num)/edit → Formulário de edição
        $routes->get('(:num)/edit', 'ClientsController::edit/$1', ['as' => 'super.clients.edit']);
        
        // PUT /super/clients/(:num) → Processa atualização
        $routes->put('(:num)', 'ClientsController::update/$1', ['as' => 'super.clients.update']);
        
        // PUT /super/clients/(:num)/action → Toggle ativar/desativar
        $routes->put('(:num)/action', 'ClientsController::action/$1', ['as' => 'super.clients.action']);
        
        // DELETE /super/clients/(:num) → Remove cliente
        $routes->delete('(:num)', 'ClientsController::delete/$1', ['as' => 'super.clients.delete']);
    });
    
    // -----------------------------------------------------------------
    // Rotas de Usuários (Users)
    // -----------------------------------------------------------------
    $routes->group('users', static function ($routes) {
        
        // GET /super/users → Lista todos os usuários
        $routes->get('/', 'UsersController::index', ['as' => 'super.users']);
        
        // GET /super/users/new → Formulário de novo usuário
        $routes->get('new', 'UsersController::new', ['as' => 'super.users.new']);
        
        // POST /super/users → Processa criação
        $routes->post('/', 'UsersController::create', ['as' => 'super.users.create']);
        
        // GET /super/users/(:num) → Exibe detalhes de um usuário
        $routes->get('(:num)', 'UsersController::show/$1', ['as' => 'super.users.show']);
        
        // GET /super/users/(:num)/edit → Formulário de edição
        $routes->get('(:num)/edit', 'UsersController::edit/$1', ['as' => 'super.users.edit']);
        
        // PUT /super/users/(:num) → Processa atualização
        $routes->put('(:num)', 'UsersController::update/$1', ['as' => 'super.users.update']);
        
        // PUT /super/users/(:num)/action → Toggle ativar/desativar
        $routes->put('(:num)/action', 'UsersController::action/$1', ['as' => 'super.users.action']);
        
        // DELETE /super/users/(:num) → Remove usuário
        $routes->delete('(:num)', 'UsersController::delete/$1', ['as' => 'super.users.delete']);
    });
    
    // -----------------------------------------------------------------
    // Rotas de Relatórios (Reports)
    // -----------------------------------------------------------------
    $routes->group('reports', static function ($routes) {
        
        // GET /super/reports → Dashboard de relatórios
        $routes->get('/', 'ReportsController::index', ['as' => 'super.reports']);
        
        // GET /super/reports/appointments → Relatório de agendamentos
        $routes->get('appointments', 'ReportsController::appointments', ['as' => 'super.reports.appointments']);
        
        // GET /super/reports/clients → Relatório de clientes
        $routes->get('clients', 'ReportsController::clients', ['as' => 'super.reports.clients']);
        
        // GET /super/reports/financial → Relatório financeiro
        $routes->get('financial', 'ReportsController::financial', ['as' => 'super.reports.financial']);
        
        // GET /super/reports/chart → Dados JSON para gráficos
        $routes->get('chart', 'ReportsController::chartData', ['as' => 'super.reports.chart']);
        
        // GET /super/reports/export → Exportar relatórios em CSV
        $routes->get('export', 'ReportsController::export', ['as' => 'super.reports.export']);
    });
    
    // -----------------------------------------------------------------
    // Rotas de WhatsApp (Configuração e Fila)
    // -----------------------------------------------------------------
    $routes->group('whatsapp', static function ($routes) {
        
        // GET /super/whatsapp → Página de configuração
        $routes->get('/', 'WhatsAppController::index', ['as' => 'super.whatsapp']);
        
        // POST /super/whatsapp/save → Salva configurações
        $routes->post('save', 'WhatsAppController::save', ['as' => 'super.whatsapp.save']);
        
        // GET /super/whatsapp/status → Verifica status conexão (JSON)
        $routes->get('status', 'WhatsAppController::status', ['as' => 'super.whatsapp.status']);
        
        // POST /super/whatsapp/test → Envia mensagem de teste
        $routes->post('test', 'WhatsAppController::test', ['as' => 'super.whatsapp.test']);
        
        // GET /super/whatsapp/queue → Lista fila de notificações
        $routes->get('queue', 'WhatsAppController::queue', ['as' => 'super.whatsapp.queue']);
        
        // POST /super/whatsapp/process → Processa fila manualmente
        $routes->post('process', 'WhatsAppController::process', ['as' => 'super.whatsapp.process']);
        
        // GET /super/whatsapp/qrcode → Obtém QR Code para conexão
        $routes->get('qrcode', 'WhatsAppController::qrcode', ['as' => 'super.whatsapp.qrcode']);
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
