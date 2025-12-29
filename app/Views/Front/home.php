<?= $this->extend('Front/layout/main') ?>

<?= $this->section('css') ?>
<style>
/* Hero Slider */
.hero-slider {
    position: relative;
    height: 70vh;
    min-height: 500px;
    overflow: hidden;
}

.hero-slider .slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.hero-slider .slide.active {
    opacity: 1;
}

.hero-slider .slide::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(50, 115, 220, 0.85), rgba(102, 126, 234, 0.75));
}

.hero-slider .hero-content {
    position: relative;
    z-index: 10;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem;
}

.hero-slider .hero-content h1 {
    font-size: 3.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    animation: fadeInUp 1s ease;
}

.hero-slider .hero-content p {
    font-size: 1.5rem;
    margin: 1.5rem 0;
    animation: fadeInUp 1s ease 0.2s both;
}

.hero-slider .hero-content .buttons {
    animation: fadeInUp 1s ease 0.4s both;
}

/* Slider Navigation */
.slider-nav {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 20;
    display: flex;
    gap: 10px;
}

.slider-nav .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: all 0.3s ease;
}

.slider-nav .dot.active {
    background: #fff;
    transform: scale(1.2);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Service Cards with Images */
.service-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.service-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.service-card:hover img {
    transform: scale(1.1);
}

.service-card .card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.8));
    padding: 2rem 1.5rem 1.5rem;
    color: white;
}

/* Unit Cards Improved */
.unit-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    background: white;
}

.unit-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.unit-card .unit-image {
    height: 180px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.unit-card .unit-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(transparent 50%, rgba(0,0,0,0.5));
}

/* Stats Section */
.stats-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 4rem 0;
}

.stat-item {
    text-align: center;
    color: white;
}

.stat-item .stat-number {
    font-size: 3rem;
    font-weight: 700;
    display: block;
}

.stat-item .stat-label {
    font-size: 1.1rem;
    opacity: 0.9;
}

/* Testimonials */
.testimonial-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    position: relative;
}

.testimonial-card::before {
    content: '"';
    font-size: 4rem;
    color: #3273dc;
    opacity: 0.2;
    position: absolute;
    top: 10px;
    left: 20px;
    font-family: Georgia, serif;
}

.testimonial-card .avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-slider {
        height: 60vh;
        min-height: 400px;
    }
    
    .hero-slider .hero-content h1 {
        font-size: 2rem;
    }
    
    .hero-slider .hero-content p {
        font-size: 1.1rem;
    }
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Slider Section -->
<section class="hero-slider">
    <!-- Slides com imagens do Unsplash -->
    <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=1920&q=80')"></div>
    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=1920&q=80')"></div>
    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=1920&q=80')"></div>
    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?w=1920&q=80')"></div>
    
    <!-- Hero Content -->
    <div class="hero-content">
        <div class="container">
            <h1 class="title is-1 has-text-white">
                Agende seu Horário Online
            </h1>
            <p class="has-text-white">
                Praticidade e conforto para marcar seus atendimentos quando e onde quiser
            </p>
            <div class="buttons is-centered mt-5">
                <a href="<?= base_url('agendar') ?>" class="button is-large is-white">
                    <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                    <span>Agendar Agora</span>
                </a>
                <a href="#unidades" class="button is-large is-white is-outlined">
                    <span class="icon"><i class="fas fa-building"></i></span>
                    <span>Ver Unidades</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Slider Navigation -->
    <div class="slider-nav">
        <span class="dot active" data-slide="0"></span>
        <span class="dot" data-slide="1"></span>
        <span class="dot" data-slide="2"></span>
        <span class="dot" data-slide="3"></span>
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

<!-- Services Gallery Section -->
<section class="section has-background-white-ter">
    <div class="container">
        <h2 class="title is-3 has-text-centered mb-2">Nossos Serviços</h2>
        <p class="subtitle has-text-centered has-text-grey mb-6">Qualidade e profissionalismo em cada atendimento</p>
        
        <div class="columns is-multiline">
            <div class="column is-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=600&q=80" alt="Beleza e Estética">
                    <div class="card-overlay">
                        <h3 class="title is-5 has-text-white mb-1">Beleza & Estética</h3>
                        <p class="is-size-7">Tratamentos faciais e corporais</p>
                    </div>
                </div>
            </div>
            <div class="column is-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=600&q=80" alt="Saúde">
                    <div class="card-overlay">
                        <h3 class="title is-5 has-text-white mb-1">Saúde & Bem-estar</h3>
                        <p class="is-size-7">Consultas e exames</p>
                    </div>
                </div>
            </div>
            <div class="column is-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80" alt="Fitness">
                    <div class="card-overlay">
                        <h3 class="title is-5 has-text-white mb-1">Personal Training</h3>
                        <p class="is-size-7">Treinos personalizados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="columns">
            <div class="column is-3">
                <div class="stat-item">
                    <span class="stat-number" data-count="5000">0</span>
                    <span class="stat-label">Agendamentos Realizados</span>
                </div>
            </div>
            <div class="column is-3">
                <div class="stat-item">
                    <span class="stat-number" data-count="1200">0</span>
                    <span class="stat-label">Clientes Satisfeitos</span>
                </div>
            </div>
            <div class="column is-3">
                <div class="stat-item">
                    <span class="stat-number" data-count="50">0</span>
                    <span class="stat-label">Profissionais</span>
                </div>
            </div>
            <div class="column is-3">
                <div class="stat-item">
                    <span class="stat-number" data-count="98">0</span>
                    <span class="stat-label">% de Satisfação</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Units Section -->
<?php if (!empty($units)): ?>
<section class="section" id="unidades">
    <div class="container">
        <h2 class="title is-3 has-text-centered mb-2">Nossas Unidades</h2>
        <p class="subtitle has-text-centered has-text-grey mb-6">Encontre a unidade mais próxima de você</p>
        
        <div class="columns is-multiline">
            <?php 
            // Imagens variadas do Unsplash para cada unidade
            $unitImages = [
                'https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=600&q=80',
                'https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?w=600&q=80',
                'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=600&q=80',
                'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=80',
                'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=600&q=80',
            ];
            foreach ($units as $i => $unit): 
            $imageUrl = $unitImages[$i % count($unitImages)];
            ?>
            <div class="column is-4">
                <div class="unit-card">
                    <div class="unit-image" style="background-image: url('<?= $imageUrl ?>')"></div>
                    <div class="card-content">
                        <h3 class="title is-5 mb-2"><?= esc($unit->name) ?></h3>
                        <?php if ($unit->city): ?>
                        <p class="has-text-grey mb-3">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?= esc($unit->city) ?><?= $unit->state ? '/' . esc($unit->state) : '' ?>
                        </p>
                        <?php endif; ?>
                        <?php if ($unit->phone): ?>
                        <p class="mb-2 is-size-7">
                            <i class="fas fa-phone has-text-primary mr-1"></i>
                            <?= esc($unit->phone) ?>
                        </p>
                        <?php endif; ?>
                        <a href="<?= base_url('agendar?unit=' . $unit->id) ?>" class="button is-primary is-fullwidth mt-3">
                            <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                            <span>Agendar</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials Section -->
<section class="section has-background-light">
    <div class="container">
        <h2 class="title is-3 has-text-centered mb-2">O que nossos clientes dizem</h2>
        <p class="subtitle has-text-centered has-text-grey mb-6">Depoimentos de quem já utilizou nosso sistema</p>
        
        <div class="columns">
            <div class="column is-4">
                <div class="testimonial-card">
                    <p class="mb-4 has-text-grey-dark">
                        "Sistema muito prático! Consegui agendar minha consulta em menos de 2 minutos, sem precisar ligar."
                    </p>
                    <div class="is-flex is-align-items-center">
                        <img class="avatar mr-3" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Maria">
                        <div>
                            <strong>Maria Silva</strong>
                            <p class="is-size-7 has-text-grey">Cliente desde 2023</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-4">
                <div class="testimonial-card">
                    <p class="mb-4 has-text-grey-dark">
                        "Adoro receber os lembretes por WhatsApp! Nunca mais esqueci de um agendamento."
                    </p>
                    <div class="is-flex is-align-items-center">
                        <img class="avatar mr-3" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80" alt="João">
                        <div>
                            <strong>João Santos</strong>
                            <p class="is-size-7 has-text-grey">Cliente desde 2024</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-4">
                <div class="testimonial-card">
                    <p class="mb-4 has-text-grey-dark">
                        "Excelente experiência! A interface é intuitiva e os profissionais são muito competentes."
                    </p>
                    <div class="is-flex is-align-items-center">
                        <img class="avatar mr-3" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80" alt="Ana">
                        <div>
                            <strong>Ana Costa</strong>
                            <p class="is-size-7 has-text-grey">Cliente desde 2023</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="box p-6" style="background: linear-gradient(135deg, #3273dc, #667eea); border-radius: 16px;">
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
                        <span>Começar Agora</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hero Slider
    const slides = document.querySelectorAll('.hero-slider .slide');
    const dots = document.querySelectorAll('.slider-nav .dot');
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        currentSlide = index;
    }

    function nextSlide() {
        let next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }

    function startSlider() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    // Click on dots
    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            clearInterval(slideInterval);
            showSlide(parseInt(this.dataset.slide));
            startSlider();
        });
    });

    startSlider();

    // Stats Counter Animation
    const statNumbers = document.querySelectorAll('.stat-number');
    const statsSection = document.querySelector('.stats-section');
    let animated = false;

    function animateStats() {
        if (animated) return;
        
        statNumbers.forEach(stat => {
            const target = parseInt(stat.dataset.count);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                stat.textContent = Math.floor(current).toLocaleString('pt-BR');
                if (stat.dataset.count === '98') {
                    stat.textContent += '%';
                } else if (current >= 1000) {
                    stat.textContent = (current >= target ? target : Math.floor(current)).toLocaleString('pt-BR') + '+';
                }
            }, 16);
        });
        
        animated = true;
    }

    // Intersection Observer for stats
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateStats();
            }
        });
    }, { threshold: 0.5 });

    if (statsSection) {
        observer.observe(statsSection);
    }
});
</script>
<?= $this->endSection() ?>
