import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

const app = createApp(App);

// Create a global notification system
import Notifications from './components/Notifications.vue';
const NotificationsInstance = createApp(Notifications).mount(
  document.body.appendChild(document.createElement('div'))
);

// Make notifications available globally
app.config.globalProperties.$notifications = NotificationsInstance;

app.use(router).mount('#app');
