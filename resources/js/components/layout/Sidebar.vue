<template>
    <aside class="w-64 h-screen sticky top-0 bg-emerald-950 text-slate-100 flex flex-col justify-between p-5 shadow-xl border-r border-emerald-900/40 flex-shrink-0 z-50 transition-all duration-300">
        <div>
            <div class="flex flex-col items-center justify-center py-4 border-b border-emerald-900 mb-8">
                <div class="text-xs uppercase tracking-widest font-semibold text-amber-400 mb-1">VSU Project</div>
                <div class="text-lg font-black tracking-wider text-white text-center">Evaluation System</div>
            </div>

            <nav class="space-y-1.5">
                <router-link
                    to="/admin/dashboard"
                    active-class="bg-emerald-900/60 text-amber-300 font-semibold border-l-4 border-amber-500 shadow-inner"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-emerald-900/30 hover:text-slate-200 border-l-4 border-transparent transition-all duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                    </svg>
                    <span>Dashboard</span>
                </router-link>

                <router-link
                    to="/admin/projects"
                    active-class="bg-emerald-900/60 text-amber-300 font-semibold border-l-4 border-amber-500 shadow-inner"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-emerald-900/30 hover:text-slate-200 border-l-4 border-transparent transition-all duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <span>Manage Projects</span>
                </router-link>
                <router-link
                    to="/admin/thrust"
                    active-class="bg-emerald-900/60 text-amber-300 font-semibold border-l-4 border-amber-500 shadow-inner"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-emerald-900/30 hover:text-slate-200 border-l-4 border-transparent transition-all duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <span>Program thrust</span>
                </router-link>
                <router-link
                    to="/admin/proponents"
                    active-class="bg-emerald-900/60 text-amber-300 font-semibold border-l-4 border-amber-500 shadow-inner"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-emerald-900/30 hover:text-slate-200 border-l-4 border-transparent transition-all duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <span>Project proponents</span>
                </router-link>
                <router-link
                    to="/admin/users"
                    active-class="bg-emerald-900/60 text-amber-300 font-semibold border-l-4 border-amber-500 shadow-inner"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-emerald-900/30 hover:text-slate-200 border-l-4 border-transparent transition-all duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>User Accounts</span>
                </router-link>
            </nav>
        </div>

        <button
            @click="handleLogout"
            class="w-full flex items-center justify-center space-x-2 bg-emerald-900/30 hover:bg-red-900/40 text-slate-300 hover:text-red-200 border border-emerald-800 hover:border-red-900 font-medium py-2.5 px-4 rounded-lg transition duration-200 cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span>Sign Out</span>
        </button>
    </aside>
</template>

<script setup>
import axios from 'axios';

const handleLogout = async () => {
    try {
        await axios.post('/api/logout');
    } catch (e) {
        console.error('Logout error:', e);
    } finally {
        localStorage.clear();
        window.location.href = '/';
    }
};
</script>
