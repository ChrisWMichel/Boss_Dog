import { createWebHistory, createRouter } from "vue-router";

import HomeView from "../views/HelloWorld.vue";
import dashboard from "../views/admin/dashboard.vue";
import login from "../views/auth/loginForm.vue";
import RequestPassword from "../views/auth/RequestPassword.vue";
import resetPassword from "../views/auth/resetPassword.vue";
import AppLayout from "../src/components/Layouts/AppLayout.vue";
import Products from "../views/admin/Products/index.vue";
import { useUserStore } from "../store/useUserStore";
import NotFound from "../views/NotFound.vue";

const routes = [
    {
        path: "/app",
        name: "app",
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: "dashboard",
                name: "app.dashboard",
                component: dashboard,
            },
            {
                path: "products",
                name: "app.products",
                component: Products,
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
        meta: {
            requiresGuest: true,
        },
    },
    {
        path: "/request-password",
        name: "requestPassword",
        component: RequestPassword,
        meta: {
            requiresGuest: true,
        },
    },
    {
        path: "/reset-password/:token",
        name: "resetPassword",
        component: resetPassword,
        meta: {
            requiresGuest: true,
        },
    },
    {
        path: "/:pathMatch(.*)*",
        name: "not-found",
        component: NotFound,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const userStore = useUserStore();
    if (to.meta.requiresAuth && !userStore.user.token) {
        return next({ name: "login" });
    } else if (to.meta.requiresGuest && userStore.user.token) {
        return next({ name: "app.dashboard" });
    } else {
        return next();
    }
});

export default router;
