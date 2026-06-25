<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-900 px-4 py-12 sm:px-6 lg:px-8 font-sans antialiased">
        <div class="max-w-md w-full space-y-8 bg-white/95 backdrop-blur-md p-8 sm:p-10 rounded-2xl shadow-2xl border border-white/20 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-2 bg-amber-500"></div>

            <div class="text-center">
                <img
                    src="/images/vsu_logo_official.png"
                    alt="VSU Official Logo"
                    class="mx-auto h-24 w-auto drop-shadow-md object-contain transition-transform duration-300 hover:scale-105"
                />
                <h2 class="mt-4 text-2xl font-black text-slate-800 tracking-tight">
                    Project Evaluation System
                </h2>
                <p class="mt-1.5 text-sm text-slate-500">
                    Welcome back! Access your secure workspace.
                </p>
            </div>

            <form class="mt-8 space-y-5" @submit.prevent="handleLogin">

                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold uppercase tracking-wider text-slate-600 block">
                        Email Address
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                        <input
                            id="email"
                            type="email"
                            v-model="email"
                            autocomplete="email"
                            required
                            class="w-full text-sm text-slate-800 px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-lg outline-none focus:bg-white focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 transition-all duration-150 placeholder:text-slate-400"
                            placeholder="name@vsu.edu.ph"
                        />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold uppercase tracking-wider text-slate-600 block">
                        Password
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                        <input
                            id="password"
                            type="password"
                            v-model="password"
                            autocomplete="current-password"
                            required
                            class="w-full text-sm text-slate-800 px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-lg outline-none focus:bg-white focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 transition-all duration-150 placeholder:text-slate-400"
                            placeholder="••••••••"
                        />
                    </div>
                </div>

                <div
                    v-if="errorMessage"
                    class="flex items-center space-x-2.5 p-3 rounded-lg bg-red-50 border border-red-100 text-red-700 animate-shake"
                >
                    <svg class="w-5 h-5 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-xs font-semibold tracking-wide">{{ errorMessage }}</span>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-emerald-950 bg-amber-400 hover:bg-amber-500 active:bg-amber-600 focus:outline-none focus:ring-4 focus:ring-amber-400/20 shadow-md disabled:opacity-60 transition-all duration-150 cursor-pointer"
                    >
                        <div v-if="isLoading" class="w-5 h-5 border-2 border-emerald-950 border-t-transparent rounded-full animate-spin"></div>
                        <span v-else class="tracking-wide">Sign In</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

// Component Navigation & Context Hooks
const router = useRouter();

// Reactive State Parameters
const email = ref('');
const password = ref('');
const errorMessage = ref('');
const isLoading = ref(false);

// Handle API Login Lifecycle Action
const handleLogin = async () => {
    try {
        errorMessage.value = '';
        isLoading.value = true;

        const response = await axios.post('/api/login', {
            email: email.value,
            password: password.value
        });

        // Populate Authorization Client Storage Variables
        localStorage.setItem('auth_token', response.data.token);
        localStorage.setItem('user_profile', JSON.stringify(response.data.user));

        // Redirect workflow relative to active security role
        if (response.data.user.role === 'admin') {
            router.push('/admin/dashboard');
        } else {
            router.push('/evaluator/dashboard');
        }
    } catch (error) {
        const isValidationError = error.response?.status === 422 || error.response?.status === 401;
        errorMessage.value = isValidationError ? 'Invalid credentials.' : 'Authentication service error.';
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.animate-shake {
    animation: shake 0.25s ease-in-out;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}
</style>
