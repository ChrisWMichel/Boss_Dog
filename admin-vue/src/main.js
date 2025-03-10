import { createApp } from "vue";
import "./style.css";
import "../../resources/js/bootstrap";
import { createPinia } from "pinia";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import router from "../vue-router";
import App from "./App.vue";

const pinia = createPinia();
const options = {
    position: "top-right",
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false,
};

const app = createApp(App);
app.use(Toast, options);
app.use(pinia);
app.use(router);

app.mount("#app");
