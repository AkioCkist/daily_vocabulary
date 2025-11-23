import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');

// Set default headers for authenticated requests
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.warn('CSRF token not found');
}

// Enable credentials for cross-domain requests
window.axios.defaults.withCredentials = true;
