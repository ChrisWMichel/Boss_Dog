// Global mixin to ensure proper cleanup of resources
export default {
  created() {
    // Initialize arrays to track resources that need cleanup
    this._eventListeners = [];
    this._timers = [];
    
    // Override addEventListener to track listeners
    const originalAddEventListener = this.$el && this.$el.addEventListener;
    if (originalAddEventListener) {
      this.$el.addEventListener = (event, handler, options) => {
        originalAddEventListener.call(this.$el, event, handler, options);
        this._eventListeners.push({ element: this.$el, event, handler });
      };
    }
    
    // Override setTimeout and setInterval
    const originalSetTimeout = setTimeout;
    const originalSetInterval = setInterval;
    
    this.$setTimeout = (handler, timeout, ...args) => {
      const timerId = originalSetTimeout(handler, timeout, ...args);
      this._timers.push({ id: timerId, type: 'timeout' });
      return timerId;
    };
    
    this.$setInterval = (handler, timeout, ...args) => {
      const timerId = originalSetInterval(handler, timeout, ...args);
      this._timers.push({ id: timerId, type: 'interval' });
      return timerId;
    };
  },
  
  beforeUnmount() {
    // Clean up event listeners
    if (this._eventListeners) {
      this._eventListeners.forEach(({ element, event, handler }) => {
        element.removeEventListener(event, handler);
      });
      this._eventListeners = [];
    }
    
    // Clean up timers
    if (this._timers) {
      this._timers.forEach(timer => {
        if (timer.type === 'timeout') {
          clearTimeout(timer.id);
        } else if (timer.type === 'interval') {
          clearInterval(timer.id);
        }
      });
      this._timers = [];
    }
    
    // Force a small delay to help with garbage collection
    if (!window._vueCleanupTimers) {
      window._vueCleanupTimers = [];
    }
    
    const cleanupTimer = setTimeout(() => {
      // This empty timeout can help with releasing resources
      const index = window._vueCleanupTimers.indexOf(cleanupTimer);
      if (index !== -1) {
        window._vueCleanupTimers.splice(index, 1);
      }
    }, 50);
    
    window._vueCleanupTimers.push(cleanupTimer);
  }
};
