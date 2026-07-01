import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';
import Layout from './components/layout/Layout.vue'; // <-- Import the Master Shell
import Dashboard from './components/Dashboard.vue';
import Projects from './components/Projects.vue';   // <-- Import the Projects view
import ProjectThrusts from "./components/ProjectThrusts.vue";
import ProjectMembers from "./components/ProjectMembers.vue";
import EvaluatorDashboard from './components/evaluator/EvaluatorDashboard.vue';
import EvaluateView from "./components/evaluator/EvaluateView.vue";
import UserManagement from './components/UserManagement.vue';
import Evaluators from './components/ProjectEvaluator.vue';

const routes = [
    {
        path: '/',
        name: 'login',
        component: Login,
        meta: { guest: true }
    },
    {
        path: '/admin',
        component: Layout, // Parent frame: Loads Sidebar, Navbar, and Footer
        meta: { requiresAuth: true, role: 'admin' }, // Applies security to ALL children instantly
        children: [
            {
                path: 'dashboard',
                name: 'admin.dashboard',
                component: Dashboard // Injected into layout's AppMain router-view
            },
            {
                path: 'projects',
                name: 'admin.projects',
                component: Projects // Injected into layout's AppMain router-view
            },
            {
                path: 'thrust',
                name: 'admin.thrust',
                component: ProjectThrusts // Injected into layout's AppMain router-view
            },
            {
                path: 'proponents',
                name: 'admin.proponents',
                component: ProjectMembers // Injected into layout's AppMain router-view
            },
            {
                path: 'evaluators',
                name: 'admin.evaluators',
                component: Evaluators // Injected into layout's AppMain router-view
            },
            {
                path: 'users',
                name: 'admin.users',
                component: UserManagement // Injected into layout's AppMain router-view
            }
        ]
    },
    {
        path: '/evaluator/dashboard',
        component: EvaluatorDashboard,
        meta: { requiresAuth: true, role: 'evaluator' }
    },
    {
        path: '/evaluator/evaluate',
        component: EvaluateView,
        meta: { requiresAuth: true, role: 'evaluator' }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Protect routes before loading components
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token');
    const userProfile = localStorage.getItem('user_profile');
    const user = userProfile ? JSON.parse(userProfile) : null;

    // 1. GUEST CHECK: If already logged in and trying to go to login screen, auto-bounce to correct space
    if (to.matched.some(record => record.meta.guest) && token && user) {
        if (user.role === 'admin') {
            return next('/admin/dashboard');
        } else if (user.role === 'evaluator') {
            return next('/evaluator/dashboard'); // 🌟 Bounce logged-in evaluators here
        }
    }

    // 2. AUTH PROTECTION CHECK: Must be logged in for these routes
    if (to.matched.some(record => record.meta.requiresAuth) && !token) {
        return next('/');
    }

    // 3. ADMINISTRATIVE ROLE CHECK
    if (to.matched.some(record => record.meta.role === 'admin') && (!user || user.role !== 'admin')) {
        // If logged in but not an admin, send them to their own dashboard instead of throwing them completely out
        if (user && user.role === 'evaluator') return next('/evaluator/dashboard');
        return next('/');
    }

    // 4. EVALUATOR ROLE CHECK (🌟 ADDED)
    if (to.matched.some(record => record.meta.role === 'evaluator') && (!user || user.role !== 'evaluator')) {
        // If logged in but not an evaluator (e.g. an Admin trying to view evaluator forms directly), send to admin dashboard
        if (user && user.role === 'admin') return next('/admin/dashboard');
        return next('/');
    }

    next();
});

export default router;
