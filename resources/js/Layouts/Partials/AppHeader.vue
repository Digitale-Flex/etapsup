<script setup lang="ts">
import LogoBox from '@/Components/LogoBox.vue';
import { Link, router } from '@inertiajs/vue3';

import UserMenu from '@/Layouts/Partials/UserMenu.vue';
import {
    BIconLightningCharge,
    BIconSearch,
    BIconList,
    BIconX,
} from 'bootstrap-icons-vue';
import { onMounted, onUnmounted, ref } from 'vue';

let isSticky = ref<boolean>(false);
const isMobileMenuOpen = ref<boolean>(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

// Fermer le menu mobile quand on clique ailleurs
const handleClickOutside = (event: MouseEvent) => {
    if (!isMobileMenuOpen.value) return;

    const target = event.target as HTMLElement;
    const mobileMenu = document.getElementById('mobile-menu');
    const toggler = document.querySelector('.mobile-toggler');

    if (toggler?.contains(target)) return;
    if (mobileMenu?.contains(target)) return;

    closeMobileMenu();
};

onMounted(() => {
    window.addEventListener('scroll', () => {
        isSticky.value = window.scrollY >= 400;
    });

    document.addEventListener('click', handleClickOutside);

    router.on('start', () => {
        closeMobileMenu();
    });
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <header
        class="etapsup-header"
        :class="{ 'header-sticky-on': isSticky }"
    >
        <nav class="navbar-etapsup">
            <div class="navbar-container">
                <!-- Mobile: Hamburger à gauche -->
                <button
                    class="mobile-toggler d-lg-none"
                    type="button"
                    @click="toggleMobileMenu"
                    aria-label="Toggle menu"
                >
                    <BIconX v-if="isMobileMenuOpen" />
                    <BIconList v-else />
                </button>

                <!-- Logo centré sur mobile, à gauche sur desktop -->
                <LogoBox class="navbar-logo" />

                <!-- Desktop: Navigation principale -->
                <div class="navbar-nav-desktop d-none d-lg-flex">
                    <Link
                        :href="route('establishments.index')"
                        class="nav-btn"
                        :class="{ 'active': route().current('establishments.*') }"
                    >
                        <BIconSearch class="nav-icon" />
                        Établissements
                    </Link>
                    <Link
                        :href="route('custom-search.index')"
                        class="nav-btn nav-btn-accent"
                        :class="{ 'active': route().current('custom-search.*') }"
                    >
                        <BIconLightningCharge class="nav-icon" />
                        Accompagnement
                    </Link>
                </div>

                <!-- Desktop: Auth -->
                <div class="navbar-auth d-none d-lg-flex">
                    <UserMenu v-if="$page.props.auth?.user" />
                    <Link
                        v-else
                        :href="route('login')"
                        class="btn-login"
                    >Se connecter</Link>
                </div>

                <!-- Mobile: Bouton connexion à droite -->
                <Link
                    v-if="!$page.props.auth?.user"
                    :href="route('login')"
                    class="mobile-login d-lg-none"
                >Connexion</Link>
                <UserMenu v-else class="d-lg-none" />
            </div>

            <!-- Mobile Menu Dropdown -->
            <div
                v-show="isMobileMenuOpen"
                id="mobile-menu"
                class="mobile-menu d-lg-none"
            >
                <Link
                    :href="route('establishments.index')"
                    class="mobile-nav-link"
                    :class="{ 'active': route().current('establishments.*') }"
                >
                    <BIconSearch class="nav-icon" />
                    Établissements
                </Link>
                <Link
                    :href="route('custom-search.index')"
                    class="mobile-nav-link"
                    :class="{ 'active': route().current('custom-search.*') }"
                >
                    <BIconLightningCharge class="nav-icon" />
                    Accompagnement
                </Link>
            </div>
        </nav>
    </header>
</template>

<style scoped>
/* EtapSup Header - Pixel Perfect Responsive */
.etapsup-header {
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-sticky-on {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.navbar-etapsup {
    width: 100%;
}

.navbar-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1320px;
    margin: 0 auto;
    padding: 12px 16px;
    position: relative;
}

/* Logo */
:deep(.navbar-logo) {
    display: flex;
    align-items: center;
}

:deep(.navbar-logo img) {
    height: 40px;
    width: auto;
}

/* Desktop Navigation */
.navbar-nav-desktop {
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.95rem;
    text-decoration: none;
    color: #374151;
    background: #f3f4f6;
    transition: all 0.2s ease;
}

.nav-btn:hover,
.nav-btn.active {
    background: #1e3a8a;
    color: white;
}

.nav-btn-accent {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.nav-btn-accent:hover,
.nav-btn-accent.active {
    background: #3b82f6;
    color: white;
}

.nav-icon {
    font-size: 1rem;
}

/* Desktop Auth */
.navbar-auth {
    display: flex;
    align-items: center;
}

.btn-login {
    padding: 10px 20px;
    border: 2px solid #1e3a8a;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    color: #1e3a8a;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-login:hover {
    background: #1e3a8a;
    color: white;
}

/* Mobile Toggler */
.mobile-toggler {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: none;
    background: #f3f4f6;
    border-radius: 8px;
    font-size: 1.5rem;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
}

.mobile-toggler:hover {
    background: #e5e7eb;
}

/* Mobile Login Button */
.mobile-login {
    padding: 8px 14px;
    border: 1px solid #1e3a8a;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.85rem;
    color: #1e3a8a;
    text-decoration: none;
    white-space: nowrap;
}

/* Mobile Menu */
.mobile-menu {
    background: white;
    border-top: 1px solid #e5e7eb;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.mobile-nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 1rem;
    text-decoration: none;
    color: #374151;
    background: #f9fafb;
    transition: all 0.2s ease;
}

.mobile-nav-link:hover,
.mobile-nav-link.active {
    background: #1e3a8a;
    color: white;
}

/* Mobile Responsive - Logo centré */
@media (max-width: 991px) {
    .navbar-container {
        display: grid;
        grid-template-columns: 40px 1fr 80px;
        gap: 8px;
    }

    :deep(.navbar-logo) {
        justify-self: center;
    }

    .mobile-login {
        justify-self: end;
    }
}

/* Small mobile adjustments */
@media (max-width: 576px) {
    .navbar-container {
        padding: 10px 12px;
    }

    :deep(.navbar-logo img) {
        height: 32px;
    }

    .mobile-login {
        padding: 6px 10px;
        font-size: 0.8rem;
    }

    .mobile-toggler {
        width: 36px;
        height: 36px;
        font-size: 1.25rem;
    }
}
</style>
