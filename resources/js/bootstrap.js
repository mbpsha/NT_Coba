import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Attach CSRF token for non-GET requests
const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
if (csrfTokenMeta && csrfTokenMeta.content) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfTokenMeta.content;
}
