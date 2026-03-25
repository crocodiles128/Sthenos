<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sthenos Gym - Gestão de Treinos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&h=1080');
            background-size: cover;
            background-position: center;
        }
        .workout-img {
            background-size: cover;
            background-position: center;
        }
        .gallery-img {
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">

    <header class="bg-gradient-to-r from-gray-800 to-gray-900 text-white p-4 md:p-6 shadow-lg">
        <div class="container mx-auto flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-bold">Sthenos Gym</h1>
            <div class="flex items-center gap-2">
                <button id="mobile-nav-toggle" class="md:hidden p-2 rounded-md border border-white/20 hover:bg-white/10 transition" aria-label="Abrir menu">
                    <i class="fas fa-bars"></i>
                </button>
                <nav id="main-nav" class="hidden md:flex items-center space-x-6 text-sm">
                    <a href="#about" class="hover:text-yellow-400 transition flex items-center"><i class="fas fa-info-circle mr-1"></i>Sobre</a>
                    <a href="#workouts" class="hover:text-yellow-400 transition flex items-center"><i class="fas fa-dumbbell mr-1"></i>Treinos</a>
                    <a href="#gallery" class="hover:text-yellow-400 transition flex items-center"><i class="fas fa-images mr-1"></i>Galeria</a>
                    <a href="#testimonials" class="hover:text-yellow-400 transition flex items-center"><i class="fas fa-star mr-1"></i>Depoimentos</a>
                    <a href="#contact" class="hover:text-yellow-400 transition flex items-center"><i class="fas fa-envelope mr-1"></i>Contato</a>
                </nav>
            </div>
        </div>
        <div id="mobile-nav" class="md:hidden hidden bg-gray-900/95 border-t border-gray-700">
            <a href="#about" class="block px-4 py-3 text-white hover:bg-gray-800">Sobre</a>
            <a href="#workouts" class="block px-4 py-3 text-white hover:bg-gray-800">Treinos</a>
            <a href="#gallery" class="block px-4 py-3 text-white hover:bg-gray-800">Galeria</a>
            <a href="#testimonials" class="block px-4 py-3 text-white hover:bg-gray-800">Depoimentos</a>
            <a href="#contact" class="block px-4 py-3 text-white hover:bg-gray-800">Contato</a>
        </div>
    </header>

    <section class="hero-bg h-screen flex items-center justify-center text-white text-center relative">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative z-10" data-aos="fade-up">
            <h1 class="text-5xl md:text-7xl font-extrabold mb-4">Bem-vindo à Sthenos Gym</h1>
            <p class="text-xl md:text-2xl mb-8" data-aos="fade-up" data-aos-delay="200">Gerencie seus treinos com eficiência e alcance seus objetivos</p>
            <a href="#workouts" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-8 rounded-full transition transform hover:scale-105" data-aos="zoom-in" data-aos-delay="400">Ver Treinos</a>
        </div>
    </section>

    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-8 text-gray-800" data-aos="fade-right"><i class="fas fa-info-circle text-yellow-500 mr-2"></i>Sobre Nós</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto" data-aos="fade-right" data-aos-delay="100">A Sthenos Gym é dedicada a ajudar você a alcançar seus objetivos de fitness. Oferecemos programas de treinamento personalizados, equipamentos de ponta e uma comunidade motivadora para transformar sua rotina.</p>
        </div>
    </section>

    <section id="workouts" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-800" data-aos="fade-left"><i class="fas fa-dumbbell text-blue-500 mr-2"></i>Planos de Treino</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition duration-300" data-aos="zoom-in">
                    <div class="workout-img h-48" style="background-image: url('https://images.unsplash.com/photo-1584464491033-06628f3a6b7b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200');"></div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-2 text-gray-800">Musculação</h3>
                        <p class="text-gray-600 mb-4">Fortaleça seus músculos com nossos programas intensivos e guiados por profissionais.</p>
                        <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-full transition">Saiba Mais</a>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition duration-300" data-aos="zoom-in" data-aos-delay="200">
                    <div class="workout-img h-48" style="background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200');"></div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-2 text-gray-800">Cardio</h3>
                        <p class="text-gray-600 mb-4">Melhore sua resistência cardiovascular com sessões dinâmicas e variadas.</p>
                        <a href="#" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-full transition">Saiba Mais</a>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition duration-300" data-aos="zoom-in" data-aos-delay="400">
                    <div class="workout-img h-48" style="background-image: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200');"></div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-2 text-gray-800">Yoga</h3>
                        <p class="text-gray-600 mb-4">Encontre equilíbrio, flexibilidade e paz interior com nossas aulas de yoga.</p>
                        <a href="#" class="bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-full transition">Saiba Mais</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="gallery" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-800" data-aos="fade-up"><i class="fas fa-images text-green-500 mr-2"></i>Galeria</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="gallery-img h-64 rounded-lg shadow-lg transform hover:scale-105 transition duration-300" style="background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=300');" data-aos="fade-up"></div>
                <div class="gallery-img h-64 rounded-lg shadow-lg transform hover:scale-105 transition duration-300" style="background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=300');" data-aos="fade-up" data-aos-delay="100"></div>
                <div class="gallery-img h-64 rounded-lg shadow-lg transform hover:scale-105 transition duration-300" style="background-image: url('https://images.unsplash.com/photo-1584464491033-06628f3a6b7b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=300');" data-aos="fade-up" data-aos-delay="200"></div>
                <div class="gallery-img h-64 rounded-lg shadow-lg transform hover:scale-105 transition duration-300" style="background-image: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&h=300');" data-aos="fade-up" data-aos-delay="300"></div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="py-20 bg-gray-100">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-800" data-aos="fade-up"><i class="fas fa-star text-yellow-500 mr-2"></i>Depoimentos</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-lg text-center" data-aos="zoom-in">
                    <i class="fas fa-quote-left text-gray-400 text-2xl mb-4"></i>
                    <p class="text-gray-600 mb-4">"Transformei meu corpo aqui! Treinos personalizados e equipe incrível."</p>
                    <p class="font-bold">- João Silva</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg text-center" data-aos="zoom-in" data-aos-delay="200">
                    <i class="fas fa-quote-left text-gray-400 text-2xl mb-4"></i>
                    <p class="text-gray-600 mb-4">"Ambiente motivador e resultados reais. Recomendo!"</p>
                    <p class="font-bold">- Maria Oliveira</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg text-center" data-aos="zoom-in" data-aos-delay="400">
                    <i class="fas fa-quote-left text-gray-400 text-2xl mb-4"></i>
                    <p class="text-gray-600 mb-4">"Yoga aqui me ajudou a encontrar equilíbrio e força."</p>
                    <p class="font-bold">- Pedro Santos</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-8 text-gray-800" data-aos="fade-right"><i class="fas fa-envelope text-purple-500 mr-2"></i>Contato</h2>
            <p class="text-lg text-gray-600 mb-8" data-aos="fade-right" data-aos-delay="100">Entre em contato conosco para mais informações ou para agendar uma consulta.</p>
            <form class="max-w-md mx-auto" data-aos="fade-up">
                <input type="text" placeholder="Nome" class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <input type="email" placeholder="Email" class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <textarea placeholder="Mensagem" class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"></textarea>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-full transition transform hover:scale-105">Enviar</button>
            </form>
        </div>
    </section>

    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-6">
        <div class="container mx-auto text-center">
            <p>&copy; 2023 Sthenos Gym. Todos os direitos reservados.</p>
        </div>
    </footer>

    <button id="back-to-top" class="fixed bottom-6 right-6 bg-yellow-500 hover:bg-yellow-600 text-black p-3 rounded-full shadow-lg transition opacity-0 pointer-events-none" style="z-index: 1000;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        const mobileNavToggle = document.getElementById('mobile-nav-toggle');
        const mobileNav = document.getElementById('mobile-nav');
        if (mobileNavToggle && mobileNav) {
            mobileNavToggle.addEventListener('click', () => {
                mobileNav.classList.toggle('hidden');
            });
        }

        // Back to top button
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.remove('opacity-0', 'pointer-events-none');
                backToTop.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                backToTop.classList.remove('opacity-100', 'pointer-events-auto');
                backToTop.classList.add('opacity-0', 'pointer-events-none');
            }
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>