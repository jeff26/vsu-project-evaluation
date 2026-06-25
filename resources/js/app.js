import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router'; // <-- ADD THIS IMPORT

const app = createApp(App);

app.use(router); // <-- ADD THIS LINE

app.mount('#app');
