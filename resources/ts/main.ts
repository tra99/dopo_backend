import { createApp } from 'vue';

import App from '@/App.vue';
import { registerPlugins } from '@core/utils/plugins';

import '../styles/app.css'; // Import Tailwin
// Styles
import '@core-scss/template/index.scss';
import '@styles/styles.scss';


// Create vue app
const app = createApp(App)

// Register plugins
registerPlugins(app)
// Mount vue app
app.mount('#app')
