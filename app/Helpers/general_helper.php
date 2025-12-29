<?php

/**
 * General Helper - Funções utilitárias globais
 * 
 * =========================================================================
 * COMO FUNCIONA
 * =========================================================================
 * 
 * Este helper é carregado automaticamente no BaseController, tornando
 * suas funções disponíveis em todos os controllers e views da aplicação.
 * 
 * CARREGAMENTO NO BASECONTROLLER:
 *   protected $helpers = ['form', 'html', 'general'];
 * 
 * USO NAS VIEWS:
 *   <?= showErrorInput('name') ?>
 *   <?= showErrorInput('email') ?>
 * 
 * @package    App\Helpers
 * @author     Sistema de Agendamentos
 */

// =========================================================================
// FUNÇÕES DE VALIDAÇÃO E ERROS
// =========================================================================

if (! function_exists('showErrorInput')) {
    /**
     * Exibe mensagem de erro de validação para um campo específico
     * 
     * =========================================================================
     * FLUXO
     * =========================================================================
     * 
     * 1. Busca em session('errorsValidation') o array de erros
     * 2. Verifica se existe erro para o campo informado
     * 3. Retorna <span class="text-danger"> com a mensagem ou string vazia
     * 
     * =========================================================================
     * COMO O CONTROLLER DEVE DEFINIR OS ERROS
     * =========================================================================
     * 
     *   return redirect()->back()
     *       ->withInput()
     *       ->with('errorsValidation', $this->model->errors())
     *       ->with('danger', 'Verifique os erros e tente novamente.');
     * 
     * =========================================================================
     * USO NA VIEW
     * =========================================================================
     * 
     *   <input type="text" name="name" class="form-control" ...>
     *   <?= showErrorInput('name') ?>
     * 
     * Se houver erro para 'name':
     *   <span class="text-danger small">
     *       <i class="fas fa-exclamation-circle mr-1"></i>
     *       O nome da unidade é obrigatório.
     *   </span>
     * 
     * Se não houver erro: retorna string vazia (nada é exibido).
     * 
     * @param string $field Nome do campo (deve corresponder ao name do input)
     * @return string HTML do span com erro ou string vazia
     */
    function showErrorInput(string $field): string
    {
        // Busca os erros de validação da sessão (flash data)
        $errors = session('errorsValidation');
        
        // Se não houver erros ou não houver erro para este campo
        if (empty($errors) || ! isset($errors[$field])) {
            return '';
        }
        
        // Retorna o span formatado com a mensagem de erro
        return '<span class="text-danger small">'
             . '<i class="fas fa-exclamation-circle mr-1"></i>'
             . esc($errors[$field])
             . '</span>';
    }
}

if (! function_exists('hasErrorInput')) {
    /**
     * Verifica se existe erro de validação para um campo
     * 
     * Útil para adicionar classes CSS condicionais (ex: is-invalid).
     * 
     * USO NA VIEW:
     *   <input type="text" 
     *          name="name" 
     *          class="form-control <?= hasErrorInput('name') ? 'is-invalid' : '' ?>">
     * 
     * @param string $field Nome do campo
     * @return bool True se houver erro, false caso contrário
     */
    function hasErrorInput(string $field): bool
    {
        $errors = session('errorsValidation');
        
        return ! empty($errors) && isset($errors[$field]);
    }
}

if (! function_exists('inputClass')) {
    /**
     * Retorna a classe CSS do input baseado no estado de validação
     * 
     * Simplifica a adição de classes is-valid/is-invalid do Bootstrap.
     * 
     * USO NA VIEW:
     *   <input type="text" name="name" class="form-control <?= inputClass('name') ?>">
     * 
     * @param string $field Nome do campo
     * @param string $baseClass Classe base (padrão: vazio)
     * @return string Classe adicional (is-invalid se erro, vazio se ok)
     */
    function inputClass(string $field, string $baseClass = ''): string
    {
        $errors = session('errorsValidation');
        
        if (! empty($errors) && isset($errors[$field])) {
            return $baseClass . ' is-invalid';
        }
        
        // Se houve submit mas não há erro neste campo, poderia retornar is-valid
        // Mas por simplicidade, retornamos apenas a classe base
        return $baseClass;
    }
}

// =========================================================================
// FUNÇÕES DE FORMATAÇÃO
// =========================================================================

if (! function_exists('formatPhone')) {
    /**
     * Formata um número de telefone para exibição
     * 
     * @param string|null $phone Telefone (apenas números ou formatado)
     * @return string Telefone formatado ou string vazia
     */
    function formatPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        
        // Remove tudo que não é número
        $numbers = preg_replace('/[^0-9]/', '', $phone);
        
        // Formato: (XX) XXXXX-XXXX (celular) ou (XX) XXXX-XXXX (fixo)
        if (strlen($numbers) === 11) {
            return sprintf(
                '(%s) %s-%s',
                substr($numbers, 0, 2),
                substr($numbers, 2, 5),
                substr($numbers, 7)
            );
        }
        
        if (strlen($numbers) === 10) {
            return sprintf(
                '(%s) %s-%s',
                substr($numbers, 0, 2),
                substr($numbers, 2, 4),
                substr($numbers, 6)
            );
        }
        
        // Se não encaixa em nenhum formato, retorna como veio
        return $phone;
    }
}

if (! function_exists('formatDate')) {
    /**
     * Formata uma data para exibição
     * 
     * @param mixed $date Data (string, DateTime, Time)
     * @param string $format Formato de saída (padrão: d/m/Y)
     * @return string Data formatada ou '-'
     */
    function formatDate($date, string $format = 'd/m/Y'): string
    {
        if (empty($date)) {
            return '-';
        }
        
        if ($date instanceof \CodeIgniter\I18n\Time || $date instanceof \DateTime) {
            return $date->format($format);
        }
        
        try {
            return (new \DateTime($date))->format($format);
        } catch (\Exception $e) {
            return '-';
        }
    }
}

if (! function_exists('formatDateTime')) {
    /**
     * Formata data e hora para exibição
     * 
     * @param mixed $date Data (string, DateTime, Time)
     * @return string Data e hora formatadas ou '-'
     */
    function formatDateTime($date): string
    {
        return formatDate($date, 'd/m/Y H:i');
    }
}

// =========================================================================
// FUNÇÕES DE TEXTO
// =========================================================================

if (! function_exists('truncate')) {
    /**
     * Trunca um texto com reticências
     * 
     * @param string|null $text Texto a truncar
     * @param int $length Tamanho máximo
     * @param string $suffix Sufixo (padrão: ...)
     * @return string Texto truncado
     */
    function truncate(?string $text, int $length = 100, string $suffix = '...'): string
    {
        if (empty($text)) {
            return '';
        }
        
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        
        return mb_substr($text, 0, $length) . $suffix;
    }
}

if (! function_exists('yesNo')) {
    /**
     * Converte valor booleano para Sim/Não
     * 
     * @param mixed $value Valor a converter
     * @return string 'Sim' ou 'Não'
     */
    function yesNo($value): string
    {
        return ($value && $value !== '0') ? 'Sim' : 'Não';
    }
}

// =========================================================================
// FUNÇÕES DE AUTORIZAÇÃO POR ROLE
// =========================================================================

if (! function_exists('userRole')) {
    /**
     * Retorna o role do usuário logado
     * 
     * @return string Role do usuário ('super', 'admin', 'user') ou 'user' se não logado
     */
    function userRole(): string
    {
        return session('userRole') ?? 'user';
    }
}

if (! function_exists('isSuper')) {
    /**
     * Verifica se o usuário logado é Super Admin
     * 
     * @return bool
     */
    function isSuper(): bool
    {
        return userRole() === 'super';
    }
}

if (! function_exists('isAdmin')) {
    /**
     * Verifica se o usuário logado é Admin ou superior (Super/Admin)
     * 
     * @return bool
     */
    function isAdmin(): bool
    {
        return in_array(userRole(), ['super', 'admin'], true);
    }
}

if (! function_exists('isUser')) {
    /**
     * Verifica se o usuário logado é apenas User (não é admin nem super)
     * 
     * @return bool
     */
    function isUser(): bool
    {
        return userRole() === 'user';
    }
}

if (! function_exists('canAccess')) {
    /**
     * Verifica se o usuário logado pode acessar uma funcionalidade
     * 
     * Usa a matriz de permissões definida em RoleFilter.
     * 
     * USO NA VIEW:
     *   <?php if (canAccess('users')): ?>
     *       <li><a href="...">Usuários</a></li>
     *   <?php endif; ?>
     * 
     * @param string $feature Funcionalidade a verificar
     * @return bool
     */
    function canAccess(string $feature): bool
    {
        return \App\Filters\RoleFilter::canAccess(userRole(), $feature);
    }
}

if (! function_exists('hasRole')) {
    /**
     * Verifica se o usuário logado possui um dos roles especificados
     * 
     * USO NA VIEW:
     *   <?php if (hasRole('super', 'admin')): ?>
     *       <!-- Mostra conteúdo para super e admin -->
     *   <?php endif; ?>
     * 
     * @param string ...$roles Roles a verificar
     * @return bool
     */
    function hasRole(string ...$roles): bool
    {
        return in_array(userRole(), $roles, true);
    }
}
