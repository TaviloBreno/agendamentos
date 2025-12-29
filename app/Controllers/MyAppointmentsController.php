<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\UnitModel;
use App\Models\ServiceModel;
use App\Models\ProfessionalModel;

/**
 * MyAppointmentsController - Gerencia os agendamentos do usuário logado
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Permite que usuários comuns (role: user) visualizem e gerenciem
 * seus próprios agendamentos, sem acesso aos agendamentos de outros.
 * 
 * @package    App\Controllers
 */
class MyAppointmentsController extends BaseController
{
    protected AppointmentModel $appointmentModel;
    protected UnitModel $unitModel;
    protected ServiceModel $serviceModel;
    protected ProfessionalModel $professionalModel;
    
    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->unitModel = new UnitModel();
        $this->serviceModel = new ServiceModel();
        $this->professionalModel = new ProfessionalModel();
    }

    /**
     * Lista os agendamentos do usuário logado
     * 
     * @return string
     */
    public function index()
    {
        $userEmail = session('userEmail');
        
        if (empty($userEmail)) {
            return redirect()->to(route_to('login'))
                ->with('error', 'Você precisa estar logado para ver seus agendamentos.');
        }

        // Busca agendamentos pelo email do usuário
        $appointments = $this->appointmentModel
            ->where('client_email', $userEmail)
            ->orderBy('date', 'DESC')
            ->orderBy('start_time', 'DESC')
            ->findAll();
        
        // Carrega dados relacionados
        $units = $this->unitModel->findAll();
        $services = $this->serviceModel->findAll();
        $professionals = $this->professionalModel->findAll();
        
        // Mapeia para fácil acesso nas views
        $unitsMap = [];
        foreach ($units as $unit) {
            $unitsMap[$unit->id] = $unit;
        }
        
        $servicesMap = [];
        foreach ($services as $service) {
            $servicesMap[$service->id] = $service;
        }
        
        $professionalsMap = [];
        foreach ($professionals as $professional) {
            $professionalsMap[$professional->id] = $professional;
        }

        return view('Back/MyAppointments/index', [
            'title'           => 'Meus Agendamentos',
            'pageHeading'     => 'Meus Agendamentos',
            'appointments'    => $appointments,
            'units'           => $unitsMap,
            'services'        => $servicesMap,
            'professionals'   => $professionalsMap,
        ]);
    }

    /**
     * Exibe detalhes de um agendamento específico
     * 
     * @param int $id
     * @return string
     */
    public function show(int $id)
    {
        $userEmail = session('userEmail');
        
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return redirect()->to(route_to('admin.my.appointments'))
                ->with('error', 'Agendamento não encontrado.');
        }
        
        // Verifica se o agendamento pertence ao usuário logado
        if ($appointment->client_email !== $userEmail) {
            return redirect()->to(route_to('admin.my.appointments'))
                ->with('error', 'Você não tem permissão para ver este agendamento.');
        }
        
        $unit = $this->unitModel->find($appointment->unit_id);
        $service = $this->serviceModel->find($appointment->service_id);
        $professional = $this->professionalModel->find($appointment->professional_id);

        return view('Back/MyAppointments/show', [
            'title'        => 'Detalhes do Agendamento',
            'pageHeading'  => 'Detalhes do Agendamento',
            'appointment'  => $appointment,
            'unit'         => $unit,
            'service'      => $service,
            'professional' => $professional,
        ]);
    }

    /**
     * Cancela um agendamento do usuário
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function cancel(int $id)
    {
        $userEmail = session('userEmail');
        
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return redirect()->to(route_to('admin.my.appointments'))
                ->with('error', 'Agendamento não encontrado.');
        }
        
        // Verifica se o agendamento pertence ao usuário logado
        if ($appointment->client_email !== $userEmail) {
            return redirect()->to(route_to('admin.my.appointments'))
                ->with('error', 'Você não tem permissão para cancelar este agendamento.');
        }
        
        // Verifica se pode ser cancelado (não pode cancelar se já passou ou já está cancelado)
        if ($appointment->status === 'cancelled') {
            return redirect()->to(route_to('admin.my.appointments'))
                ->with('warning', 'Este agendamento já está cancelado.');
        }
        
        if ($appointment->status === 'completed') {
            return redirect()->to(route_to('admin.my.appointments'))
                ->with('warning', 'Não é possível cancelar um agendamento já concluído.');
        }
        
        // Verifica se a data já passou
        $appointmentDate = new \DateTime($appointment->date . ' ' . $appointment->start_time);
        $now = new \DateTime();
        
        if ($appointmentDate < $now) {
            return redirect()->to(route_to('admin.my.appointments'))
                ->with('warning', 'Não é possível cancelar um agendamento que já passou.');
        }
        
        // Cancela o agendamento
        $this->appointmentModel->update($id, ['status' => 'cancelled']);
        
        return redirect()->to(route_to('admin.my.appointments'))
            ->with('success', 'Agendamento cancelado com sucesso.');
    }

    /**
     * Formulário para novo agendamento
     * 
     * @return string
     */
    public function new()
    {
        $units = $this->unitModel->where('active', 1)->findAll();
        
        return view('Back/MyAppointments/form', [
            'title'       => 'Novo Agendamento',
            'pageHeading' => 'Novo Agendamento',
            'units'       => $units,
        ]);
    }

    /**
     * Processa criação de novo agendamento
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $userEmail = session('userEmail');
        $userName = session('userName');
        
        $data = [
            'unit_id'         => $this->request->getPost('unit_id'),
            'professional_id' => $this->request->getPost('professional_id'),
            'service_id'      => $this->request->getPost('service_id'),
            'client_name'     => $userName,
            'client_email'    => $userEmail,
            'client_phone'    => $this->request->getPost('client_phone') ?? '',
            'date'            => $this->request->getPost('date'),
            'start_time'      => $this->request->getPost('start_time'),
            'end_time'        => $this->request->getPost('end_time'),
            'status'          => 'scheduled',
            'notes'           => $this->request->getPost('notes') ?? '',
        ];

        if (!$this->appointmentModel->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errorsValidation', $this->appointmentModel->errors())
                ->with('error', 'Erro ao criar agendamento. Verifique os dados.');
        }

        return redirect()->to(route_to('admin.my.appointments'))
            ->with('success', 'Agendamento criado com sucesso!');
    }
}
