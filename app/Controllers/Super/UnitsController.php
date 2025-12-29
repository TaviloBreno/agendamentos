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
     * 2. Renderiza dropdown de service_time via UnitService
     * 3. Monta array $data com título, entity e dropdown renderizado
     * 4. Renderiza view Back/Units/edit.php
     * 
     * A view edit.php exibe o formulário preenchido com dados da $unit.
     * O dropdown de service_time já vem pronto da Service, mantendo
     * a view limpa e a lógica centralizada.
     * 
     * @param int $id ID da unidade
     * @return string HTML da view
     */
    public function edit(int $id): string
    {
        // findOrFail() lança PageNotFoundException se não encontrar
        $unit = $this->unitModel->findOrFail($id);

        $data = [
            'title'         => 'Editar Unidade',
            'pageHeading'   => "Editar: {$unit->name}",
            'unit'          => $unit,
            // Dropdown de tempo de atendimento renderizado pela Service
            // Passa o valor atual para pré-selecionar a opção correta
            'timesInterval' => $this->unitService->renderTimesInterval($unit->service_time),
        ];

        return view('Back/Units/edit', $data);
    }

    /**
     * Processa atualização de unidade
     * 
     * PUT /super/units/(:num)
     * 
     * =========================================================================
     * FLUXO DE VALIDAÇÃO E PERSISTÊNCIA
     * =========================================================================
     * 
     * 1. Busca a unidade via findOrFail() (segurança: evita manipulação de URL)
     * 2. Coleta dados do POST
     * 3. Tenta atualizar via Model (validação automática pelas rules)
     * 4. Se falhar → redirect back com withInput() e erros
     * 5. Se sucesso → redirect para listagem com flash message
     * 
     * IMPORTANTE sobre is_unique no UPDATE:
     * O Model usa o placeholder {id} nas rules. Quando chamamos update($id, $data),
     * o CI4 automaticamente substitui {id} pelo valor de $id, permitindo que
     * o próprio registro não "viole" a regra de unicidade.
     * 
     * Requer token CSRF válido (configurado em Config/Filters.php).
     * 
     * @param int $id ID da unidade
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update(int $id)
    {
        /**
         * BUSCA O REGISTRO EXISTENTE
         * ==========================
         * 
         * Mesmo que já tenhamos o $id, buscamos do banco para:
         * 1. Garantir que o registro existe (findOrFail lança 404)
         * 2. Evitar manipulação de HTML/URL por usuário mal-intencionado
         * 3. Ter acesso ao nome original para mensagens
         */
        $unit = $this->unitModel->findOrFail($id);

        /**
         * COLETA DADOS DO POST
         * ====================
         * 
         * Usamos getPost() para cada campo esperado.
         * O campo 'active' usa ?? 0 como fallback (técnica do hidden).
         * O campo 'services' é convertido para JSON.
         */
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

        /**
         * TENTA ATUALIZAR (COM VALIDAÇÃO AUTOMÁTICA)
         * ==========================================
         * 
         * O Model valida automaticamente usando $validationRules.
         * O placeholder {id} nas regras is_unique é substituído por $id.
         * 
         * Se a validação falhar, update() retorna false e os erros
         * ficam disponíveis em $this->unitModel->errors().
         */
        $updated = $this->unitModel->update($id, $data);

        if ($updated === false) {
            /**
             * VALIDAÇÃO FALHOU
             * ================
             * 
             * - back() → Volta para a página anterior (edit)
             * - withInput() → Mantém os dados digitados (para old())
             * - with('errors', ...) → Flash data com array de erros
             */
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->unitModel->errors());
        }

        /**
         * SUCESSO
         * =======
         * 
         * Redireciona para a listagem com mensagem de sucesso.
         * Usamos route_to() para gerar a URL da rota nomeada.
         */
        return redirect()->to(route_to('super.units'))
                       ->with('success', "Unidade '{$unit->name}' atualizada com sucesso!");
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
