<?php
/**
 * Partial: Back/Layout/_messages.php
 * 
 * =========================================================================
 * EXIBIÇÃO CENTRALIZADA DE FLASH MESSAGES
 * =========================================================================
 * 
 * Este partial exibe mensagens de feedback para o usuário usando:
 * - Flash data da sessão (success, danger, warning, info)
 * - Erros de validação (errorsValidation)
 * 
 * INCLUIR NO LAYOUT PRINCIPAL:
 *   <?= $this->include('Back/Layout/_messages') ?>
 * 
 * COMO DEFINIR MENSAGENS NO CONTROLLER:
 * 
 *   // Sucesso
 *   return redirect()->to(...)->with('success', 'Operação realizada!');
 * 
 *   // Erro genérico
 *   return redirect()->back()->with('danger', 'Algo deu errado.');
 * 
 *   // Erro de validação com erros por campo
 *   return redirect()->back()
 *       ->withInput()
 *       ->with('errorsValidation', $this->model->errors())
 *       ->with('danger', 'Verifique os erros e tente novamente.');
 * 
 *   // Informação
 *   return redirect()->back()->with('info', 'Não há dados para atualizar.');
 * 
 *   // Aviso
 *   return redirect()->back()->with('warning', 'Atenção: campo opcional vazio.');
 * 
 * @package    App\Views\Back\Layout
 * @author     Sistema de Agendamentos
 */
?>

<?php
/**
 * MAPEAMENTO DE TIPOS PARA CLASSES BOOTSTRAP
 * ==========================================
 * 
 * Tipo Flash    → Classe Alert    → Ícone
 * success       → alert-success   → fa-check-circle
 * danger        → alert-danger    → fa-exclamation-triangle
 * warning       → alert-warning   → fa-exclamation-circle
 * info          → alert-info      → fa-info-circle
 * 
 * Também suporta 'errors' legado (mantido por compatibilidade).
 */
$messageTypes = [
    'success' => ['class' => 'alert-success', 'icon' => 'fa-check-circle',        'title' => 'Sucesso!'],
    'danger'  => ['class' => 'alert-danger',  'icon' => 'fa-exclamation-triangle', 'title' => 'Erro!'],
    'warning' => ['class' => 'alert-warning', 'icon' => 'fa-exclamation-circle',  'title' => 'Atenção!'],
    'info'    => ['class' => 'alert-info',    'icon' => 'fa-info-circle',         'title' => 'Informação'],
];
?>

<?php foreach ($messageTypes as $type => $config): ?>
    <?php if (session()->has($type)): ?>
        <div class="alert <?= $config['class'] ?> alert-dismissible fade show" role="alert">
            <i class="fas <?= $config['icon'] ?> mr-2"></i>
            <strong><?= $config['title'] ?></strong> 
            <?= esc(session($type)) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php
/**
 * ERROS DE VALIDAÇÃO DETALHADOS
 * =============================
 * 
 * Se existir 'errorsValidation' na sessão (array de erros por campo),
 * exibe uma lista completa para referência do usuário.
 * 
 * Os erros individuais por campo são exibidos via showErrorInput()
 * ao lado de cada input na view.
 * 
 * Este bloco exibe todos os erros em uma lista única no topo da página.
 */
?>
<?php if (session()->has('errorsValidation') && is_array(session('errorsValidation'))): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading mb-2">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Erros de Validação</strong>
        </h6>
        <ul class="mb-0 pl-3">
            <?php foreach (session('errorsValidation') as $field => $message): ?>
                <li class="small"><?= esc($message) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php
/**
 * COMPATIBILIDADE COM 'errors' LEGADO
 * ===================================
 * 
 * Mantido para retrocompatibilidade com código existente que usa:
 *   ->with('errors', $model->errors())
 * 
 * Recomendação: Usar 'errorsValidation' para novos códigos.
 */
?>
<?php if (session()->has('errors') && is_array(session('errors'))): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading mb-2">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Erros encontrados</strong>
        </h6>
        <ul class="mb-0 pl-3">
            <?php foreach (session('errors') as $error): ?>
                <li class="small"><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
