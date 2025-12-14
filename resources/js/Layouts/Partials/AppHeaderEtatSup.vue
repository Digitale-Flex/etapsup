<script setup lang="ts">
// Refonte: Header EtapSup (sans Mareza)
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const isSticky = ref<boolean>(false);
const showMobileMenu = ref<boolean>(false);

if (typeof window !== 'undefined') {
    window.addEventListener('scroll', () => {
        isSticky.value = window.scrollY >= 100;
    });
}

const isMobile = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.innerWidth <= 991;
});
</script>

<template>
    <header
        class="etapsup-header"
        :class="{ 'header-sticky': isSticky }"
    >
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Logo EtapSup -->
                <Link href="/" class="navbar-brand">
                    <span class="logo-text">EtapSup</span>
                </Link>

                <!-- Mobile toggle -->
                <button
                    v-if="isMobile"
                    class="navbar-toggler"
                    type="button"
                    @click="showMobileMenu = !showMobileMenu"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation -->
                <div
                    class="collapse navbar-collapse"
                    :class="{ 'show': showMobileMenu }"
                >
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item">
                            <Link href="/" class="nav-link">Accueil</Link>
                        </li>
                        <li class="nav-item">
                            <Link href="/events" class="nav-link">Événements</Link>
                        </li>
                        <li class="nav-item">
                            <Link href="/about" class="nav-link">À propos</Link>
                        </li>
                        <li class="nav-item">
                            <Link href="/contact" class="nav-link">Contact</Link>
                        </li>
                        <li class="nav-item ms-lg-3">
                            <Link href="/login" class="btn btn-sm btn-primary-etapsup">
                                Connexion
                            </Link>
                        </li>
                        <li class="nav-item ms-2">
                            <Link href="/register" class="btn btn-sm btn-outline-primary-etapsup">
                                Inscription
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>

<style scoped>
.etapsup-header {
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease;
}

.etapsup-header.header-sticky {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.logo-text {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.navbar {
    padding: 1rem 0;
}

.nav-link {
    color: #374151;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: color 0.2s ease;
}

.nav-link:hover {
    color: #1e3a8a;
}

.btn-primary-etapsup {
    background: linear-gradient(45deg, #ed2939, #cc1f2d);
    border: none;
    color: white;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-primary-etapsup:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(237, 41, 57, 0.3);
}

.btn-outline-primary-etapsup {
    border: 2px solid #1e3a8a;
    color: #1e3a8a;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-outline-primary-etapsup:hover {
    background: #1e3a8a;
    color: white;
}

@media (max-width: 991px) {
    .navbar-nav {
        padding: 1rem 0;
    }

    .nav-item {
        margin: 0.5rem 0;
    }

    .btn-primary-etapsup,
    .btn-outline-primary-etapsup {
        display: block;
        width: 100%;
        text-align: center;
    }
}
</style>
