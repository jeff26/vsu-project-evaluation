<template>
    <div class="space-y-6 max-w-300 mx-auto pb-12">
        <div class="bg-gradient-to-r from-emerald-800 to-emerald-950 p-6 rounded-xl shadow-xs text-white relative overflow-hidden">
            <div class="absolute right-0 bottom-0 opacity-10 translate-x-6 translate-y-6 pointer-events-none">
                <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2z"/></svg>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
                <div>
                    <h1 class="text-2xl font-black tracking-tight">Welcome Back, {{ user.name }}</h1>
                    <p class="text-emerald-100 text-xs mt-1 max-w-xl font-medium">
                        Visayas State University Technical Review Committee.
                    </p>
                </div>
                <button @click="handleLogout" class="bg-emerald-900/50 hover:bg-red-700/90 px-4 py-2.5 rounded-xl text-xs font-bold transition-all">
                    Secure Session Exit
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-xs">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Projects Assigned</div>
                <div class="text-3xl font-black text-slate-800 mt-1">{{ dashboardData.metrics.total_assigned }}</div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-xs relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending for Evaluation</div>
                <div class="text-3xl font-black text-amber-600 mt-1">{{ dashboardData.metrics.pending_evaluations }}</div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-xs relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-400"></div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Drafts</div>
                <div class="text-3xl font-black text-slate-600 mt-1">{{ dashboardData.metrics.draft_evaluations }}</div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-xs relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-600"></div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Completed Evaluation</div>
                <div class="text-3xl font-black text-emerald-700 mt-1">{{ dashboardData.metrics.completed_evaluations }}</div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white mb-5 p-4 rounded-xl shadow-xs border border-slate-200/60">
            <div class="flex items-center gap-3 max-w-lg">
                <label class="text-sm font-medium text-slate-700 whitespace-nowrap">
                    Select a Program Thrust
                </label>
                <select
                    v-model="selectedThrust"
                    class="flex-1 px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-colors text-slate-600"
                    @change="loadDashboardData"
                >
                    <option v-for="thrust in thrustOptions" :key="thrust.thrust_id" :value="thrust.thrust_id">
                        {{ thrust.thrust_name }}
                    </option>
                </select>
            </div>
        </div>

        <div class="border-b border-slate-200">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                        :class="[activeTab === tab.id ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700']"
                        class="py-4 px-1 border-b-2 font-bold text-sm transition-colors uppercase tracking-wider" style="cursor: pointer">
                    {{ tab.name }}
                </button>
            </nav>
        </div>

        <div v-if="loading" class="py-20 text-center text-slate-400">Loading your data...</div>

        <div v-else class="animate-fade-in">

            <div v-if="activeTab === 'assigned'" class="bg-white rounded-xl border border-slate-200/60 shadow-xs overflow-hidden">
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

            <div v-if="activeTab === 'members'" class="bg-white rounded-xl border border-slate-200/60 shadow-xs overflow-hidden">
                <div class="flex items-center gap-3 max-w-lg" style="padding: 20px">
                    <label class="text-sm font-medium text-slate-700 whitespace-nowrap">
                        Select a Project
                    </label>
                    <select
                        v-model="selectedProjects"
                        class="flex-1 px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-colors text-slate-600"
                        @change="fetchMembersRating"
                    >
                        <option value="">All Projects</option>
                        <option v-for="project in projects" :key="project.id" :value="project.id">
                            {{ project.title }}
                        </option>
                    </select>
                </div>
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-5">Project Title</th>
                        <th class="py-3 px-5">Evaluators Name</th>
                        <th class="py-3 px-5">Rating</th>
                        <th class="py-3 px-5 text-center">Status</th>
                        <th class="py-3 px-5 text-center"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                    <tr v-for="project in membersRating" :key="project.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-5 text-slate-900 font-bold max-w-xs sm:max-w-md truncate">
                            {{ project.title }}
                        </td>
                        <td class="py-4 px-5 text-slate-500 text-xs">
                            {{ project.name }}
                        </td>
                        <td class="py-4 px-5">
                            {{ parseFloat(project.final_calculated_score).toFixed(2) }}%
                        </td>
                        <td class="py-4 px-5 text-center">
                                    <span v-if="project.status === 'pending'" class="inline-block bg-amber-50 text-amber-800 text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded border border-amber-200/50">
                                        Pending Scorecard
                                    </span>
                            <span v-else class="inline-block bg-emerald-50 text-emerald-800 text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded border border-emerald-200/50">
                                        {{ project.status === 'draft' ? 'Draft' : 'Completed' }}
                                    </span>
                        </td>
                        <td class="py-4 px-5 text-right">
                            <span class="text-xs text-slate-400 font-bold pr-2 inline-block">
                                        Logged: {{ project.evaluated_at }}
                                    </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="activeTab === 'overall'" class="p-10 text-center border-2 border-dashed border-slate-200 rounded-xl text-slate-400">
                Overall Project Rating View (Coming Soon)
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const projects = ref([]);
const selectedProjects = ref('');
const thrustOptions = ref([]);
const selectedThrust = ref('');
const membersRating = ref([]);
const loading = ref(true);
const activeTab = ref('assigned'); // Default tab

const token = localStorage.getItem('auth_token');
const userProfile = localStorage.getItem('user_profile');
const user = userProfile ? JSON.parse(userProfile) : null;
let thrust_id = '';

let tabs = [
    { id: 'assigned', name: 'Assigned Projects for Evaluation' }
];

const dashboardData = ref({
    metrics: { total_assigned: 0, completed_evaluations: 0, pending_evaluations: 0, draft_evaluations: 0 },
    projects: []
});

const loadDashboardData = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/evaluator/dashboard-metrics', {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            },
            params: { thrust_id: selectedThrust.value }
        });

        if (response.data.status === 'success') {
            dashboardData.value = response.data.data;
            if (response.data.data.isChairperson) {
                thrust_id = selectedThrust.value
                tabs = [
                    { id: 'assigned', name: 'Assigned Projects for Evaluation' },
                    { id: 'members', name: 'Members Rating' },
                    { id: 'overall', name: 'Overall Project Rating' }
                ];

                await fetchProjects();
                await fetchMembersRating();
            }
        }
    } catch (error) {
        console.error('Failed to parse dashboard telemetry details:', error);
    } finally {
        loading.value = false;
    }
};

const fetchProjects = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/admin/projects', {
            headers: { Authorization: `Bearer ${token}` },
            params: { search: '', thrust_id: thrust_id, label: user.label }
        });
        projects.value = response.data;
    } catch (error) {
        console.error('Failed to resolve data repository context items:', error);
    } finally { loading.value = false; }
};

const fetchMembersRating = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/evaluator/members-rating', {
            headers: { Authorization: `Bearer ${token}` },
            params: { project_id: selectedProjects.value, thrust_id: thrust_id, label: user.label }
        });
        membersRating.value = response.data.data;
    } catch (error) {
        console.error('Failed to resolve data repository context items:', error);
    } finally { loading.value = false; }
};

const fetchThrusts = async () => {
    try {
        const response = await axios.get('/api/evaluator/thrusts', {
            headers: { Authorization: `Bearer ${token}` },
            params: { email: user.email }
        });
        if (response.data.status === 'success') {
            thrustOptions.value = response.data.data;
            selectedThrust.value = response.data.data[0].thrust_id;
            await loadDashboardData();
        }
    } catch (error) {
        console.error('Failed to fetch institutional thrust data:', error);
    }
};
// Add this right alongside your other function declarations (e.g., below loadDashboardData)
const handleLogout = async () => {
    try {
        const token = localStorage.getItem('auth_token');

        // Notify Laravel Sanctum to destroy the current token token record
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
        // Clear out local state arrays completely
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_profile');

        // Bounce the user back to the login screen
        await router.push('/');
    }
};

const navigateToEvaluation = (id) => {
    // Navigate directly to our evaluation scorecard input form page
    router.push({ path: '/evaluator/evaluate', query: { project_id: id } });
};

onMounted(() => {
    fetchThrusts();
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
