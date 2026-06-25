import axios from 'axios';
window.axios = axios;

// 🌟 ADD THIS: Point Axios explicitly to your Laravel server port
// window.axios.defaults.baseURL = 'http://127.0.0.1:8000'; // Make sure this matches your 'php artisan serve' URL

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// 🌟 ADD THIS LINE: Force Laravel to always return JSON, even for errors
window.axios.defaults.headers.common['Accept'] = 'application/json';
