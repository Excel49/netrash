// Import Bootstrap JS
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Import Axios
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import AlpineJS (jika ingin menggunakan)
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();