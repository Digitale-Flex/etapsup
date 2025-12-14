import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Sanctum SPA authentication : envoyer les cookies avec les requêtes API
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// CSRF Token pour auth:web (session Laravel)
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
}
