<template>
    <div class="space-y-6">
        <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl shadow-xs border border-slate-100">
            <div class="w-10 h-10 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-4"></div>
            <div class="text-slate-500 font-semibold text-sm tracking-wide">Assembling dashboard aggregates...</div>
        </div>

        <div v-else class="space-y-6 animate-fade-in">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-emerald-700"></div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Evaluators</div>
                    <div class="mt-2 text-4xl font-black text-slate-800 tracking-tight">{{ stats.total_users }}</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-amber-500"></div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Projects</div>
                    <div class="mt-2 text-4xl font-black text-slate-800 tracking-tight">{{ stats.total_projects }}</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-blue-500"></div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Completion Rate</div>
                    <div class="mt-2 text-4xl font-black text-slate-800 tracking-tight">{{ stats.completion_rate }}%</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 relative overflow-hidden group">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-purple-500"></div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">System Avg Score</div>
                    <div class="mt-2 text-4xl font-black text-slate-800 tracking-tight">{{ stats.avg_score }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60">
                    <h3 class="text-base font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">System Health</h3>
                    <div class="relative h-64 w-full">
                        <Doughnut :data="statusChartData" :options="chartOptions" />
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60">
                    <h3 class="text-base font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Quality Distribution</h3>
                    <div class="relative h-64 w-full">
                        <Bar :data="scoreChartData" :options="chartOptions" />
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60">
                    <h3 class="text-base font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Rubric Performance</h3>
                    <div class="relative h-64 w-full">
                        <Radar :data="radarChartData" :options="radarOptions" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 flex flex-col">
                    <h3 class="text-base font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Evaluator Workload</h3>
                    <ul class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto pr-1">
                        <li v-for="user in stats.evaluator_workload" :key="user.id" class="py-3 flex justify-between items-center">
                            <span class="text-slate-700 font-semibold text-sm">{{ user.name }}</span>
                            <div class="flex space-x-2">
                                <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded" title="Assigned">Assigned: {{ user.assigned_count }}</span>
                                <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded font-bold" title="Completed">Done: {{ user.completed_count }}</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200/60 flex flex-col">
                    <h3 class="text-base font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Recent Projects</h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-100 flex-1">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase">
                            <tr>
                                <th class="py-3 px-4">Title</th>
                                <th class="py-3 px-4 text-right">Unit</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            <tr v-for="project in stats.recent_projects" :key="project.id">
                                <td class="py-3 px-4 font-semibold text-slate-900 truncate max-w-[200px]">{{ project.title }}</td>
                                <td class="py-3 px-4 text-right font-medium text-slate-500">{{ project.unit_center || 'Unassigned' }}</td>
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
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, RadialLinearScale, PointElement, LineElement } from 'chart.js';
import { Bar, Doughnut, Radar } from 'vue-chartjs';

// Register Chart.js components
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, RadialLinearScale, PointElement, LineElement);

const loading = ref(true);
const stats = ref({
    total_users: 0,
    total_projects: 0,
    completion_rate: 0,
    avg_score: "0.00",
    status_data: { completed: 0, pending: 0, draft: 0 },
    score_distribution: [],
    radar_data: [],
    evaluator_workload: [],
    recent_projects: []
});

// Chart.js Configurations
const chartOptions = { responsive: true, maintainAspectRatio: false };
const radarOptions = { responsive: true, maintainAspectRatio: false, scales: { r: { min: 0, max: 5 } } };

// Bind Dynamic Backend Data to Charts
const statusChartData = computed(() => ({
    labels: ['Completed', 'Pending', 'Draft'],
    datasets: [{
        backgroundColor: ['#047857', '#F59E0B', '#64748B'], // Emerald, Amber, Slate
        data: [stats.value.status_data.completed, stats.value.status_data.pending, stats.value.status_data.draft]
    }]
}));

const scoreChartData = computed(() => ({
    labels: ['1-2', '2-3', '3-4', '4-5'],
    datasets: [{
        label: 'Projects per Bracket',
        backgroundColor: '#3B82F6', // Blue
        data: stats.value.score_distribution
    }]
}));

const radarChartData = computed(() => ({
    labels: stats.value.radar_data.map(item => item.category),
    datasets: [{
        label: 'Average Score',
        backgroundColor: 'rgba(4, 120, 87, 0.2)',
        borderColor: '#047857',
        pointBackgroundColor: '#047857',
        data: stats.value.radar_data.map(item => item.score)
    }]
}));

const fetchDashboardData = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const userProfile = localStorage.getItem('user_profile');
        const user = userProfile ? JSON.parse(userProfile) : null;
        const response = await axios.get('/api/admin/dashboard', {
            headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
            params: {
                label: user.label
            }
        });
        if (response.data.status === 'success') {
            stats.value = response.data.data;
        }
    } catch (error) {
        console.error('Failed to resolve system telemetry:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => fetchDashboardData());
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.35s ease-out forwards; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
