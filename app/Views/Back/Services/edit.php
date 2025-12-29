<?php
/**
 * View: Back/Services/edit.php
 * 
 * Formulário de edição de serviço
 */

helper('form');
?>

<?= $this->extend('Back/Layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= esc($pageHeading ?? 'Editar Serviço') ?>
    </h1>
    <a href="<?= route_to('super.services') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Voltar
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit mr-2"></i>Editar Serviço
        </h6>
    </div>
    <div class="card-body">
        
        <?= form_open(route_to('super.services.update', $service->id), ['class' => 'needs-validation']) ?>
            <?= form_hidden('_method', 'PUT') ?>

            <div class="row">
                <!-- Nome -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome do Serviço <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control <?= showErrorInput('name') ?>" 
                            id="name" 
                            name="name" 
                            value="<?= old('name', $service->name) ?>"
                            placeholder="Ex: Corte de Cabelo"
                            required
                        >
                        <?= hasErrorInput('name') ?>
                    </div>
                </div>

                <!-- Duração -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="duration">Duração <span class="text-danger">*</span></label>
                        <select 
                            class="form-control <?= showErrorInput('duration') ?>" 
                            id="duration" 
                            name="duration"
                            required
                        >
                            <option value="">Selecione...</option>
                            <?php foreach ($durationOptions as $value => $label): ?>
                                <option value="<?= $value ?>" <?= old('duration', $service->duration) == $value ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= hasErrorInput('duration') ?>
                    </div>
                </div>

                <!-- Preço -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="price">Preço (R$) <span class="text-danger">*</span></label>
                        <input 
                            type="number" 
                            class="form-control <?= showErrorInput('price') ?>" 
                            id="price" 
                            name="price" 
                            value="<?= old('price', number_format($service->price, 2, '.', '')) ?>"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required
                        >
                        <?= hasErrorInput('price') ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Descrição -->
                <div class="col-md-9">
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea 
                            class="form-control <?= showErrorInput('description') ?>" 
                            id="description" 
                            name="description" 
                            rows="3"
                            placeholder="Descreva o serviço (opcional)"
                        ><?= old('description', $service->description) ?></textarea>
                        <?= hasErrorInput('description') ?>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="active">Status <span class="text-danger">*</span></label>
                        <select 
                            class="form-control <?= showErrorInput('active') ?>" 
                            id="active" 
                            name="active"
                            required
                        >
                            <option value="1" <?= old('active', $service->active) == '1' ? 'selected' : '' ?>>Ativo</option>
                            <option value="0" <?= old('active', $service->active) == '0' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                        <?= hasErrorInput('active') ?>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salvar Alterações
                    </button>
                    <a href="<?= route_to('super.services') ?>" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <a href="<?= route_to('super.services.show', $service->id) ?>" class="btn btn-info">
                        <i class="fas fa-eye mr-1"></i> Visualizar
                    </a>
                </div>
            </div>

        <?= form_close() ?>

    </div>
</div>

<?= $this->endSection() ?>
