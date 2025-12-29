<?php

namespace App\Libraries;

use App\Models\UnitModel;
use App\Entities\Unit;
use CodeIgniter\I18n\Time;

/**
 * UnitService - Service para lógica de negócio de Unidades
 * 
 * =========================================================================
 * RESPONSABILIDADES DESTA SERVICE
 * =========================================================================
 * 
 * - Montar tabelas HTML para listagem
 * - Formatar dados para exibição
 * - Centralizar queries específicas
 * - Aplicar regras de negócio relacionadas a Unidades
 * 
 * O Controller apenas orquestra: chama a service e retorna view.
 * Esta service não faz echo, apenas retorna strings/arrays.
 * 
 * =========================================================================
 * COMO USAR NO CONTROLLER
 * =========================================================================
 * 
 *   use App\Libraries\UnitService;
 * 
 *   class UnitsController extends BaseController
 *   {
 *       private UnitService $unitService;
 * 
 *       public function __construct()
 *       {
 *           $this->unitService = new UnitService();
 *       }
 * 
 *       public function index(): string
 *       {
 *           return view('Back/Units/index', [
 *               'title' => 'Unidades',
 *               'units' => $this->unitService->renderUnits(),
 *           ]);
 *       }
 *   }
 * 
 * @package    App\Libraries
 * @author     Sistema de Agendamentos
 */
class UnitService extends MyBaseService
{
    /**
     * Renderiza a tabela de Unidades para listagem
     * 
     * Recupera todas as unidades do banco, monta a tabela HTML
     * usando a Table Class herdada e retorna o HTML pronto.
     * 
     * =========================================================================
     * FLUXO DO MÉTODO
     * =========================================================================
     * 
     * 1. Busca registros via Model (ordenados por nome)
     * 2. Se vazio → retorna mensagem amigável (empty state)
     * 3. Se houver dados → configura headings
     * 4. Loop nas entities → addRow() com dados formatados
     * 5. Retorna $this->htmlTable->generate()
     * 
     * @return string HTML da tabela ou mensagem de lista vazia
     */
    public function renderUnits(): string
    {
        // Reseta a tabela para garantir estado limpo
        $this->resetTable();
        
        /**
         * RECUPERA OS REGISTROS
         * =====================
         * 
         * Usando o helper model() que:
         * - Instancia o Model se ainda não existir
         * - Reutiliza instância em chamadas subsequentes
         * - Retorna objetos Unit (Entity) por causa do $returnType do Model
         * 
         * @var Unit[] $units Array de objetos Unit
         */
        $units = model(UnitModel::class)
            ->orderBy('name', 'ASC')
            ->findAll();
        
        // Se não houver registros, retorna empty state amigável
        if (empty($units)) {
            return $this->emptyState(
                icon: 'fas fa-building',
                title: 'Nenhuma unidade cadastrada',
                message: 'Comece cadastrando a primeira unidade do sistema.',
                buttonUrl: route_to('super.units.new'),
                buttonText: 'Cadastrar Unidade'
            );
        }
        
        /**
         * CONFIGURA O CABEÇALHO DA TABELA
         * ===============================
         * 
         * setHeading() aceita múltiplos argumentos (um por coluna)
         * ou um array. Aqui usamos argumentos para legibilidade.
         */
        $this->htmlTable->setHeading(
            'Nome',
            'E-mail',
            'Telefone',
            'Início',
            'Fim',
            'Criado em',
            'Ações'
        );
        
        /**
         * ADICIONA AS LINHAS
         * ==================
         * 
         * Percorre o array de Entities e usa addRow() para cada registro.
         * Como $units contém objetos Unit, acessamos propriedades diretamente.
         */
        foreach ($units as $unit) {
            $this->htmlTable->addRow(
                esc($unit->name),
                esc($unit->email),
                esc($unit->phone),
                esc($unit->start_time),
                esc($unit->end_time),
                $this->formatDate($unit->created_at),
                $this->renderActionButtons($unit)
            );
        }
        
        // Gera e retorna o HTML da tabela
        return $this->htmlTable->generate();
    }

    /**
     * Renderiza os botões de ação para uma unidade
     * 
     * Gera HTML dos botões Ver, Editar e Excluir.
     * Usa route_to() para URLs nomeadas (manutenção facilitada).
     * 
     * @param Unit $unit Entity da unidade
     * @return string HTML dos botões
     */
    protected function renderActionButtons(Unit $unit): string
    {
        $viewUrl   = route_to('super.units.show', $unit->id);
        $editUrl   = route_to('super.units.edit', $unit->id);
        $unitName  = esc($unit->name);
        
        return <<<HTML
            <a href="{$viewUrl}" class="btn btn-info btn-sm" title="Ver detalhes">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{$editUrl}" class="btn btn-warning btn-sm" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" 
                    class="btn btn-danger btn-sm btn-delete" 
                    data-id="{$unit->id}" 
                    data-name="{$unitName}" 
                    title="Excluir">
                <i class="fas fa-trash"></i>
            </button>
        HTML;
    }

    /**
     * Busca uma unidade por ID
     * 
     * Retorna a Entity ou null se não encontrar.
     * Útil para os métodos show/edit/delete do controller.
     * 
     * @param int|string $id ID da unidade
     * @return Unit|null
     */
    public function find(int|string $id): ?Unit
    {
        return model(UnitModel::class)->find($id);
    }

    /**
     * Retorna todas as unidades ativas
     * 
     * @return Unit[]
     */
    public function getActiveUnits(): array
    {
        return model(UnitModel::class)
            ->where('active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Retorna contagem de unidades
     * 
     * @param bool $onlyActive Se true, conta apenas ativas
     * @return int
     */
    public function count(bool $onlyActive = false): int
    {
        $model = model(UnitModel::class);
        
        if ($onlyActive) {
            $model->where('active', 1);
        }
        
        return $model->countAllResults();
    }
}
