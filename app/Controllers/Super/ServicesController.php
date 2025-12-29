<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Libraries\ServiceService;
use App\Models\ServiceModel;

/**
 * ServicesController - Controller para gerenciar Serviços
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
 * @package    App\Controllers\Super
 * @author     Sistema de Agendamentos
 */
class ServicesController extends BaseController
{
    /**
     * Service responsável pela lógica de Serviços
     */
    private ServiceService $serviceService;

    /**
     * Model para operações de banco de dados
     */
    private ServiceModel $serviceModel;

    /**
     * Construtor - Inicializa Service e Model
     */
    public function __construct()
    {
        $this->serviceService = new ServiceService();
        $this->serviceModel   = model(ServiceModel::class);
    }

    // =========================================================================
    // CRUD METHODS
    // =========================================================================

    /**
     * Lista todos os serviços
     * 
     * GET /super/services
     */
    public function index(): string
    {
        $data = [
            'title'       => 'Serviços',
            'pageHeading' => 'Gerenciar Serviços',
            'services'    => $this->serviceService->renderServices(),
        ];

        return view('Back/Services/index', $data);
    }

    /**
     * Exibe formulário de criação
     * 
     * GET /super/services/new
     */
    public function new(): string
    {
        $data = [
            'title'           => 'Novo Serviço',
            'pageHeading'     => 'Cadastrar Novo Serviço',
            'durationOptions' => ServiceService::getDurationOptions(),
        ];

        return view('Back/Services/form', $data);
    }

    /**
     * Processa criação de novo serviço
     * 
     * POST /super/services
     */
    public function create()
    {
        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'duration'    => $this->request->getPost('duration'),
            'price'       => $this->request->getPost('price'),
            'active'      => $this->request->getPost('active') ?? 0,
        ];

        $insertId = $this->serviceModel->insert($data);

        if ($insertId === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errorsValidation', $this->serviceModel->errors())
                           ->with('danger', 'Verifique os erros de validação.');
        }

        return redirect()->to(route_to('super.services.show', $insertId))
                       ->with('success', 'Serviço cadastrado com sucesso!');
    }

    /**
     * Exibe detalhes de um serviço
     * 
     * GET /super/services/(:num)
     */
    public function show(int $id): string
    {
        $service = $this->serviceModel->findOrFail($id);

        $data = [
            'title'       => "{$service->name} | Serviços",
            'pageHeading' => $service->name,
            'service'     => $service,
        ];

        return view('Back/Services/show', $data);
    }

    /**
     * Exibe formulário de edição
     * 
     * GET /super/services/(:num)/edit
     */
    public function edit(int $id): string
    {
        $service = $this->serviceModel->findOrFail($id);

        $data = [
            'title'           => 'Editar Serviço',
            'pageHeading'     => "Editar: {$service->name}",
            'service'         => $service,
            'durationOptions' => ServiceService::getDurationOptions(),
        ];

        return view('Back/Services/edit', $data);
    }

    /**
     * Processa atualização de serviço
     * 
     * PUT /super/services/(:num)
     */
    public function update(int $id)
    {
        $service = $this->serviceModel->findOrFail($id);

        // Limpa os dados do POST (remove _method, csrf)
        $data = $this->cleanRequest();
        
        // Popula a Entity com os dados do formulário
        $service->fill($data);

        // Verifica se houve alteração
        if (! $service->hasChanged()) {
            return redirect()->back()
                           ->with('info', 'Nenhuma alteração detectada.');
        }

        // Tenta salvar
        $saved = $this->serviceModel->save($service);

        if ($saved === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errorsValidation', $this->serviceModel->errors())
                           ->with('danger', 'Verifique os erros de validação.');
        }

        return redirect()->to(route_to('super.services'))
                       ->with('success', "Serviço '{$service->name}' atualizado com sucesso!");
    }

    /**
     * Remove um serviço
     * 
     * DELETE /super/services/(:num)
     */
    public function delete(int $id)
    {
        $service = $this->serviceModel->findOrFail($id);

        $this->serviceModel->delete($id);

        return redirect()->to(route_to('super.services'))
                       ->with('success', "Serviço '{$service->name}' removido com sucesso!");
    }

    /**
     * Alterna o status de um serviço (Ativar/Desativar)
     * 
     * PUT /super/services/(:num)/action
     */
    public function action(int $id)
    {
        $service = $this->serviceModel->findOrFail($id);
        
        $wasActive = $service->isActive();
        $service->setAction();

        $saved = $this->serviceModel->save($service);

        if ($saved === false) {
            return redirect()->back()
                           ->with('danger', 'Erro ao alterar status do serviço.')
                           ->with('errorsValidation', $this->serviceModel->errors());
        }

        $actionText = $wasActive ? 'desativado' : 'ativado';
        
        return redirect()->to(route_to('super.services'))
                       ->with('success', "Serviço '{$service->name}' {$actionText} com sucesso!");
    }
}
