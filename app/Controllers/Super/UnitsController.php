<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\UnitModel;
use App\Entities\Unit;
use CodeIgniter\View\Table;

/**
 * UnitsController - Controller para gerenciar Unidades
 * 
 * =========================================================================
 * TABLE CLASS DO CODEIGNITER
 * =========================================================================
 * 
 * A Table Class permite gerar tabelas HTML no back-end, mantendo a view limpa.
 * 
 * Vantagens:
 * - View sem loops PHP (apenas <?= $unitsTable ?>)
 * - Template customizável (classes CSS, IDs)
 * - Separação clara entre lógica e apresentação
 * 
 * @package    App\Controllers\Super
 * @author     Sistema de Agendamentos
 */
class UnitsController extends BaseController
{
    /**
     * Lista todas as unidades
     * 
     * GET /super/units
     * 
     * @return string
     */
    public function index(): string
    {
        // Recupera todos os registros (retorna array de Entities)
        $units = model(UnitModel::class)->findAll();
        
        // Instancia a Table Class
        $table = new Table();
        
        /**
         * TEMPLATE DA TABLE CLASS
         * =======================
         * Define as classes CSS e o id="dataTable" para o DataTables funcionar.
         * 
         * IMPORTANTE: O id="dataTable" é obrigatório!
         * O arquivo datatables-demo.js busca esse id para aplicar o plugin.
         * Se alterar, o DataTables não funcionará (tabela "crua").
         * Use Ctrl+F5 para hard refresh se os assets não carregarem.
         */
        $template = [
            'table_open' => '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">',
            'thead_open' => '<thead>',
            'thead_close' => '</thead>',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'tbody_open' => '<tbody>',
            'tbody_close' => '</tbody>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>',
        ];
        
        $table->setTemplate($template);
        
        // Define os cabeçalhos da tabela
        $table->setHeading('Nome', 'E-mail', 'Telefone', 'Início', 'Fim', 'Criado em', 'Ações');
        
        // Adiciona as linhas com dados das Entities
        foreach ($units as $unit) {
            // Formata created_at (Time → string legível)
            $createdAt = $unit->created_at 
                ? $unit->created_at->format('d/m/Y H:i') 
                : '-';
            
            // Botões de ação
            $actions = '
                <a href="' . route_to('super.units.show', $unit->id) . '" class="btn btn-info btn-sm" title="Ver">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="' . route_to('super.units.edit', $unit->id) . '" class="btn btn-warning btn-sm" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $unit->id . '" data-name="' . esc($unit->name) . '" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            ';
            
            // Adiciona a linha
            $table->addRow(
                esc($unit->name),
                esc($unit->email),
                esc($unit->phone),
                esc($unit->start_time),
                esc($unit->end_time),
                $createdAt,
                $actions
            );
        }
        
        // Gera o HTML da tabela
        $unitsTable = $table->generate();
        
        // Se não houver registros, exibe mensagem
        if (empty($units)) {
            $unitsTable = '<div class="text-center py-5">
                <i class="fas fa-building fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Nenhuma unidade cadastrada</h5>
                <p class="text-muted mb-3">Comece cadastrando a primeira unidade do sistema.</p>
                <a href="' . route_to('super.units.new') . '" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Cadastrar Unidade
                </a>
            </div>';
        }

        $data = [
            'title'       => 'Unidades | Sistema de Agendamentos',
            'pageHeading' => 'Gerenciar Unidades',
            'unitsTable'  => $unitsTable,
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
            'title'       => 'Nova Unidade | Sistema',
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
        $unit = model(UnitModel::class)->find($id);

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
        $unit = model(UnitModel::class)->find($id);

        if ($unit === null) {
            return redirect()->to(route_to('super.units'))
                           ->with('error', 'Unidade não encontrada.');
        }

        $data = [
            'title'       => "Editar {$unit->name} | Sistema",
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
