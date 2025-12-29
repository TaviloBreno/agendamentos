<?= $this->extend('Back/layout/main') ?>

<?= $this->section('content') ?>

<?php 
$currentUser = session()->get('user');
$isSelf = $currentUser && $currentUser['id'] == $user->id;
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user text-primary mr-2"></i>Detalhes do Usuário
        <?php if ($isSelf): ?>
            <span class="badge badge-primary ml-2">Seu perfil</span>
        <?php endif; ?>
    </h1>
    <div>
        <a href="<?= route_to('super.users.edit', $user->id) ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-edit fa-sm mr-1"></i> Editar
        </a>
        <a href="<?= route_to('super.users') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Voltar
        </a>
    </div>
</div>

<!-- Alertas -->
<?= view('Back/layout/partials/alerts') ?>

<div class="row">
    <!-- Coluna Principal -->
    <div class="col-lg-8">
        <!-- Dados do Usuário -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-1"></i>Informações do Usuário
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="35%">ID:</th>
                                <td>#<?= esc($user->id) ?></td>
                            </tr>
                            <tr>
                                <th>Nome:</th>
                                <td><?= esc($user->name) ?></td>
                            </tr>
                            <tr>
                                <th>E-mail:</th>
                                <td>
                                    <a href="mailto:<?= esc($user->email) ?>">
                                        <i class="fas fa-envelope mr-1"></i><?= esc($user->email) ?>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="35%">Papel:</th>
                                <td><?= $user->roleBadge() ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><?= $user->statusBadge() ?></td>
                            </tr>
                            <tr>
                                <th>Cadastro:</th>
                                <td>
                                    <i class="fas fa-clock mr-1"></i>
                                    <?= $user->created_at->format('d/m/Y H:i') ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descrição do Papel -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-shield-alt mr-1"></i>Permissões
                </h6>
            </div>
            <div class="card-body">
                <?php if ($user->isSuper()): ?>
                    <div class="alert alert-danger mb-0">
                        <h6 class="font-weight-bold"><i class="fas fa-user-shield mr-1"></i>Super Administrador</h6>
                        <p class="mb-0">Este usuário possui acesso total ao sistema, incluindo:</p>
                        <ul class="mb-0 mt-2">
                            <li>Gerenciamento de todos os usuários</li>
                            <li>Configurações do sistema</li>
                            <li>Acesso a todos os módulos</li>
                            <li>Relatórios avançados</li>
                        </ul>
                    </div>
                <?php elseif ($user->isAdmin()): ?>
                    <div class="alert alert-warning mb-0">
                        <h6 class="font-weight-bold"><i class="fas fa-user-tie mr-1"></i>Administrador</h6>
                        <p class="mb-0">Este usuário possui acesso administrativo, incluindo:</p>
                        <ul class="mb-0 mt-2">
                            <li>Gerenciamento de unidades e serviços</li>
                            <li>Gerenciamento de profissionais</li>
                            <li>Gerenciamento de agendamentos</li>
                            <li>Visualização de relatórios básicos</li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <h6 class="font-weight-bold"><i class="fas fa-user mr-1"></i>Usuário</h6>
                        <p class="mb-0">Este usuário possui acesso básico ao sistema:</p>
                        <ul class="mb-0 mt-2">
                            <li>Visualização de informações</li>
                            <li>Gerenciamento do próprio perfil</li>
                            <li>Acesso limitado aos módulos</li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Atividade -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-1"></i>Atividade
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="35%">Criado em:</th>
                        <td><?= $user->created_at->format('d/m/Y H:i:s') ?></td>
                    </tr>
                    <tr>
                        <th>Última atualização:</th>
                        <td>
                            <?php if ($user->updated_at): ?>
                                <?= $user->updated_at->format('d/m/Y H:i:s') ?>
                            <?php else: ?>
                                <span class="text-muted">Nunca atualizado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Coluna Lateral -->
    <div class="col-lg-4">
        <!-- Card Avatar -->
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img src="<?= $user->avatarUrl(150) ?>" alt="<?= esc($user->name) ?>" 
                     class="rounded-circle mb-3" width="150" height="150">
                <h5 class="font-weight-bold"><?= esc($user->name) ?></h5>
                <p class="text-muted mb-2"><?= esc($user->email) ?></p>
                <?= $user->roleBadge() ?>
                <?= $user->statusBadge() ?>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt mr-1"></i>Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= route_to('super.users.edit', $user->id) ?>" 
                       class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit mr-1"></i> Editar Usuário
                    </a>
                    <?php if (!$isSelf): ?>
                    <form action="<?= route_to('super.users.action', $user->id) ?>" method="POST" class="mb-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        <button type="submit" class="btn btn-<?= $user->isActive() ? 'warning' : 'success' ?> btn-block">
                            <i class="fas fa-<?= $user->isActive() ? 'ban' : 'check' ?> mr-1"></i>
                            <?= $user->isActive() ? 'Desativar' : 'Ativar' ?> Usuário
                        </button>
                    </form>
                    <form action="<?= route_to('super.users.delete', $user->id) ?>" method="POST"
                          onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash mr-1"></i> Excluir Usuário
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <small>
                            <i class="fas fa-info-circle mr-1"></i>
                            Você não pode desativar ou excluir seu próprio usuário.
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Contato -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-envelope mr-1"></i>Contato
                </h6>
            </div>
            <div class="card-body">
                <a href="mailto:<?= esc($user->email) ?>" class="btn btn-outline-primary btn-block">
                    <i class="fas fa-envelope mr-1"></i> Enviar E-mail
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
