<?php

namespace App\Libraries;

use CodeIgniter\View\Table as HTMLTable;

/**
 * MyBaseService - Classe base para todas as Services do sistema
 * 
 * =========================================================================
 * SERVICE LAYER PATTERN
 * =========================================================================
 * 
 * O padrão Service Layer centraliza a lógica de negócio em classes
 * especializadas, deixando os controllers apenas como orquestradores
 * de requisição/resposta.
 * 
 * HIERARQUIA DE RESPONSABILIDADES:
 * ┌─────────────────────────────────────────────────────────────────┐
 * │ Controller   → Orquestra: recebe request, chama service, view  │
 * │ Service      → Processa: lógica de negócio, validações, dados  │
 * │ Model        → Persiste: CRUD no banco de dados               │
 * │ Entity       → Representa: objeto com comportamento            │
 * │ View         → Apresenta: apenas exibe dados formatados        │
 * └─────────────────────────────────────────────────────────────────┘
 * 
 * VANTAGENS:
 * - Controllers magros e focados
 * - Lógica reutilizável entre controllers
 * - Testabilidade (services são fáceis de testar em isolamento)
 * - Manutenção simplificada (mudança em um lugar afeta todos)
 * 
 * =========================================================================
 * TABLE CLASS CENTRALIZADA
 * =========================================================================
 * 
 * Esta classe base fornece uma instância da HTMLTable (Table Class do CI4)
 * pré-configurada com template padrão para DataTables.
 * 
 * Services filhas usam: $this->htmlTable->setHeading(...)->addRow(...)
 * 
 * @package    App\Libraries
 * @author     Sistema de Agendamentos
 */
class MyBaseService
{
    /**
     * Mensagem padrão para listas vazias
     * 
     * Constante reutilizável por todas as services filhas.
     * Uso: return self::TXT_NO_DATA;
     * 
     * @var string
     */
    protected const TXT_NO_DATA = '<div class="alert alert-info text-center">
        <i class="fas fa-info-circle mr-2"></i>
        Não há dados para serem exibidos.
    </div>';

    /**
     * Instância da Table Class para geração de tabelas HTML
     * 
     * Usando alias HTMLTable para evitar confusão com tabelas do banco.
     * Inicializada no construtor com template padrão para DataTables.
     * 
     * @var HTMLTable
     */
    protected HTMLTable $htmlTable;

    /**
     * Template padrão para tabelas
     * 
     * Pode ser sobrescrito nas services filhas se necessário.
     * O id="dataTable" é OBRIGATÓRIO para o plugin DataTables funcionar.
     * 
     * @var array<string, string>
     */
    protected array $defaultTableTemplate = [
        'table_open'         => '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">',
        'thead_open'         => '<thead>',
        'thead_close'        => '</thead>',
        'heading_row_start'  => '<tr>',
        'heading_row_end'    => '</tr>',
        'heading_cell_start' => '<th>',
        'heading_cell_end'   => '</th>',
        'tbody_open'         => '<tbody>',
        'tbody_close'        => '</tbody>',
        'row_start'          => '<tr>',
        'row_end'            => '</tr>',
        'cell_start'         => '<td>',
        'cell_end'           => '</td>',
        'row_alt_start'      => '<tr>',
        'row_alt_end'        => '</tr>',
        'cell_alt_start'     => '<td>',
        'cell_alt_end'       => '</td>',
        'table_close'        => '</table>',
    ];

    /**
     * Construtor - Inicializa dependências comuns
     * 
     * =========================================================================
     * INJEÇÃO DE DEPENDÊNCIA NO CODEIGNITER 4
     * =========================================================================
     * 
     * O CodeIgniter 4 oferece várias formas de obter dependências:
     * 
     * 1. service('nome')      → Para services registrados em Config/Services.php
     * 2. model(Classe::class) → Para Models (helper específico)
     * 3. new Classe()         → Instanciação direta (menos flexível)
     * 4. Factories::class()   → Para classes que não são services registrados
     * 
     * A Table Class NÃO está registrada como service por padrão,
     * então usamos instanciação direta aqui. Se quiser usar Factories:
     * 
     *   use Config\Factories;
     *   $this->htmlTable = Factories::class(HTMLTable::class);
     * 
     * Porém, Factories é mais útil para classes com configuração complexa.
     * Para a Table Class, instanciação direta é a prática comum no CI4.
     */
    public function __construct()
    {
        // Inicializa a Table Class
        $this->htmlTable = new HTMLTable();
        
        // Aplica o template padrão com id="dataTable" para DataTables
        $this->htmlTable->setTemplate($this->defaultTableTemplate);
    }

    /**
     * Reseta a Table Class para reutilização
     * 
     * Útil quando a mesma service gera múltiplas tabelas diferentes.
     * Limpa headings, rows e reaplica o template padrão.
     * 
     * @return static Retorna $this para method chaining
     */
    protected function resetTable(): static
    {
        $this->htmlTable->clear();
        $this->htmlTable->setTemplate($this->defaultTableTemplate);
        
        return $this;
    }

    /**
     * Permite customizar o template da tabela
     * 
     * Use quando precisar de classes CSS diferentes ou outro id.
     * 
     * ATENÇÃO: Se alterar o id, o DataTables pode não funcionar!
     * O arquivo datatables-demo.js busca #dataTable especificamente.
     * 
     * @param array<string, string> $template Array com slots do template
     * @return static Retorna $this para method chaining
     */
    protected function setCustomTemplate(array $template): static
    {
        $this->htmlTable->setTemplate($template);
        
        return $this;
    }

    /**
     * Formata uma data para exibição
     * 
     * Aceita Time objects ou strings e retorna formato brasileiro.
     * 
     * @param mixed $date Data a ser formatada
     * @param string $format Formato de saída (padrão: d/m/Y H:i)
     * @return string Data formatada ou placeholder
     */
    protected function formatDate(mixed $date, string $format = 'd/m/Y H:i'): string
    {
        if ($date instanceof \CodeIgniter\I18n\Time) {
            return $date->format($format);
        }
        
        if (is_string($date) && ! empty($date)) {
            try {
                return date($format, strtotime($date));
            } catch (\Exception $e) {
                return $date;
            }
        }
        
        return '-';
    }

    /**
     * Gera HTML para mensagem de lista vazia
     * 
     * Retorna um card amigável quando não há registros.
     * 
     * @param string $icon Classe do ícone FontAwesome
     * @param string $title Título da mensagem
     * @param string $message Mensagem descritiva
     * @param string|null $buttonUrl URL do botão (opcional)
     * @param string|null $buttonText Texto do botão (opcional)
     * @return string HTML da mensagem
     */
    protected function emptyState(
        string $icon = 'fas fa-inbox',
        string $title = 'Nenhum registro encontrado',
        string $message = 'Não há dados para serem exibidos.',
        ?string $buttonUrl = null,
        ?string $buttonText = null
    ): string {
        $button = '';
        if ($buttonUrl && $buttonText) {
            $button = sprintf(
                '<a href="%s" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> %s</a>',
                $buttonUrl,
                esc($buttonText)
            );
        }

        return sprintf(
            '<div class="text-center py-5">
                <i class="%s fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">%s</h5>
                <p class="text-muted mb-3">%s</p>
                %s
            </div>',
            esc($icon),
            esc($title),
            esc($message),
            $button
        );
    }
}
