<?= $this->extend('Front/layout/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero is-primary is-medium">
    <div class="hero-body">
        <div class="container has-text-centered">
            <h1 class="title is-1">
                <i class="fas fa-calendar-check"></i>
                Agende seu Horário
            </h1>
            <h2 class="subtitle is-4">
                Encontre o melhor horário para você de forma rápida e prática
            </h2>
            <a href="<?= base_url('agendar') ?>" class="button is-large is-white is-outlined mt-4">
                <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                <span>Agendar Agora</span>
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section">
    <div class="container">
        <h2 class="title is-3 has-text-centered mb-6">Como Funciona</h2>
        
        <div class="columns is-variable is-8">
            <div class="column is-4">
                <div class="has-text-centered">
                    <div class="icon-wrapper mx-auto mb-4" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #3273dc, #667eea); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-building fa-3x has-text-white"></i>
                    </div>
                    <h3 class="title is-5">1. Escolha a Unidade</h3>
                    <p class="has-text-grey">Selecione a unidade mais próxima de você entre nossas opções disponíveis.</p>
                </div>
            </div>
            
            <div class="column is-4">
                <div class="has-text-centered">
                    <div class="icon-wrapper mx-auto mb-4" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #48c774, #23d160); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-concierge-bell fa-3x has-text-white"></i>
                    </div>
                    <h3 class="title is-5">2. Selecione o Serviço</h3>
                    <p class="has-text-grey">Escolha entre os diversos serviços disponíveis na unidade selecionada.</p>
                </div>
            </div>
            
            <div class="column is-4">
                <div class="has-text-centered">
                    <div class="icon-wrapper mx-auto mb-4" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #ffdd57, #ffc107); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-day fa-3x has-text-white"></i>
                    </div>
                    <h3 class="title is-5">3. Escolha Data e Hora</h3>
                    <p class="has-text-grey">Selecione o melhor dia e horário disponível para seu atendimento.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Units Section -->
<?php if (!empty($units)): ?>
<section class="section has-background-light">
    <div class="container">
        <h2 class="title is-3 has-text-centered mb-6">Nossas Unidades</h2>
        
        <div class="columns is-multiline">
            <?php foreach ($units as $unit): ?>
            <div class="column is-4">
                <div class="card">
                    <div class="card-content">
                        <div class="media">
                            <div class="media-left">
                                <span class="icon is-large has-text-primary">
                                    <i class="fas fa-building fa-2x"></i>
                                </span>
                            </div>
                            <div class="media-content">
                                <p class="title is-5"><?= esc($unit->name) ?></p>
                                <?php if ($unit->city): ?>
                                <p class="subtitle is-6 has-text-grey">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?= esc($unit->city) ?><?= $unit->state ? '/' . esc($unit->state) : '' ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($unit->phone): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone has-text-grey mr-1"></i>
                            <?= esc($unit->phone) ?>
                        </p>
                        <?php endif; ?>
                        <?php if ($unit->email): ?>
                        <p class="mb-3">
                            <i class="fas fa-envelope has-text-grey mr-1"></i>
                            <?= esc($unit->email) ?>
                        </p>
                        <?php endif; ?>
                        <a href="<?= base_url('agendar?unit=' . $unit->id) ?>" class="button is-primary is-fullwidth">
                            <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                            <span>Agendar nesta Unidade</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="box has-background-primary has-text-white p-6">
            <div class="columns is-vcentered">
                <div class="column is-8">
                    <h2 class="title is-3 has-text-white">Pronto para agendar?</h2>
                    <p class="subtitle is-5 has-text-white-ter">
                        É rápido, fácil e você pode fazer de qualquer lugar!
                    </p>
                </div>
                <div class="column is-4 has-text-right">
                    <a href="<?= base_url('agendar') ?>" class="button is-large is-white">
                        <span class="icon"><i class="fas fa-arrow-right"></i></span>
                        <span>Começar</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
