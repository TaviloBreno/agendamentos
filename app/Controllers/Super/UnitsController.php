<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Libraries\UnitService;
use App\Models\UnitModel;

/**
 * UnitsController - Controller para gerenciar Unidades
 * 
 * =========================================================================
 * PADRÃO SERVICE LAYER
 * =========================================================================
 * 
 * Este controller segue o princípio de "Controller Magro":
 * - NÃO contém lógica de negócio
 * - NÃO monta tabelas HTML
 * - NÃO faz formatação de dados
 * 
 * Ele apenas ORQUESTRA:
 * 1. Recebe a requisição
 * 2. Chama a Service apropriada
 * 3. Retorna a View com os dados
 * 
 * Toda a lógica de montagem de tabelas está em UnitService.
 * 
 * @package    App\Controllers\Super
 * @author     Sistema de Agendamentos
 */
class UnitsController extends BaseController
{
    /**
     * Service responsável pela lógica de Unidades
     * 
     * Inicializada no construtor, usada em todos os métodos.
     * 
     * @var UnitService
     */
    private UnitService $unitService;

    /**
     * Construtor - Inicializa a Service
     * 
     * Usamos instanciação direta da Service.
     * Alternativa: usar Factories ou injeção de dependência.
     */
    public function __construct()
    {
        $this->unitService = new UnitService();
    }

    // =========================================================================
    // CRUD METHODS
    // =========================================================================

    /**
     * Lista todas as unidades
     * 
     * GET /super/units
     * 
     * Controller minimalista:
     * - Chama a service para renderizar a tabela
     * - Passa o HTML pronto para a view
     * - View apenas exibe: <?= $units ?>
     * 
     * @return string
     */
    public function index(): string
    {
        $data = [
            'title'       => 'Unidades',
            'pageHeading' => 'Gerenciar Unidades',
            'units'       => $this->unitService->renderUnits(),
        ];

        return view('Back/Units/index', $data);
    }

    /**
     * Exibe formulário de criação
     * 
     * GET /super/units/new
     * 
     * @return string
     */
    public function new(): string
    {
        $data = [
            'title'       => 'Nova Unidade',
            'pageHeading' => 'Cadastrar Nova Unidade',
        ];

        return view('Back/Units/form', $data);
    }

    /**
     * Processa criação de nova unidade
     * 
     * POST /super/units
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $model = model(UnitModel::class);
        
        $data = [
            'name'         => $this->request->getPost('name'),
            'email'        => $this->request->getPost('email'),
            'phone'        => $this->request->getPost('phone'),
            'coordinator'  => $this->request->getPost('coordinator'),
            'address'      => $this->request->getPost('address'),
            'services'     => json_encode($this->request->getPost('services') ?? []),
            'start_time'   => $this->request->getPost('start_time'),
            'end_time'     => $this->request->getPost('end_time'),
            'service_time' => $this->request->getPost('service_time'),
            'active'       => $this->request->getPost('active') ?? 0,
        ];

        $insertId = $model->insert($data);

        if ($insertId === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $model->errors());
        }

        return redirect()->to(route_to('super.units.show', $insertId))
                       ->with('success', 'Unidade cadastrada com sucesso!');
    }

    /**
     * Exibe detalhes de uma unidade
     * 
     * GET /super/units/(:num)
     * 
     * @param int|string $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function show($id)
    {
        $unit = $this->unitService->find($id);

        if ($unit === null) {
            return redirect()->to(route_to('super.units'))
                           ->with('error', 'Unidade não encontrada.');
        }

        $data = [
            'title'       => "{$unit->name} | Unidades",
            'pageHeading' => $unit->name,
            'unit'        => $unit,
        ];

        return view('Back/Units/show', $data);
    }

    /**
     * Exibe formulário de edição
     * 
     * GET /super/units/(:num)/edit
     * 
     * @param int|string $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function edit($id)
    {
        $unit = $this->unitService->find($id);

        if ($unit === null) {
            return redirect()->to(route_to('super.units'))
                           ->with('error', 'Unidade não encontrada.');
        }

        $data = [
            'title'       => "Editar {$unit->name}",
            'pageHeading' => "Editar: {$unit->name}",
            'unit'        => $unit,
        ];

        return view('Back/Units/form', $data);
    }

    /**
     * Processa atualização de unidade
     * 
     * PUT /super/units/(:num)
     * 
     * @param int|string $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $model = model(UnitModel::class);
        $unit  = $model->find($id);

        if ($unit === null) {
            return redirect()->to(route_to('super.units'))
                           ->with('error', 'Unidade não encontrada.');
        }

        $data = [
            'name'         => $this->request->getPost('name'),
            'email'        => $this->request->getPost('email'),
            'phone'        => $this->request->getPost('phone'),
            'coordinator'  => $this->request->getPost('coordinator'),
            'address'      => $this->request->getPost('address'),
            'services'     => json_encode($this->request->getPost('services') ?? []),
            'start_time'   => $this->request->getPost('start_time'),
            'end_time'     => $this->request->getPost('end_time'),
            'service_time' => $this->request->getPost('service_time'),
            'active'       => $this->request->getPost('active') ?? 0,
        ];

        $updated = $model->update($id, $data);

        if ($updated === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $model->errors());
        }

        return redirect()->to(route_to('super.units.show', $id))
                       ->with('success', 'Unidade atualizada com sucesso!');
    }

    /**
     * Remove uma unidade
     * 
     * DELETE /super/units/(:num)
     * 
     * @param int|string $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        $model = model(UnitModel::class);
        $unit  = $model->find($id);

        if ($unit === null) {
            return redirect()->to(route_to('super.units'))
                           ->with('error', 'Unidade não encontrada.');
        }

        $model->delete($id);

        return redirect()->to(route_to('super.units'))
                       ->with('success', "Unidade '{$unit->name}' removida com sucesso!");
    }
}
