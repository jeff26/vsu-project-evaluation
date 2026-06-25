<template>
    <header class="bg-white px-6 py-4 rounded-xl shadow-sm border border-slate-200/60 flex justify-between items-center w-full">
        <div class="flex items-center space-x-3">
            <button
                @click="$emit('toggle-sidebar')"
                class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none cursor-pointer"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">System Workspace</h1>
            </div>
        </div>

        <div class="flex items-center space-x-3 bg-slate-50 border border-slate-100 px-4 py-2 rounded-lg">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <div class="text-sm font-medium text-slate-600">
                Account: <strong class="text-slate-900 font-bold">{{ user?.name }}</strong>
                <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
          {{ user?.role }}
        </span>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue';

defineEmits(['toggle-sidebar']);
const user = ref(JSON.parse(localStorage.getItem('user_profile')) || null);
</script>
