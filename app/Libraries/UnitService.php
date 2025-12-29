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
         * 
         * IMPORTANTE: A coluna "Ações" vem PRIMEIRO para facilitar
         * o acesso rápido aos botões de ação em cada linha.
         */
        $this->htmlTable->setHeading(
            'Ações',
            'Nome',
            'E-mail',
            'Telefone',
            'Início',
            'Fim',
            'Criado em'
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
                $this->renderBtnActions($unit),
                esc($unit->name),
                esc($unit->email),
                esc($unit->phone),
                esc($unit->start_time),
                esc($unit->end_time),
                $this->formatDate($unit->created_at)
            );
        }
        
        // Gera e retorna o HTML da tabela
        return $this->htmlTable->generate();
    }

    /**
     * Renderiza dropdown de ações para uma unidade
     * 
     * =========================================================================
     * DROPDOWN BOOTSTRAP 4 GERADO NO BACK-END
     * =========================================================================
     * 
     * Centraliza a construção do HTML de ações na Service.
     * Usa o helper anchor() do CodeIgniter para gerar links.
     * 
     * ESTRUTURA DO DROPDOWN:
     * ┌─────────────────────────────────────┐
     * │ [Ações ▼]                           │
     * │  ├─ 👁️ Visualizar                   │
     * │  ├─ ✏️ Editar                       │
     * │  ├─ ───────────                     │
     * │  ├─ ✅ Ativar/Desativar             │
     * │  ├─ ───────────                     │
     * │  └─ 🗑️ Excluir                      │
     * └─────────────────────────────────────┘
     * 
     * @param Unit $unit Entity da unidade
     * @return string HTML do dropdown completo
     */
    private function renderBtnActions(Unit $unit): string
    {
        // Carrega o helper HTML para usar anchor()
        helper('html');
        
        // Monta os itens do menu usando anchor() + route_to()
        $viewLink = anchor(
            route_to('super.units.show', $unit->id),
            '<i class="fas fa-eye fa-sm fa-fw mr-2 text-info"></i> Visualizar',
            ['class' => 'dropdown-item']
        );
        
        $editLink = anchor(
            route_to('super.units.edit', $unit->id),
            '<i class="fas fa-edit fa-sm fa-fw mr-2 text-warning"></i> Editar',
            ['class' => 'dropdown-item']
        );
        
        // Status toggle (Ativar/Desativar) - placeholder
        $statusText = $unit->active 
            ? '<i class="fas fa-ban fa-sm fa-fw mr-2 text-secondary"></i> Desativar'
            : '<i class="fas fa-check fa-sm fa-fw mr-2 text-success"></i> Ativar';
        $statusLink = '<a class="dropdown-item" href="#" onclick="alert(\'Em desenvolvimento\'); return false;">' . $statusText . '</a>';
        
        // Link de exclusão - placeholder com confirmação
        $deleteLink = '<a class="dropdown-item text-danger" href="#" '
            . 'onclick="if(confirm(\'Excluir ' . esc($unit->name, 'js') . '?\')) { alert(\'Em desenvolvimento\'); } return false;">'
            . '<i class="fas fa-trash fa-sm fa-fw mr-2"></i> Excluir</a>';
        
        // Monta o dropdown completo
        return '
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" 
                        type="button" 
                        id="dropdownActions' . $unit->id . '" 
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false">
                    <i class="fas fa-cog"></i> Ações
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownActions' . $unit->id . '">
                    ' . $viewLink . '
                    ' . $editLink . '
                    <div class="dropdown-divider"></div>
                    ' . $statusLink . '
                    <div class="dropdown-divider"></div>
                    ' . $deleteLink . '
                </div>
            </div>
        ';
    }

    /**
     * Renderiza os botões de ação inline (versão compacta)
     * 
     * Alternativa ao dropdown para layouts que preferem botões lado a lado.
     * Mantido para referência e possível uso futuro.
     * 
     * @param Unit $unit Entity da unidade
     * @return string HTML dos botões
     * @deprecated Use renderBtnActions() para dropdown
     */
    protected function renderActionButtons(Unit $unit): string
    {
        helper('html');
        
        $viewBtn = anchor(
            route_to('super.units.show', $unit->id),
            '<i class="fas fa-eye"></i>',
            ['class' => 'btn btn-info btn-sm', 'title' => 'Ver detalhes']
        );
        
        $editBtn = anchor(
            route_to('super.units.edit', $unit->id),
            '<i class="fas fa-edit"></i>',
            ['class' => 'btn btn-warning btn-sm', 'title' => 'Editar']
        );
        
        $deleteBtn = '<button type="button" '
            . 'class="btn btn-danger btn-sm btn-delete" '
            . 'data-id="' . $unit->id . '" '
            . 'data-name="' . esc($unit->name) . '" '
            . 'title="Excluir">'
            . '<i class="fas fa-trash"></i></button>';
        
        return $viewBtn . ' ' . $editBtn . ' ' . $deleteBtn;
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
