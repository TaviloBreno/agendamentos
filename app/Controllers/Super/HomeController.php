<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\ClientModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * HomeController - Controller do Painel Administrativo
 * 
 * Demonstração do fluxo de dados: Controller → View → Layout
 * 
 * FLUXO DE RENDERIZAÇÃO DO CODEIGNITER 4:
 * =========================================
 * 1. O usuário acessa uma URL (ex: /admin)
 * 2. O Router direciona para este Controller
 * 3. O método index() é executado
 * 4. Criamos um array $data com os dados a serem enviados
 * 5. Chamamos view('caminho/da/view', $data)
 * 6. A view recebe cada índice do array como variável individual
 * 7. A view estende o layout e insere o conteúdo nas seções
 * 8. O HTML final é enviado ao navegador
 */
class HomeController extends BaseController
{
    protected AppointmentModel $appointmentModel;
    
    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
    }
    
    /**
     * Página inicial do painel administrativo
     * 
     * @return string
     */
    public function index(): string
    {
        $userRole = session('userRole') ?? 'user';
        $userEmail = session('userEmail');
        $userName = session('userName');
        
        // Dados base
        $data = [
            'title' => 'Dashboard | Sistema de Agendamentos',
            'pageHeading' => 'Painel Principal',
            'userName' => $userName,
            'systemVersion' => '1.0.0',
        ];
        
        // Para usuário comum - mostra apenas seus próprios dados
        if ($userRole === 'user') {
            $today = date('Y-m-d');
            
            // Busca agendamentos do usuário
            $myAppointments = $this->appointmentModel
                ->where('client_email', $userEmail)
                ->countAllResults(false);
            
            $myAppointmentsToday = $this->appointmentModel
                ->where('client_email', $userEmail)
                ->where('date', $today)
                ->countAllResults(false);
            
            $myUpcomingAppointments = $this->appointmentModel
                ->where('client_email', $userEmail)
                ->where('date >=', $today)
                ->where('status !=', 'cancelled')
                ->orderBy('date', 'ASC')
                ->orderBy('start_time', 'ASC')
                ->limit(5)
                ->find();
            
            $data['totalAgendamentos'] = $myAppointments;
            $data['agendamentosHoje'] = $myAppointmentsToday;
            $data['proximosAgendamentos'] = $myUpcomingAppointments;
            $data['isUserDashboard'] = true;
        } else {
            // Para admin e super - mostra todos os dados
            $today = date('Y-m-d');
            
            $totalAgendamentos = $this->appointmentModel->countAll();
            $agendamentosHoje = $this->appointmentModel
                ->where('date', $today)
                ->countAllResults();
            
            // Clientes ativos
            $clientModel = new ClientModel();
            $clientesAtivos = $clientModel->where('active', 1)->countAllResults();
            
            // Últimos agendamentos
            $ultimosAgendamentos = $this->appointmentModel
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->find();
            
            $data['totalAgendamentos'] = $totalAgendamentos;
            $data['agendamentosHoje'] = $agendamentosHoje;
            $data['clientesAtivos'] = $clientesAtivos;
            $data['ultimosAgendamentos'] = $ultimosAgendamentos;
            $data['isUserDashboard'] = false;
        }

        return view('Back/Home/index', $data);
    }
}
