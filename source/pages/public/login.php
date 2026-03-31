<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sthenos Gym - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .backdrop {
            background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&h=1080');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-100 to-gray-200 text-gray-900">

    <header class="bg-gradient-to-r from-gray-800 to-gray-900 text-white p-4 md:p-6 shadow-lg">
        <div class="container mx-auto flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-bold">Sthenos Gym</h1>
            <div class="flex items-center gap-2">
                <button id="mobile-nav-toggle" class="md:hidden p-2 rounded-md border border-white/20 hover:bg-white/10 transition" aria-label="Abrir menu">
                    <i class="fas fa-bars"></i>
                </button>
                <nav id="main-nav" class="hidden md:flex items-center space-x-6 text-sm">
                    <a href="landing.php" class="hover:text-yellow-400 transition flex items-center"><i class="fas fa-home mr-1"></i>Início</a>
                    <a href="#" class="hover:text-yellow-400 transition flex items-center"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
                </nav>
            </div>
        </div>
        <div id="mobile-nav" class="md:hidden hidden bg-gray-900/95 border-t border-gray-700">
            <a href="landing.php" class="block px-4 py-3 text-white hover:bg-gray-800">Início</a>
            <a href="#" class="block px-4 py-3 text-white hover:bg-gray-800">Login</a>
        </div>
    </header>

    <main class="min-h-screen flex items-center justify-center p-6">
        <section class="w-full max-w-md bg-white/90 backdrop-blur-md border border-gray-200 rounded-3xl shadow-2xl p-8 md:p-10" data-aos="fade-up">
            <?php
            $error = $_GET['error'] ?? '';
            if ($error) {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>$error</div>";
            }

            ?>
            <h2 class="text-3xl font-extrabold text-gray-800 mb-4 text-center">Entrar na sua conta</h2>
            <p class="text-gray-600 mb-6 text-center">Use seu email e senha para acessar o painel de treinos.</p>
            <form action="../../API/auth/login.php" method="post" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input id="email" name="email" type="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="seu@exemplo.com"/>
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Senha</label>
                    <input id="password" name="password" type="password" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="•••••••••"/>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"> Lembrar-me</label>
                    <a href="#" class="text-blue-600 hover:text-blue-700">Esqueceu a senha?</a>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-full transition transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500">Login</button>
            </form>
            <p class="mt-6 text-center text-sm text-gray-500">Não tem uma conta? <a href="#" class="text-yellow-500 hover:text-yellow-600 font-semibold">Cadastre-se</a></p>
        </section>
    </main>

    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-5">
        <div class="container mx-auto text-center text-sm md:text-base">&copy; 2023 Sthenos Gym. Todos os direitos reservados.</div>
    </footer>

    <button id="back-to-top" class="fixed bottom-6 right-6 bg-yellow-500 hover:bg-yellow-600 text-black p-3 rounded-full shadow-lg transition opacity-0 pointer-events-none" style="z-index:1000;">
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
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 250) {
                backToTop.classList.remove('opacity-0', 'pointer-events-none');
                backToTop.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                backToTop.classList.remove('opacity-100', 'pointer-events-auto');
                backToTop.classList.add('opacity-0', 'pointer-events-none');
            }
        });
        backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    </script>
</body>
</html>