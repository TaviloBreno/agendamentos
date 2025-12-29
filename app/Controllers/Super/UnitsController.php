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
     * Usada para renderização de tabelas e lógica de negócio.
     * 
     * @var UnitService
     */
    private UnitService $unitService;

    /**
     * Model para operações de banco de dados
     * 
     * Usado para CRUD direto quando não há lógica complexa.
     * 
     * @var UnitModel
     */
    private UnitModel $unitModel;

    /**
     * Construtor - Inicializa Service e Model
     * 
     * =========================================================================
     * INJEÇÃO DE DEPENDÊNCIAS
     * =========================================================================
     * 
     * - UnitService: instanciação direta (classe própria)
     * - UnitModel: via helper model() (reutiliza instância)
     */
    public function __construct()
    {
        $this->unitService = new UnitService();
        $this->unitModel   = model(UnitModel::class);
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
    /**
     * Exibe detalhes de uma unidade
     * 
     * GET /super/units/(:num)
     * 
     * Usa findOrFail() que lança 404 automaticamente se não existir.
     * 
     * @param int $id ID da unidade
     * @return string
     */
    public function show(int $id): string
    {
        // findOrFail() lança PageNotFoundException se não encontrar
        $unit = $this->unitModel->findOrFail($id);

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
     * =========================================================================
     * FLUXO DO MÉTODO
     * =========================================================================
     * 
     * 1. Busca a unidade via findOrFail() (404 automático se não existir)
     * 2. Monta array $data com título e entity
     * 3. Renderiza view Back/Units/edit.php
     * 
     * A view edit.php exibe o formulário preenchido com dados da $unit.
     * 
     * @param int $id ID da unidade
     * @return string HTML da view
     */
    public function edit(int $id): string
    {
        // findOrFail() lança PageNotFoundException se não encontrar
        $unit = $this->unitModel->findOrFail($id);

        $data = [
            'title'       => "Editar Unidade",
            'pageHeading' => "Editar: {$unit->name}",
            'unit'        => $unit,
        ];

        return view('Back/Units/edit', $data);
    }

    /**
     * Processa atualização de unidade
     * 
     * PUT /super/units/(:num)
     * 
     * Requer token CSRF válido (configurado em Config/Filters.php).
     * 
     * @param int $id ID da unidade
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update(int $id)
    {
        // findOrFail() lança 404 se não existir
        $unit = $this->unitModel->findOrFail($id);

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

        $updated = $this->unitModel->update($id, $data);

        if ($updated === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->unitModel->errors());
        }

        return redirect()->to(route_to('super.units.show', $id))
                       ->with('success', 'Unidade atualizada com sucesso!');
    }

    /**
     * Remove uma unidade
     * 
     * DELETE /super/units/(:num)
     * 
     * Requer token CSRF válido.
     * 
     * @param int $id ID da unidade
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete(int $id)
    {
        // findOrFail() lança 404 se não existir
        $unit = $this->unitModel->findOrFail($id);

        $this->unitModel->delete($id);

        return redirect()->to(route_to('super.units'))
                       ->with('success', "Unidade '{$unit->name}' removida com sucesso!");
    }
}
