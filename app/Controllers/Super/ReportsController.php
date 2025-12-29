<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\ClientModel;
use App\Models\ProfessionalModel;
use App\Models\ServiceModel;
use App\Models\UnitModel;

/**
 * ReportsController - Relatórios e Dashboards
 */
class ReportsController extends BaseController
{
    protected AppointmentModel $appointmentModel;
    protected ClientModel $clientModel;
    protected ProfessionalModel $professionalModel;
    protected ServiceModel $serviceModel;
    protected UnitModel $unitModel;

    public function __construct()
    {
        $this->appointmentModel = model('AppointmentModel');
        $this->clientModel = model('ClientModel');
        $this->professionalModel = model('ProfessionalModel');
        $this->serviceModel = model('ServiceModel');
        $this->unitModel = model('UnitModel');
    }

    /**
     * Dashboard principal com visão geral
     */
    public function index()
    {
        $data = [
            'title' => 'Relatórios e Dashboard',
            'summary' => $this->getSummary(),
            'todayAppointments' => $this->getTodayAppointments(),
            'weeklyStats' => $this->getWeeklyStats(),
            'topServices' => $this->getTopServices(),
            'topProfessionals' => $this->getTopProfessionals(),
        ];

        return view('Back/Reports/index', $data);
    }

    /**
     * Relatório de agendamentos
     */
    public function appointments()
    {
        $dateStart = $this->request->getGet('date_start') ?? date('Y-m-01');
        $dateEnd = $this->request->getGet('date_end') ?? date('Y-m-t');
        $unitId = $this->request->getGet('unit_id');
        $status = $this->request->getGet('status');

        $builder = $this->appointmentModel
            ->where('date >=', $dateStart)
            ->where('date <=', $dateEnd);

        if ($unitId) {
            $builder->where('unit_id', $unitId);
        }

        if ($status) {
            $builder->where('status', $status);
        }

        $appointments = $builder->orderBy('date', 'ASC')->orderBy('start_time', 'ASC')->findAll();

        $stats = $this->getAppointmentStats($dateStart, $dateEnd, $unitId);

        $data = [
            'title' => 'Relatório de Agendamentos',
            'appointments' => $appointments,
            'stats' => $stats,
            'filters' => [
                'date_start' => $dateStart,
                'date_end' => $dateEnd,
                'unit_id' => $unitId,
                'status' => $status,
            ],
            'units' => $this->unitModel->where('active', 1)->findAll(),
        ];

        return view('Back/Reports/appointments', $data);
    }

    /**
     * Relatório de clientes
     */
    public function clients()
    {
        $dateStart = $this->request->getGet('date_start') ?? date('Y-01-01');
        $dateEnd = $this->request->getGet('date_end') ?? date('Y-12-31');

        // Clientes mais frequentes
        $topClients = $this->getTopClients($dateStart, $dateEnd);

        // Novos clientes por mês
        $newClientsByMonth = $this->getNewClientsByMonth($dateStart, $dateEnd);

        $data = [
            'title' => 'Relatório de Clientes',
            'topClients' => $topClients,
            'newClientsByMonth' => $newClientsByMonth,
            'totalClients' => $this->clientModel->countAllResults(),
            'filters' => [
                'date_start' => $dateStart,
                'date_end' => $dateEnd,
            ],
        ];

        return view('Back/Reports/clients', $data);
    }

    /**
     * Relatório financeiro (por serviços)
     */
    public function financial()
    {
        $dateStart = $this->request->getGet('date_start') ?? date('Y-m-01');
        $dateEnd = $this->request->getGet('date_end') ?? date('Y-m-t');
        $unitId = $this->request->getGet('unit_id');

        $revenueData = $this->getRevenueData($dateStart, $dateEnd, $unitId);
        $revenueByService = $this->getRevenueByService($dateStart, $dateEnd, $unitId);
        $revenueByProfessional = $this->getRevenueByProfessional($dateStart, $dateEnd, $unitId);

        $data = [
            'title' => 'Relatório Financeiro',
            'revenueData' => $revenueData,
            'revenueByService' => $revenueByService,
            'revenueByProfessional' => $revenueByProfessional,
            'filters' => [
                'date_start' => $dateStart,
                'date_end' => $dateEnd,
                'unit_id' => $unitId,
            ],
            'units' => $this->unitModel->where('active', 1)->findAll(),
        ];

        return view('Back/Reports/financial', $data);
    }

    /**
     * API para dados de gráficos
     */
    public function chartData()
    {
        $type = $this->request->getGet('type');
        $dateStart = $this->request->getGet('date_start') ?? date('Y-m-01');
        $dateEnd = $this->request->getGet('date_end') ?? date('Y-m-t');
        $unitId = $this->request->getGet('unit_id');

        $data = match ($type) {
            'appointments_by_status' => $this->getAppointmentsByStatus($dateStart, $dateEnd, $unitId),
            'appointments_by_day' => $this->getAppointmentsByDay($dateStart, $dateEnd, $unitId),
            'revenue_by_month' => $this->getRevenueByMonth($dateStart, $dateEnd, $unitId),
            'top_services' => $this->getTopServices($dateStart, $dateEnd, $unitId, 10),
            default => [],
        };

        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Exportar relatório em CSV
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'appointments';
        $dateStart = $this->request->getGet('date_start') ?? date('Y-m-01');
        $dateEnd = $this->request->getGet('date_end') ?? date('Y-m-t');
        $unitId = $this->request->getGet('unit_id');

        $data = [];
        $filename = '';
        $headers = [];

        switch ($type) {
            case 'appointments':
                $data = $this->getAppointmentsForExport($dateStart, $dateEnd, $unitId);
                $filename = "agendamentos_{$dateStart}_{$dateEnd}.csv";
                $headers = ['ID', 'Data', 'Horário', 'Cliente', 'Profissional', 'Serviço', 'Unidade', 'Status', 'Valor'];
                break;

            case 'clients':
                $data = $this->getClientsForExport();
                $filename = "clientes_" . date('Y-m-d') . ".csv";
                $headers = ['ID', 'Nome', 'Email', 'Telefone', 'CPF', 'Criado em'];
                break;

            case 'financial':
                $data = $this->getFinancialForExport($dateStart, $dateEnd, $unitId);
                $filename = "financeiro_{$dateStart}_{$dateEnd}.csv";
                $headers = ['Data', 'Serviço', 'Profissional', 'Cliente', 'Valor'];
                break;
        }

        return $this->downloadCsv($data, $filename, $headers);
    }

    // =========================================================================
    // MÉTODOS PRIVADOS - Dados Agregados
    // =========================================================================

    private function getSummary(): array
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        return [
            'today_appointments' => $this->appointmentModel->where('date', $today)->countAllResults(),
            'month_appointments' => $this->appointmentModel
                ->where('date >=', $monthStart)
                ->where('date <=', $monthEnd)
                ->countAllResults(),
            'month_completed' => $this->appointmentModel
                ->where('date >=', $monthStart)
                ->where('date <=', $monthEnd)
                ->where('status', 'completed')
                ->countAllResults(),
            'month_cancelled' => $this->appointmentModel
                ->where('date >=', $monthStart)
                ->where('date <=', $monthEnd)
                ->where('status', 'cancelled')
                ->countAllResults(),
            'total_clients' => $this->clientModel->countAllResults(),
            'active_professionals' => $this->professionalModel->where('active', 1)->countAllResults(),
            'active_services' => $this->serviceModel->where('active', 1)->countAllResults(),
            'active_units' => $this->unitModel->where('active', 1)->countAllResults(),
        ];
    }

    private function getTodayAppointments(): array
    {
        return $this->appointmentModel
            ->where('date', date('Y-m-d'))
            ->orderBy('start_time', 'ASC')
            ->findAll();
    }

    private function getWeeklyStats(): array
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = $this->appointmentModel->where('date', $date)->countAllResults();
            $days[] = [
                'date' => $date,
                'day_name' => $this->getDayName($date),
                'count' => $count,
            ];
        }
        return $days;
    }

    private function getTopServices(?string $dateStart = null, ?string $dateEnd = null, ?int $unitId = null, int $limit = 5): array
    {
        $dateStart = $dateStart ?? date('Y-m-01');
        $dateEnd = $dateEnd ?? date('Y-m-t');

        $db = \Config\Database::connect();
        $builder = $db->table('appointments a')
            ->select('s.name as service_name, COUNT(*) as total, SUM(s.price) as revenue')
            ->join('services s', 's.id = a.service_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd)
            ->where('a.status', 'completed');

        if ($unitId) {
            $builder->where('a.unit_id', $unitId);
        }

        return $builder
            ->groupBy('a.service_id')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    private function getTopProfessionals(?string $dateStart = null, ?string $dateEnd = null, int $limit = 5): array
    {
        $dateStart = $dateStart ?? date('Y-m-01');
        $dateEnd = $dateEnd ?? date('Y-m-t');

        $db = \Config\Database::connect();
        return $db->table('appointments a')
            ->select('p.name as professional_name, COUNT(*) as total')
            ->join('professionals p', 'p.id = a.professional_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd)
            ->where('a.status', 'completed')
            ->groupBy('a.professional_id')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    private function getAppointmentStats(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $builder = $this->appointmentModel
            ->where('date >=', $dateStart)
            ->where('date <=', $dateEnd);

        if ($unitId) {
            $builder->where('unit_id', $unitId);
        }

        $total = $builder->countAllResults(false);

        $stats = [];
        foreach (['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'] as $status) {
            $stats[$status] = $this->appointmentModel
                ->where('date >=', $dateStart)
                ->where('date <=', $dateEnd)
                ->where('status', $status);

            if ($unitId) {
                $stats[$status]->where('unit_id', $unitId);
            }

            $stats[$status] = $stats[$status]->countAllResults(false);
        }

        $stats['total'] = $total;
        return $stats;
    }

    private function getTopClients(string $dateStart, string $dateEnd, int $limit = 10): array
    {
        $db = \Config\Database::connect();
        return $db->table('appointments a')
            ->select('c.id, c.name, c.email, c.phone, COUNT(*) as total_appointments')
            ->join('clients c', 'c.id = a.client_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd)
            ->where('a.status !=', 'cancelled')
            ->groupBy('a.client_id')
            ->orderBy('total_appointments', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    private function getNewClientsByMonth(string $dateStart, string $dateEnd): array
    {
        $db = \Config\Database::connect();
        return $db->table('clients')
            ->select("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('created_at >=', $dateStart)
            ->where('created_at <=', $dateEnd)
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getRevenueData(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments a')
            ->select('SUM(s.price) as total_revenue, COUNT(*) as total_appointments')
            ->join('services s', 's.id = a.service_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd)
            ->where('a.status', 'completed');

        if ($unitId) {
            $builder->where('a.unit_id', $unitId);
        }

        return $builder->get()->getRowArray();
    }

    private function getRevenueByService(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments a')
            ->select('s.name as service_name, SUM(s.price) as revenue, COUNT(*) as quantity')
            ->join('services s', 's.id = a.service_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd)
            ->where('a.status', 'completed');

        if ($unitId) {
            $builder->where('a.unit_id', $unitId);
        }

        return $builder
            ->groupBy('a.service_id')
            ->orderBy('revenue', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function getRevenueByProfessional(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments a')
            ->select('p.name as professional_name, SUM(s.price) as revenue, COUNT(*) as quantity')
            ->join('services s', 's.id = a.service_id')
            ->join('professionals p', 'p.id = a.professional_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd)
            ->where('a.status', 'completed');

        if ($unitId) {
            $builder->where('a.unit_id', $unitId);
        }

        return $builder
            ->groupBy('a.professional_id')
            ->orderBy('revenue', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function getAppointmentsByStatus(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments')
            ->select('status, COUNT(*) as total')
            ->where('date >=', $dateStart)
            ->where('date <=', $dateEnd);

        if ($unitId) {
            $builder->where('unit_id', $unitId);
        }

        return $builder
            ->groupBy('status')
            ->get()
            ->getResultArray();
    }

    private function getAppointmentsByDay(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments')
            ->select('date, COUNT(*) as total')
            ->where('date >=', $dateStart)
            ->where('date <=', $dateEnd);

        if ($unitId) {
            $builder->where('unit_id', $unitId);
        }

        return $builder
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getRevenueByMonth(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments a')
            ->select("DATE_FORMAT(a.date, '%Y-%m') as month, SUM(s.price) as revenue")
            ->join('services s', 's.id = a.service_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd)
            ->where('a.status', 'completed');

        if ($unitId) {
            $builder->where('a.unit_id', $unitId);
        }

        return $builder
            ->groupBy("DATE_FORMAT(a.date, '%Y-%m')")
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getAppointmentsForExport(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments a')
            ->select('a.id, a.date, a.start_time, c.name as client_name, p.name as professional_name, s.name as service_name, u.name as unit_name, a.status, s.price')
            ->join('clients c', 'c.id = a.client_id')
            ->join('professionals p', 'p.id = a.professional_id')
            ->join('services s', 's.id = a.service_id')
            ->join('units u', 'u.id = a.unit_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd);

        if ($unitId) {
            $builder->where('a.unit_id', $unitId);
        }

        return $builder
            ->orderBy('a.date', 'ASC')
            ->orderBy('a.start_time', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getClientsForExport(): array
    {
        return $this->clientModel
            ->select('id, name, email, phone, cpf, created_at')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    private function getFinancialForExport(string $dateStart, string $dateEnd, ?int $unitId = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('appointments a')
            ->select('a.date, s.name as service_name, p.name as professional_name, c.name as client_name, s.price')
            ->join('services s', 's.id = a.service_id')
            ->join('professionals p', 'p.id = a.professional_id')
            ->join('clients c', 'c.id = a.client_id')
            ->where('a.date >=', $dateStart)
            ->where('a.date <=', $dateEnd)
            ->where('a.status', 'completed');

        if ($unitId) {
            $builder->where('a.unit_id', $unitId);
        }

        return $builder
            ->orderBy('a.date', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function downloadCsv(array $data, string $filename, array $headers): \CodeIgniter\HTTP\ResponseInterface
    {
        $output = fopen('php://temp', 'w');

        // BOM for UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        fputcsv($output, $headers, ';');

        // Data
        foreach ($data as $row) {
            fputcsv($output, array_values($row), ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($csv);
    }

    private function getDayName(string $date): string
    {
        $days = [
            'Sunday' => 'Dom',
            'Monday' => 'Seg',
            'Tuesday' => 'Ter',
            'Wednesday' => 'Qua',
            'Thursday' => 'Qui',
            'Friday' => 'Sex',
            'Saturday' => 'Sáb',
        ];

        $dayEnglish = date('l', strtotime($date));
        return $days[$dayEnglish] ?? $dayEnglish;
    }
}
