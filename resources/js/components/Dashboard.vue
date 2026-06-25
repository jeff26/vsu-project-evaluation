<template>
    <div class="space-y-6">
        <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl shadow-xs border border-slate-100">
            <div class="w-10 h-10 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-4"></div>
            <div class="text-slate-500 font-semibold text-sm tracking-wide">Assembling dashboard aggregates...</div>
        </div>

        <div v-else class="space-y-6 animate-fade-in">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-emerald-700"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Active Evaluators</div>
                            <div class="mt-2 text-4xl font-black text-slate-800 tracking-tight">{{ stats.total_users }}</div>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-lg text-emerald-700 group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-amber-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Registered Institutional Projects</div>
                            <div class="mt-2 text-4xl font-black text-slate-800 tracking-tight">{{ stats.total_projects }}</div>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-lg text-amber-600 group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 lg:col-span-1 flex flex-col">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-800">Distribution by Unit</h3>
                        <span class="text-xs bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded font-medium">Aggregated</span>
                    </div>

                    <div v-if="stats.projects_by_unit.length === 0" class="flex flex-col items-center justify-center py-12 flex-1 text-slate-400">
                        <svg class="w-8 h-8 mb-2 stroke-slate-300" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-4m-8 0H4"></path>
                        </svg>
                        <span class="text-xs font-medium">No unit metrics recorded.</span>
                    </div>

                    <ul v-else class="divide-y divide-slate-100 max-h-[350px] overflow-y-auto pr-1 flex-1">
                        <li v-for="unit in stats.projects_by_unit" :key="unit.unit_center" class="py-3 flex justify-between items-center group hover:bg-slate-50/80 px-2 rounded-lg transition-colors">
                            <span class="text-slate-700 font-semibold text-sm truncate max-w-[160px]">{{ unit.unit_center }}</span>
                            <span class="bg-emerald-50 text-emerald-800 group-hover:bg-emerald-700 group-hover:text-white font-bold text-xs px-2.5 py-1 rounded-full border border-emerald-100 transition-all">
                {{ unit.total }} Projects
              </span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 lg:col-span-2 flex flex-col">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-800">Recent Program Submissions</h3>
                        <span class="text-xs text-slate-400">Live feed</span>
                    </div>

                    <div v-if="stats.recent_projects.length === 0" class="flex flex-col items-center justify-center py-20 flex-1 text-slate-400">
                        <svg class="w-10 h-10 mb-2 stroke-slate-300" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-sm font-medium">No system entries found.</span>
                    </div>

                    <div v-else class="overflow-x-auto rounded-lg border border-slate-100 flex-1">
                        <table class="w-full text-left text-sm text-slate-600 border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider, sticky top-0 z-10">
                            <tr>
                                <th class="py-3.5 px-4">Project Scope / Title</th>
                                <th class="py-3.5 px-4 text-right">Implementing Unit</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            <tr v-for="project in stats.recent_projects" :key="project.id" class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-slate-900 max-w-sm truncate">{{ project.title }}</td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-500">
                    <span class="inline-block bg-slate-100 text-slate-700 px-2.5 py-1 rounded text-xs border border-slate-200/30">
                      {{ project.unit_center || 'Unassigned' }}
                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Component Analytical State Definitions
const loading = ref(true);
const stats = ref({
    total_users: 0,
    total_projects: 0,
    projects_by_unit: [],
    recent_projects: []
});

// Pull core statistical metrics directly from endpoints
const fetchDashboardData = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/admin/dashboard', {
            headers: { Authorization: `Bearer ${token}`,Accept: 'application/json' }
        });
        if (response.data.status === 'success') {
            stats.value = response.data.data;
        }
    } catch (error) {
        console.error('Failed to resolve system telemetry parameters:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.35s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
