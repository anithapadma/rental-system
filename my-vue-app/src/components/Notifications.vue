<template>
  <div class="fixed top-4 right-4 z-50 w-80">
    <transition-group name="notification" tag="div" class="space-y-2">
      <div 
        v-for="notification in notifications" 
        :key="notification.id"
        :class="[
          'p-4 rounded-md shadow-lg transform transition-all duration-300',
          getNotificationClass(notification.type)
        ]"
      >
        <div class="flex justify-between">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg v-if="notification.type === 'success'" class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <svg v-else-if="notification.type === 'error'" class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
              <svg v-else-if="notification.type === 'warning'" class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <svg v-else class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium" :class="getTextClass(notification.type)">
                {{ notification.title }}
              </h3>
              <div class="mt-1 text-sm" :class="getTextClass(notification.type)">
                {{ notification.message }}
              </div>
            </div>
          </div>
          <div class="ml-4 flex-shrink-0 flex">
            <button 
              @click="removeNotification(notification.id)" 
              class="inline-flex text-gray-400 hover:text-gray-500"
            >
              <span class="sr-only">Close</span>
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script>
export default {
  name: 'Notifications',
  data() {
    return {
      notifications: []
    };
  },
  methods: {
    getNotificationClass(type) {
      switch(type) {
        case 'success':
          return 'bg-green-50 border-l-4 border-green-400';
        case 'error':
          return 'bg-red-50 border-l-4 border-red-400';
        case 'warning':
          return 'bg-yellow-50 border-l-4 border-yellow-400';
        default:
          return 'bg-blue-50 border-l-4 border-blue-400';
      }
    },
    getTextClass(type) {
      switch(type) {
        case 'success':
          return 'text-green-800';
        case 'error':
          return 'text-red-800';
        case 'warning':
          return 'text-yellow-800';
        default:
          return 'text-blue-800';
      }
    },
    addNotification(notification) {
      const id = Date.now().toString();
      this.notifications.push({
        id,
        ...notification
      });
      
      // Auto-remove after timeout
      setTimeout(() => {
        this.removeNotification(id);
      }, notification.timeout || 5000);
    },
    removeNotification(id) {
      const index = this.notifications.findIndex(n => n.id === id);
      if (index !== -1) {
        this.notifications.splice(index, 1);
      }
    }
  }
};
</script>

<style scoped>
.notification-enter-active, .notification-leave-active {
  transition: all 0.5s;
}
.notification-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.notification-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>