<?php

namespace App\Cells;

/**
 * ButtonsCell - View Cell para renderizar botões de ação
 * 
 * =========================================================================
 * O QUE É UMA VIEW CELL?
 * =========================================================================
 * 
 * View Cells são "mini-controllers" que renderizam pequenos fragmentos
 * de HTML reutilizáveis. Diferente de Views parciais, Cells podem ter
 * lógica própria e são ideais para componentes que aparecem em várias
 * páginas com pequenas variações.
 * 
 * =========================================================================
 * USO EM VIEWS OU SERVICES
 * =========================================================================
 * 
 * // Opção 1: Helper view_cell() (recomendado)
 * <?= view_cell('ButtonsCell::action', [
 *     'route'      => route_to('super.units.action', $unit->id),
 *     'activated'  => $unit->isActive(),
 *     'textAction' => $unit->textToAction(),
 *     'iconAction' => $unit->iconToAction(),
 *     'classAction'=> $unit->classToAction(),
 * ]) ?>
 * 
 * // Opção 2: Instanciar diretamente (em Services)
 * $cell = new \App\Cells\ButtonsCell();
 * echo $cell->action([...]);
 * 
 * =========================================================================
 * QUANDO USAR CELLS
 * =========================================================================
 * 
 * - Botões de formulário que aparecem em vários lugares
 * - Componentes de UI repetitivos (cards, badges, menus)
 * - Elementos que precisam de lógica antes de renderizar
 * - Alternativa a "partials" quando há processamento envolvido
 * 
 * @package    App\Cells
 * @author     Sistema de Agendamentos
 * @link       https://codeigniter.com/user_guide/outgoing/view_cells.html
 */
class ButtonsCell
{
    /**
     * Renderiza botão de ação (Ativar/Desativar) como item de dropdown
     * 
     * =========================================================================
     * DESCRIÇÃO
     * =========================================================================
     * 
     * Gera um formulário inline (display: inline) com:
     * - CSRF token automático (via form_open)
     * - Method spoofing (_method = PUT)
     * - Botão estilizado como dropdown-item do Bootstrap 4
     * 
     * O form é necessário porque links <a> não podem fazer PUT/POST seguro.
     * 
     * =========================================================================
     * PARÂMETROS ESPERADOS
     * =========================================================================
     * 
     * @param array $params Array com:
     *   - route (string): URL completa da rota (ex.: route_to('super.units.action', 1))
     *   - activated (bool): Estado atual do registro (true = ativo)
     *   - textAction (string): Texto do botão ("Ativar" ou "Desativar")
     *   - iconAction (string, opcional): Classe do ícone FontAwesome (ex.: 'fa-check', 'fa-ban')
     *   - classAction (string, opcional): Classe CSS adicional (ex.: 'text-success', 'text-secondary')
     *   - btnClass (string, opcional): Classes extras para o botão
     * 
     * @return string HTML do formulário com botão
     * 
     * =========================================================================
     * EXEMPLO DE SAÍDA HTML
     * =========================================================================
     * 
     * <form action="/super/units/1/action" method="post" class="d-inline">
     *   <input type="hidden" name="csrf" value="abc123...">
     *   <input type="hidden" name="_method" value="PUT">
     *   <button type="submit" class="dropdown-item text-success">
     *     <i class="fas fa-check fa-sm fa-fw mr-2"></i> Ativar
     *   </button>
     * </form>
     */
    public function action(array $params): string
    {
        // Carrega o helper de formulários
        helper('form');
        
        // Extrai parâmetros com valores padrão
        $route       = $params['route'] ?? '#';
        $activated   = $params['activated'] ?? false;
        $textAction  = $params['textAction'] ?? ($activated ? 'Desativar' : 'Ativar');
        $iconAction  = $params['iconAction'] ?? ($activated ? 'fa-ban' : 'fa-check');
        $classAction = $params['classAction'] ?? ($activated ? 'text-secondary' : 'text-success');
        $btnClass    = $params['btnClass'] ?? '';
        
        /**
         * MONTA O FORMULÁRIO
         * ==================
         * 
         * form_open() com 3 parâmetros:
         * 1. action (URL)
         * 2. attributes (array de atributos HTML)
         * 3. hidden (campos hidden adicionais, inclui _method para spoofing)
         * 
         * O CSRF é adicionado automaticamente pelo form_open().
         */
        $html = form_open(
            $route,
            ['class' => 'd-inline'],
            ['_method' => 'PUT']
        );
        
        // Botão submit estilizado como dropdown-item
        $buttonClasses = trim("dropdown-item {$classAction} {$btnClass}");
        
        $html .= '<button type="submit" class="' . esc($buttonClasses) . '">';
        $html .= '<i class="fas ' . esc($iconAction) . ' fa-sm fa-fw mr-2"></i> ';
        $html .= esc($textAction);
        $html .= '</button>';
        
        $html .= form_close();
        
        return $html;
    }

    /**
     * Renderiza botão de exclusão como item de dropdown
     * 
     * Similar ao action(), mas para DELETE com confirmação.
     * 
     * @param array $params Array com:
     *   - route (string): URL da rota de exclusão
     *   - name (string): Nome do item (para mensagem de confirmação)
     *   - btnClass (string, opcional): Classes extras
     * 
     * @return string HTML do formulário com botão
     */
    public function delete(array $params): string
    {
        helper('form');
        
        $route    = $params['route'] ?? '#';
        $name     = $params['name'] ?? 'este item';
        $btnClass = $params['btnClass'] ?? '';
        
        $html = form_open(
            $route,
            [
                'class'   => 'd-inline',
                'onsubmit' => "return confirm('Tem certeza que deseja excluir \"" . esc($name, 'js') . "\"?');"
            ],
            ['_method' => 'DELETE']
        );
        
        $buttonClasses = trim("dropdown-item text-danger {$btnClass}");
        
        $html .= '<button type="submit" class="' . esc($buttonClasses) . '">';
        $html .= '<i class="fas fa-trash fa-sm fa-fw mr-2"></i> Excluir';
        $html .= '</button>';
        
        $html .= form_close();
        
        return $html;
    }

    /**
     * Renderiza botão genérico como item de dropdown
     * 
     * Para ações customizadas que precisam de formulário.
     * 
     * @param array $params Array com:
     *   - route (string): URL da rota
     *   - method (string): Método HTTP (PUT, POST, DELETE, PATCH)
     *   - text (string): Texto do botão
     *   - icon (string, opcional): Classe do ícone
     *   - class (string, opcional): Classes CSS
     *   - confirm (string|null, opcional): Mensagem de confirmação
     * 
     * @return string HTML do formulário com botão
     */
    public function generic(array $params): string
    {
        helper('form');
        
        $route   = $params['route'] ?? '#';
        $method  = strtoupper($params['method'] ?? 'POST');
        $text    = $params['text'] ?? 'Ação';
        $icon    = $params['icon'] ?? '';
        $class   = $params['class'] ?? 'dropdown-item';
        $confirm = $params['confirm'] ?? null;
        
        $formAttrs = ['class' => 'd-inline'];
        if ($confirm) {
            $formAttrs['onsubmit'] = "return confirm('" . esc($confirm, 'js') . "');";
        }
        
        $html = form_open($route, $formAttrs, ['_method' => $method]);
        
        $html .= '<button type="submit" class="' . esc($class) . '">';
        if ($icon) {
            $html .= '<i class="fas ' . esc($icon) . ' fa-sm fa-fw mr-2"></i> ';
        }
        $html .= esc($text);
        $html .= '</button>';
        
        $html .= form_close();
        
        return $html;
    }
}
