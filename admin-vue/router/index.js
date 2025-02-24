import { createWebHistory, createRouter } from "vue-router";

import HomeView from "../views/HelloWorld.vue";
import dashboard from "../views/dashboard.vue";
import login from "../views/auth/loginForm.vue";
import RequestPassword from "../views/auth/RequestPassword.vue";
import resetPassword from "../views/auth/resetPassword.vue";
import AppLayout from "../src/components/Layouts/AppLayout.vue";

const routes = [
    {
        path: "/app",
        name: "app",
        component: AppLayout,
        children: [
            {
                path: "dashboard",
                name: "app.dashboard",
                component: dashboard,
            },
        ],
    },
    {
        path: "/",
        name: "home",
        component: HomeView,
    },

    {
        path: "/login",
        name: "login",
        component: login,
    },
    {
        path: "/request-password",
        name: "requestPassword",
        component: RequestPassword,
    },
    {
        path: "/reset-password/:token",
        name: "resetPassword",
        component: resetPassword,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
