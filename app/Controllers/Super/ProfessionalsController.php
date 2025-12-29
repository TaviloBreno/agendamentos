<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\ProfessionalModel;
use App\Models\UnitModel;
use App\Models\ServiceModel;
use App\Libraries\ProfessionalService;

/**
 * ProfessionalsController - CRUD de Profissionais
 * 
 * =========================================================================
 * ROTAS MAPEADAS
 * =========================================================================
 * 
 * GET    /super/professionals           → index()   Lista todos
 * GET    /super/professionals/new       → new()     Formulário de criação
 * POST   /super/professionals           → create()  Processa criação
 * GET    /super/professionals/(:num)    → show()    Exibe detalhes
 * GET    /super/professionals/(:num)/edit → edit() Formulário de edição
 * PUT    /super/professionals/(:num)    → update()  Processa atualização
 * PUT    /super/professionals/(:num)/action → action() Toggle ativo/inativo
 * DELETE /super/professionals/(:num)    → delete()  Processa exclusão
 * 
 * @package    App\Controllers\Super
 */
class ProfessionalsController extends BaseController
{
    protected ProfessionalModel $professionalModel;
    protected ProfessionalService $professionalService;

    /**
     * Construtor - Inicializa dependências
     */
    public function __construct()
    {
        $this->professionalModel = model(ProfessionalModel::class);
        $this->professionalService = new ProfessionalService();
    }

    /**
     * Lista todos os profissionais
     * 
     * GET /super/professionals
     */
    public function index(): string
    {
        $data = [
            'title'       => 'Profissionais',
            'pageHeading' => 'Gerenciar Profissionais',
            'tableHtml'   => $this->professionalService->renderProfessionals(),
        ];

        return view('Back/Professionals/index', $data);
    }

    /**
     * Exibe formulário de criação
     * 
     * GET /super/professionals/new
     */
    public function new(): string
    {
        $unitModel = model(UnitModel::class);
        $serviceModel = model(ServiceModel::class);

        $data = [
            'title'             => 'Novo Profissional',
            'pageHeading'       => 'Cadastrar Novo Profissional',
            'units'             => $unitModel->getForDropdown(),
            'availableServices' => $serviceModel->getForDropdownDetailed(),
        ];

        return view('Back/Professionals/form', $data);
    }

    /**
     * Processa criação de novo profissional
     * 
     * POST /super/professionals
     */
    public function create()
    {
        $data = [
            'unit_id'   => $this->request->getPost('unit_id') ?: null,
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'phone'     => $this->request->getPost('phone'),
            'specialty' => $this->request->getPost('specialty'),
            'bio'       => $this->request->getPost('bio'),
            'services'  => $this->request->getPost('services') ?? [],
            'active'    => $this->request->getPost('active') ?? 0,
        ];

        $insertId = $this->professionalModel->insert($data);

        if ($insertId === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errorsValidation', $this->professionalModel->errors())
                           ->with('danger', 'Verifique os erros de validação.');
        }

        return redirect()->to(route_to('super.professionals.show', $insertId))
                       ->with('success', 'Profissional cadastrado com sucesso!');
    }

    /**
     * Exibe detalhes de um profissional
     * 
     * GET /super/professionals/(:num)
     */
    public function show(int $id): string
    {
        $professional = $this->professionalModel->findOrFail($id);

        $data = [
            'title'        => "{$professional->name} | Profissionais",
            'pageHeading'  => $professional->name,
            'professional' => $professional,
        ];

        return view('Back/Professionals/show', $data);
    }

    /**
     * Exibe formulário de edição
     * 
     * GET /super/professionals/(:num)/edit
     */
    public function edit(int $id): string
    {
        $professional = $this->professionalModel->findOrFail($id);
        $unitModel = model(UnitModel::class);
        $serviceModel = model(ServiceModel::class);

        $data = [
            'title'             => 'Editar Profissional',
            'pageHeading'       => "Editar: {$professional->name}",
            'professional'      => $professional,
            'units'             => $unitModel->getForDropdown(),
            'availableServices' => $serviceModel->getForDropdownDetailed(),
        ];

        return view('Back/Professionals/edit', $data);
    }

    /**
     * Processa atualização de profissional
     * 
     * PUT /super/professionals/(:num)
     */
    public function update(int $id)
    {
        $professional = $this->professionalModel->findOrFail($id);

        $data = [
            'unit_id'   => $this->request->getPost('unit_id') ?: null,
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'phone'     => $this->request->getPost('phone'),
            'specialty' => $this->request->getPost('specialty'),
            'bio'       => $this->request->getPost('bio'),
            'services'  => $this->request->getPost('services') ?? [],
            'active'    => $this->request->getPost('active') ?? 0,
        ];

        $professional->fill($data);

        if (! $professional->hasChanged()) {
            return redirect()->back()
                           ->with('info', 'Nenhuma alteração detectada.');
        }

        // Converte services para JSON antes de salvar
        $data['services'] = json_encode($data['services']);

        $saved = $this->professionalModel->update($id, $data);

        if ($saved === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errorsValidation', $this->professionalModel->errors())
                           ->with('danger', 'Verifique os erros de validação.');
        }

        return redirect()->to(route_to('super.professionals'))
                       ->with('success', 'Profissional atualizado com sucesso!');
    }

    /**
     * Toggle ativo/inativo
     * 
     * PUT /super/professionals/(:num)/action
     */
    public function action(int $id)
    {
        $professional = $this->professionalModel->findOrFail($id);
        
        $newStatus = $professional->active ? 0 : 1;
        $statusText = $newStatus ? 'ativado' : 'desativado';

        $this->professionalModel->update($id, ['active' => $newStatus]);

        return redirect()->back()
                       ->with('success', "Profissional {$statusText} com sucesso!");
    }

    /**
     * Processa exclusão de profissional
     * 
     * DELETE /super/professionals/(:num)
     */
    public function delete(int $id)
    {
        $professional = $this->professionalModel->findOrFail($id);

        $this->professionalModel->delete($id);

        return redirect()->to(route_to('super.professionals'))
                       ->with('success', "Profissional \"{$professional->name}\" excluído com sucesso!");
    }
}
