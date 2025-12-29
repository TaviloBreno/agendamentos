<?php
/**
 * View: Back/Services/show.php
 * 
 * Exibição de detalhes do serviço
 */

helper('form');
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= esc($pageHeading ?? 'Detalhes do Serviço') ?>
    </h1>
    <div>
        <a href="<?= route_to('super.services.edit', $service->id) ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50 mr-1"></i> Editar
        </a>
        <a href="<?= route_to('super.services') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Coluna Principal -->
    <div class="col-lg-8">
        <!-- Card Informações -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-concierge-bell mr-2"></i>Informações do Serviço
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">ID:</th>
                        <td><?= $service->id ?></td>
                    </tr>
                    <tr>
                        <th>Nome:</th>
                        <td><strong><?= esc($service->name) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Duração:</th>
                        <td>
                            <span class="badge badge-info"><?= $service->durationFormatted() ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Preço:</th>
                        <td>
                            <span class="badge badge-success"><?= $service->priceFormatted() ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><?= $service->statusBadge() ?></td>
                    </tr>
                    <tr>
                        <th>Descrição:</th>
                        <td>
                            <?php if ($service->hasDescription()): ?>
                                <?= nl2br(esc($service->description)) ?>
                            <?php else: ?>
                                <span class="text-muted">Nenhuma descrição cadastrada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Coluna Lateral -->
    <div class="col-lg-4">
        <!-- Card Timestamps -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock mr-2"></i>Datas
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Criado em:</strong><br>
                    <small class="text-muted">
                        <?= $service->created_at ? $service->created_at->format('d/m/Y H:i') : '-' ?>
                    </small>
                </p>
                <p class="mb-0">
                    <strong>Atualizado em:</strong><br>
                    <small class="text-muted">
                        <?= $service->updated_at ? $service->updated_at->format('d/m/Y H:i') : '-' ?>
                    </small>
                </p>
            </div>
        </div>

        <!-- Card Ações -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cogs mr-2"></i>Ações
                </h6>
            </div>
            <div class="card-body">
                <!-- Toggle Status -->
                <?= form_open(route_to('super.services.action', $service->id), ['class' => 'd-inline']) ?>
                    <?= form_hidden('_method', 'PUT') ?>
                    <button type="submit" class="btn btn-<?= $service->isActive() ? 'warning' : 'success' ?> btn-block mb-2">
                        <i class="fas <?= $service->iconToAction() ?> mr-1"></i>
                        <?= $service->textToAction() ?>
                    </button>
                <?= form_close() ?>

                <!-- Excluir -->
                <?= form_open(route_to('super.services.delete', $service->id), ['class' => 'd-inline', 'onsubmit' => "return confirm('Deseja realmente excluir este serviço?')"]) ?>
                    <?= form_hidden('_method', 'DELETE') ?>
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-trash mr-1"></i> Excluir
                    </button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
