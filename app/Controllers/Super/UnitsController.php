<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\UnitModel;
use App\Entities\Unit;

/**
 * UnitsController - Controller para gerenciar Unidades
 * 
 * =========================================================================
 * EXEMPLO COMPLETO: MODEL + ENTITY NO CODEIGNITER 4
 * =========================================================================
 * 
 * Este controller demonstra como usar o UnitModel e a Entity Unit
 * para realizar operações CRUD com retorno orientado a objetos.
 * 
 * FLUXO DE DADOS:
 * ---------------
 * 1. Controller instancia o Model
 * 2. Model executa query no banco
 * 3. Model retorna Entity (objeto Unit) em vez de array
 * 4. Controller usa métodos da Entity para formatar/processar dados
 * 5. Controller envia Entity ou dados para a View
 * 
 * @package    App\Controllers\Super
 * @author     Sistema de Agendamentos
 */
class UnitsController extends BaseController
{
    /**
     * Instância do UnitModel
     * 
     * @var UnitModel
     */
    protected UnitModel $unitModel;

    /**
     * Construtor - Inicializa o Model
     * 
     * Boa prática: Injetar dependências no construtor
     */
    public function __construct()
    {
        // Instancia o Model uma vez para usar em todos os métodos
        $this->unitModel = new UnitModel();
    }

    /**
     * Lista todas as unidades
     * 
     * GET /admin/units
     * 
     * @return string
     */
    public function index(): string
    {
        /**
         * BUSCAR TODOS OS REGISTROS
         * =========================
         * findAll() retorna um array de objetos Unit (não arrays!)
         * porque configuramos $returnType = Unit::class no Model.
         * 
         * @var array<Unit> $units
         */
        $units = $this->unitModel->findAll();

        /**
         * Alternativa: Buscar apenas unidades ativas
         * Usando método customizado do Model
         */
        // $units = $this->unitModel->getActiveUnits();

        /**
         * Alternativa: Buscar com paginação
         */
        // $units = $this->unitModel->paginate(10);
        // $pager = $this->unitModel->pager;

        $data = [
            'title'       => 'Unidades | Sistema de Agendamentos',
            'pageHeading' => 'Gerenciar Unidades',
            'units'       => $units,
            // 'pager'    => $pager ?? null, // Para paginação
        ];

        return view('Back/Units/index', $data);
    }

    /**
     * Exibe detalhes de uma unidade
     * 
     * GET /admin/units/:id
     * 
     * @param int|string $id
     * @return string
     */
    public function show($id): string
    {
        /**
         * BUSCAR UM REGISTRO POR ID
         * =========================
         * find($id) retorna um objeto Unit ou null se não encontrar.
         * 
         * @var Unit|null $unit
         */
        $unit = $this->unitModel->find($id);

        // Se não encontrou, redireciona ou mostra erro
        if ($unit === null) {
            return redirect()->to('/admin/units')
                           ->with('error', 'Unidade não encontrada.');
        }

        /**
         * ACESSANDO PROPRIEDADES DA ENTITY
         * =================================
         * Como o retorno é um objeto Unit, podemos:
         * 
         * 1. Acessar propriedades diretamente:
         *    $unit->name
         *    $unit->email
         *    $unit->phone
         * 
         * 2. Usar métodos de comportamento:
         *    $unit->startHour()        → "08:00"
         *    $unit->getWorkingHours()  → "08:00 às 18:00"
         *    $unit->isActive()         → true/false
         *    $unit->getStatusBadge()   → "<span class='badge...'>Ativa</span>"
         * 
         * 3. Acessar datas como objetos Time:
         *    $unit->created_at->format('d/m/Y H:i')
         */
        
        // Exemplo de uso dos métodos da Entity
        $workingHours = $unit->getWorkingHours();   // "08:00 às 18:00"
        $isActive     = $unit->isActive();          // true ou false
        $statusBadge  = $unit->getStatusBadge();    // HTML do badge
        $services     = $unit->getServicesArray();  // [1, 2, 3]

        $data = [
            'title'        => "{$unit->name} | Unidades",
            'pageHeading'  => $unit->name,
            'unit'         => $unit,
            'workingHours' => $workingHours,
        ];

        return view('Back/Units/show', $data);
    }

    /**
     * Exibe formulário de criação
     * 
     * GET /admin/units/new
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
     * POST /admin/units
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        /**
         * INSERIR NOVO REGISTRO - MÉTODO 1: Array direto
         * ==============================================
         * Passa um array com os dados. O Model valida e insere.
         */
        $dataFromForm = [
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
         * insert() retorna:
         * - O ID do registro inserido (int) em caso de sucesso
         * - false em caso de falha (validação, etc.)
         */
        $insertId = $this->unitModel->insert($dataFromForm);

        if ($insertId === false) {
            // Validação falhou - retorna com erros
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->unitModel->errors());
        }

        return redirect()->to("/admin/units/{$insertId}")
                       ->with('success', 'Unidade cadastrada com sucesso!');
    }

    /**
     * Processa criação usando Entity
     * 
     * Exemplo alternativo usando objeto Entity
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function createWithEntity()
    {
        /**
         * INSERIR NOVO REGISTRO - MÉTODO 2: Usando Entity
         * ================================================
         * Cria um objeto Unit, define as propriedades e passa para o Model.
         * Os mutators da Entity são executados (formatação automática).
         */
        $unit = new Unit();
        
        // Define propriedades - Mutators são chamados automaticamente!
        // Ex: setName() capitaliza, setEmail() converte para minúsculas
        $unit->name         = $this->request->getPost('name');
        $unit->email        = $this->request->getPost('email');
        $unit->phone        = $this->request->getPost('phone');  // Formatação automática!
        $unit->coordinator  = $this->request->getPost('coordinator');
        $unit->address      = $this->request->getPost('address');
        $unit->services     = $this->request->getPost('services') ?? [];
        $unit->start_time   = $this->request->getPost('start_time');
        $unit->end_time     = $this->request->getPost('end_time');
        $unit->service_time = $this->request->getPost('service_time');
        $unit->active       = $this->request->getPost('active') ?? 0;

        // Insere a Entity no banco
        $insertId = $this->unitModel->insert($unit);

        if ($insertId === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->unitModel->errors());
        }

        return redirect()->to("/admin/units/{$insertId}")
                       ->with('success', 'Unidade cadastrada com sucesso!');
    }

    /**
     * Exibe formulário de edição
     * 
     * GET /admin/units/:id/edit
     * 
     * @param int|string $id
     * @return string
     */
    public function edit($id): string
    {
        $unit = $this->unitModel->find($id);

        if ($unit === null) {
            return redirect()->to('/admin/units')
                           ->with('error', 'Unidade não encontrada.');
        }

        $data = [
            'title'       => "Editar {$unit->name} | Sistema",
            'pageHeading' => "Editar Unidade: {$unit->name}",
            'unit'        => $unit,
        ];

        return view('Back/Units/form', $data);
    }

    /**
     * Processa atualização de unidade
     * 
     * PUT/PATCH /admin/units/:id
     * 
     * @param int|string $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $unit = $this->unitModel->find($id);

        if ($unit === null) {
            return redirect()->to('/admin/units')
                           ->with('error', 'Unidade não encontrada.');
        }

        /**
         * ATUALIZAR REGISTRO
         * ==================
         * update($id, $data) atualiza o registro com o ID especificado.
         * 
         * Também é possível usar save() que detecta automaticamente
         * se deve inserir (sem ID) ou atualizar (com ID).
         */
        $dataToUpdate = [
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

        $updated = $this->unitModel->update($id, $dataToUpdate);

        if ($updated === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->unitModel->errors());
        }

        return redirect()->to("/admin/units/{$id}")
                       ->with('success', 'Unidade atualizada com sucesso!');
    }

    /**
     * Remove uma unidade
     * 
     * DELETE /admin/units/:id
     * 
     * @param int|string $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        $unit = $this->unitModel->find($id);

        if ($unit === null) {
            return redirect()->to('/admin/units')
                           ->with('error', 'Unidade não encontrada.');
        }

        /**
         * DELETAR REGISTRO
         * ================
         * delete($id) remove o registro permanentemente.
         * 
         * Se $useSoftDeletes = true no Model, apenas marca deleted_at.
         * Como não temos essa coluna, é exclusão física.
         */
        $this->unitModel->delete($id);

        return redirect()->to('/admin/units')
                       ->with('success', "Unidade '{$unit->name}' removida com sucesso!");
    }

    // =========================================================================
    // EXEMPLOS ADICIONAIS DE USO
    // =========================================================================

    /**
     * Exemplo: Buscar por email
     */
    public function findByEmailExample()
    {
        // Usando método customizado do Model
        $unit = $this->unitModel->findByEmail('contato@unidade.com');
        
        if ($unit) {
            echo $unit->name;           // "Unidade Centro"
            echo $unit->startHour();    // "08:00"
            echo $unit->isActive();     // true
        }
    }

    /**
     * Exemplo: Buscar com condições
     */
    public function queryExamples()
    {
        // WHERE simples
        $activeUnits = $this->unitModel
            ->where('active', 1)
            ->findAll();

        // WHERE com múltiplas condições
        $units = $this->unitModel
            ->where('active', 1)
            ->like('name', 'Centro')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Primeiro resultado
        $firstUnit = $this->unitModel
            ->where('active', 1)
            ->first();

        // Contar registros
        $totalActive = $this->unitModel
            ->where('active', 1)
            ->countAllResults();

        // Selecionar campos específicos
        $unitNames = $this->unitModel
            ->select('id, name')
            ->findAll();
    }
}
