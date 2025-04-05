// Vue 2 compatibility layer for Vue 3
export default {
  install(app) {
    // Add $createElement method that was available in Vue 2 but removed in Vue 3
    app.config.globalProperties.$createElement = (tag, data, children) => {
      // Simple implementation that mimics Vue 2's $createElement
      return { tag, data, children };
    };
  }
};
