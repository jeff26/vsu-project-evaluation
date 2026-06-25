<template>
    <div class="space-y-6 max-w-300 mx-auto pb-12">
        <div class="bg-gradient-to-r from-emerald-800 to-emerald-950 p-6 rounded-xl shadow-xs text-white relative overflow-hidden">
            <div class="absolute right-0 bottom-0 opacity-10 translate-x-6 translate-y-6 pointer-events-none">
                <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2z"/></svg>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
                <div>
                    <h1 class="text-2xl font-black tracking-tight">Welcome Back, Technical Evaluator</h1>
                    <p class="text-emerald-100 text-xs mt-1 max-w-xl font-medium">
                        Visayas State University Project Evaluation Engine. Assess assigned portfolios using the structural performance criteria.
                    </p>
                </div>

                <div>
                    <button
                        @click="handleLogout"
                        class="bg-emerald-900/50 hover:bg-red-700/90 border border-emerald-700/60 hover:border-red-600/40 px-4 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all duration-150 flex items-center justify-center gap-2 cursor-pointer backdrop-blur-xs group"
                    >
                        <svg class="w-4 h-4 text-emerald-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Secure Session Exit
                    </button>
                </div>
            </div>
        </div>

        <div v-if="loading" class="flex flex-col items-center justify-center py-20 bg-white rounded-xl border border-slate-100 shadow-xs">
            <div class="w-9 h-9 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-3"></div>
            <div class="text-slate-500 font-semibold text-xs tracking-wide">Syncing reviewer profile metrics...</div>
        </div>

        <div v-else class="space-y-6 animate-fade-in">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Projects</div>
                        <div class="text-3xl font-black text-slate-800 mt-1">{{ dashboardData.metrics.total_assigned }}</div>
                    </div>
                    <div class="p-3 bg-slate-50 text-slate-600 rounded-lg font-bold text-sm">Roster</div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-xs flex items-center justify-between relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending Evaluation</div>
                        <div class="text-3xl font-black text-amber-600 mt-1">{{ dashboardData.metrics.pending_evaluations }}</div>
                    </div>
                    <div class="p-2.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold uppercase tracking-wider">Required</div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-xs flex items-center justify-between relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-600"></div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Completed Evaluation</div>
                        <div class="text-3xl font-black text-emerald-700 mt-1">{{ dashboardData.metrics.completed_evaluations }}</div>
                    </div>
                    <div class="p-2.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold uppercase tracking-wider">Verified</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200/60 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Assigned Projects for Evaluation</h3>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded font-bold">Live Context</span>
                </div>

                <div v-if="dashboardData.projects.length === 0" class="p-16 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 stroke-slate-300" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    <p class="text-sm font-bold text-slate-700">No Projects Found</p>
                    <p class="text-xs text-slate-400 mt-0.5">Your profile isn't linked to any active institutional research groups yet.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-5">Project Operational Scope / Title</th>
                            <th class="py-3 px-5">Implementing Unit</th>
                            <th class="py-3 px-5">Annual Report</th>
                            <th class="py-3 px-5 text-center">Audit Status</th>
                            <th class="py-3 px-5 text-right">Assigned Action</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                        <tr v-for="project in dashboardData.projects" :key="project.id" class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-5 text-slate-900 font-bold max-w-xs sm:max-w-md truncate">
                                {{ project.title }}
                            </td>
                            <td class="py-4 px-5 text-slate-500 text-xs">
                                {{ project.unit_center }}
                            </td>
                            <td class="py-4 px-5">
                                <a
                                    v-if="project.attachment"
                                    :href="'/storage/' + project.attachment"
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2 py-1.5 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    View PDF
                                </a>
                                <span v-else class="text-xs text-slate-400 italic">No file</span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                    <span v-if="project.status === 'pending'" class="inline-block bg-amber-50 text-amber-800 text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded border border-amber-200/50">
                                        Pending Scorecard
                                    </span>
                                <span v-else class="inline-block bg-emerald-50 text-emerald-800 text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded border border-emerald-200/50">
                                        {{ project.status === 'draft' ? 'Draft' : 'Completed' }} ({{ project.score }}%)
                                    </span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <button
                                    v-if="project.status === 'pending'"
                                    @click="navigateToEvaluation(project.id)"
                                    class="bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs py-1.5 px-4 rounded-lg shadow-2xs transition-colors cursor-pointer"
                                >
                                    Evaluate
                                </button>
                                <button
                                    v-else-if="project.status === 'draft'"
                                    @click="navigateToEvaluation(project.id)"
                                    class="bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs py-1.5 px-4 rounded-lg shadow-2xs transition-colors cursor-pointer"
                                >
                                    Update
                                </button>
                                <span v-else class="text-xs text-slate-400 font-bold pr-2 inline-block">
                                        Logged: {{ project.evaluated_at }}
                                    </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loading = ref(true);

const dashboardData = ref({
    metrics: { total_assigned: 0, completed_evaluations: 0, pending_evaluations: 0 },
    projects: []
});

const loadDashboardData = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('auth_token');

        const response = await axios.get('/api/evaluator/dashboard-metrics', {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });

        if (response.data.status === 'success') {
            dashboardData.value = response.data.data;
        }
    } catch (error) {
        console.error('Failed to parse dashboard telemetry details:', error);
    } finally {
        loading.value = false;
    }
};

// Add this right alongside your other function declarations (e.g., below loadDashboardData)
const handleLogout = async () => {
    try {
        const token = localStorage.getItem('auth_token');

        // 1. Notify Laravel Sanctum to destroy the current token token record
        await axios.post('/api/logout', {}, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });
    } catch (error) {
        // Log the error but continue flushing local data so the user isn't stuck trapped in the page
        console.warn('Backend session revoking was bypassed:', error);
    } finally {
        // 2. Clear out local state arrays completely
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_profile');

        // 3. Bounce the user back to the login screen
        await router.push('/');
    }
};

const navigateToEvaluation = (id) => {
    // Navigate directly to our evaluation scorecard input form page
    router.push({ path: '/evaluator/evaluate', query: { project_id: id } });
};

onMounted(() => {
    loadDashboardData();
});
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
