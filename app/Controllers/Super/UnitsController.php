<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Libraries\UnitService;
use App\Models\UnitModel;
use App\Models\ServiceModel;

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
        // Busca serviços ativos para o multi-select
        $serviceModel = model(ServiceModel::class);

        $data = [
            'title'             => 'Nova Unidade',
            'pageHeading'       => 'Cadastrar Nova Unidade',
            'timesInterval'     => $this->unitService->renderTimesInterval(),
            'availableServices' => $serviceModel->getForDropdownDetailed(),
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

        // Busca serviços ativos para o multi-select
        $serviceModel = model(ServiceModel::class);

        $data = [
            'title'             => 'Editar Unidade',
            'pageHeading'       => "Editar: {$unit->name}",
            'unit'              => $unit,
            // Dropdown de tempo de atendimento renderizado pela Service
            // Passa o valor atual para pré-selecionar a opção correta
            'timesInterval'     => $this->unitService->renderTimesInterval($unit->service_time),
            // Serviços disponíveis para seleção
            'availableServices' => $serviceModel->getForDropdownDetailed(),
        ];

        return view('Back/Units/edit', $data);
    }

    /**
     * Processa atualização de unidade
     * 
     * PUT /super/units/(:num)
     * 
     * =========================================================================
     * FLUXO COMPLETO DE ATUALIZAÇÃO
     * =========================================================================
     * 
     * 1. Valida o método HTTP (PUT via method spoofing)
     * 2. Busca a entidade existente (segurança: evita manipulação de URL)
     * 3. Limpa os dados do POST (remove _method, csrf)
     * 4. Popula a Entity via fill()
     * 5. Verifica se houve alteração via hasChanged()
     * 6. Se não mudou → redirect com mensagem info
     * 7. Se mudou → valida e salva via Model
     * 8. Trata erros de validação com errorsValidation
     * 9. Sucesso → redirect para listagem com flash success
     * 
     * =========================================================================
     * SOBRE O FILL() DA ENTITY
     * =========================================================================
     * 
     * O método fill() da Entity popula automaticamente as propriedades
     * com base nas chaves do array. As chaves devem corresponder aos
     * atributos da Entity ou às colunas mapeadas em $datamap.
     * 
     * IMPORTANTE: fill() não ignora campos desconhecidos, por isso usamos
     * cleanRequest() para remover _method e csrf antes.
     * 
     * =========================================================================
     * SOBRE O HASCHANGED() DA ENTITY
     * =========================================================================
     * 
     * hasChanged() verifica se a Entity tem diferenças entre os valores
     * originais (do banco) e os atuais (após fill). Evita UPDATEs
     * desnecessários quando o usuário clica em "Salvar" sem alterar nada.
     * 
     * Requer token CSRF válido (configurado em Config/Filters.php).
     * 
     * @param int $id ID da unidade
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update(int $id)
    {
        /**
         * VALIDAÇÃO DO MÉTODO HTTP
         * ========================
         * 
         * Mesmo usando method spoofing (_method=PUT), o método real é POST.
         * Verificamos via checkMethod() para garantir consistência.
         * 
         * NOTA: Como usamos PUT nas rotas, o CI4 já valida. Este check
         * é uma camada extra de segurança/documentação.
         */
        // $this->checkMethod('put'); // Opcional: CI4 já valida pela rota

        /**
         * BUSCA A ENTIDADE EXISTENTE
         * ==========================
         * 
         * Buscamos do banco novamente para:
         * 1. Garantir que o registro existe (findOrFail lança 404)
         * 2. Ter o estado original para comparação via hasChanged()
         * 3. Evitar manipulação de HTML/URL por usuário mal-intencionado
         */
        $unit = $this->unitModel->findOrFail($id);

        /**
         * LIMPA E POPULA A ENTITY
         * =======================
         * 
         * cleanRequest() remove _method e csrf dos dados do POST.
         * fill() popula as propriedades da Entity automaticamente.
         * 
         * O campo 'services' precisa de tratamento especial (JSON),
         * então ajustamos após obter os dados limpos.
         */
        $data = $this->cleanRequest();
        
        // Tratamento especial para o campo services (JSON)
        if (isset($data['services'])) {
            $data['services'] = json_encode($data['services']);
        } else {
            $data['services'] = json_encode([]);
        }
        
        // Popula a Entity com os dados do formulário
        $unit->fill($data);

        /**
         * VERIFICA SE HOUVE ALTERAÇÃO
         * ==========================
         * 
         * hasChanged() compara os valores atuais com os originais.
         * Se nada mudou, não faz sentido executar UPDATE no banco.
         * 
         * Retornamos com mensagem informativa para o usuário.
         */
        if (! $unit->hasChanged()) {
            return redirect()->back()
                           ->with('info', 'Não há dados para atualizar. Nenhuma alteração foi detectada.');
        }

        /**
         * TENTA SALVAR (COM VALIDAÇÃO AUTOMÁTICA)
         * =======================================
         * 
         * save() no Model aceita uma Entity e executa:
         * 1. Validação usando $validationRules
         * 2. INSERT ou UPDATE baseado na presença de ID
         * 
         * Como a Entity já tem ID, será UPDATE.
         * O placeholder {id} nas regras is_unique é substituído.
         */
        $saved = $this->unitModel->save($unit);

        if ($saved === false) {
            /**
             * VALIDAÇÃO FALHOU
             * ================
             * 
             * Usamos 'errorsValidation' para os erros por campo
             * e 'danger' para a mensagem geral.
             * 
             * O helper showErrorInput() busca em 'errorsValidation'.
             * O partial _messages.php exibe ambos.
             */
            return redirect()->back()
                           ->withInput()
                           ->with('errorsValidation', $this->unitModel->errors())
                           ->with('danger', 'Verifique os erros de validação e tente novamente.');
        }

        /**
         * SUCESSO
         * =======
         * 
         * Redireciona para a listagem com mensagem de sucesso.
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

    /**
     * Alterna o status de uma unidade (Ativar/Desativar)
     * 
     * PUT /super/units/(:num)/action
     * 
     * =========================================================================
     * FLUXO DO TOGGLE DE STATUS
     * =========================================================================
     * 
     * 1. Valida método HTTP (PUT via method spoofing)
     * 2. Busca a entidade existente (findOrFail → 404 se não existir)
     * 3. Alterna o status via setAction() (método herdado de MyBaseEntity)
     * 4. Persiste com save() no Model
     * 5. Retorna com mensagem de sucesso ou erro
     * 
     * =========================================================================
     * SOBRE O setAction()
     * =========================================================================
     * 
     * O método setAction() vem de MyBaseEntity e faz o toggle:
     * - Se está ATIVO → torna INATIVO
     * - Se está INATIVO → torna ATIVO
     * 
     * Também disponíveis: activate(), deactivate() para ações específicas.
     * 
     * =========================================================================
     * SEGURANÇA
     * =========================================================================
     * 
     * - CSRF validado automaticamente pelo filtro global
     * - Method spoofing (_method=PUT) usado no form
     * - findOrFail() previne manipulação de IDs inválidos
     * 
     * @param int $id ID da unidade
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function action(int $id)
    {
        /**
         * BUSCA A ENTIDADE
         * ================
         * 
         * findOrFail() lança PageNotFoundException automaticamente
         * se o registro não existir, evitando verificações manuais.
         */
        $unit = $this->unitModel->findOrFail($id);

        /**
         * GUARDA O ESTADO ANTERIOR
         * ========================
         * 
         * Útil para a mensagem de feedback ao usuário.
         */
        $wasActive = $unit->isActive();

        /**
         * ALTERNA O STATUS
         * ================
         * 
         * setAction() vem de MyBaseEntity:
         * - Se active = true → define como false
         * - Se active = false → define como true
         */
        $unit->setAction();

        /**
         * PERSISTE NO BANCO
         * =================
         * 
         * save() aceita Entity e:
         * - Valida usando $validationRules do Model
         * - Executa UPDATE (pois Entity já tem ID)
         */
        $saved = $this->unitModel->save($unit);

        if ($saved === false) {
            return redirect()->back()
                           ->with('danger', 'Erro ao alterar status da unidade.')
                           ->with('errorsValidation', $this->unitModel->errors());
        }

        /**
         * MENSAGEM DE SUCESSO
         * ===================
         * 
         * Informa ao usuário a ação realizada de forma clara.
         */
        $actionText = $wasActive ? 'desativada' : 'ativada';
        
        return redirect()->to(route_to('super.units'))
                       ->with('success', "Unidade '{$unit->name}' {$actionText} com sucesso!");
    }
}
