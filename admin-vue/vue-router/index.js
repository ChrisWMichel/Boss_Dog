import { createWebHistory, createRouter } from "vue-router";

import HomeView from "../views/Home.vue";
import dashboard from "../views/admin/dashboard.vue";
import login from "../views/auth/loginForm.vue";
import RequestPassword from "../views/auth/RequestPassword.vue";
import resetPassword from "../views/auth/resetPassword.vue";
import AppLayout from "../src/components/Layouts/AppLayout.vue";
import Products from "../views/admin/Products/index.vue";
import { useUserStore } from "../store/useUserStore";
import Orders from "../views/admin/Orders/index.vue";
import OrderView from "../views/admin/Orders/orderView.vue";
import Users from "../views/admin/Users/index.vue";
import Customers from "../views/admin/Customers/index.vue";
//import NotFound from "../views/NotFound.vue";

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
            {
                path: "orders",
                name: "app.orders",
                component: Orders,
            },
            {
                path: "orders/:id",
                name: "app.order.view",
                component: OrderView,
            },
            {
                path: "users",
                name: "app.users",
                component: Users,
            },
            {
                path: "customers",
                name: "app.customers",
                component: Customers,
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
        name: "NotFound",
        beforeEnter() {
            window.location = "/";
        },
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
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
