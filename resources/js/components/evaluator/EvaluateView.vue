<template>
    <div class="max-w-350 mx-auto px-4 py-8 space-y-6">

        <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider">
            <button
                @click="goBack"
                class="text-slate-400 hover:text-emerald-800 transition-colors inline-flex items-center gap-1 cursor-pointer focus:outline-hidden"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Projects Queue
            </button>
        </div>

        <div v-if="!projectId" class="bg-red-50 border border-red-200 text-red-800 p-6 rounded-xl text-center shadow-2xs">
            <svg class="w-12 h-12 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="font-black text-sm uppercase tracking-wide">Invalid Navigation Parameters</h3>
            <p class="text-xs text-red-600 font-medium mt-1">No operational target identifier was linked to this workspace. Please re-route from your dashboard queue.</p>
        </div>

        <div v-else-if="loading" class="flex flex-col items-center justify-center py-32 bg-white rounded-xl border border-slate-200/60 shadow-xs">
            <div class="w-10 h-10 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-4"></div>
            <div class="text-slate-500 font-semibold text-sm tracking-wide">Synchronizing dynamic score card structural bounds...</div>
        </div>

        <form v-else @submit.prevent="handleSubmission(false)" class="space-y-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200/60 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-1 max-w-2xl">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[12px] font-black bg-amber-50 text-amber-800 border border-amber-200 uppercase tracking-wider">
                        {{ user.label === 'research' ? 'A • RESEARCH EVALUATION FORM' : 'Form B • DEVELOPMENT EVALUATION FORM' }}
                    </span>
                    <h2 class="text-xl font-black text-slate-800 tracking-tight leading-snug">
                        {{ projectContext?.title || 'Relational Project Record Workspace' }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 pt-1 text-xs font-bold text-slate-500">
                        <div><span class="text-slate-400 font-semibold uppercase text-[10px]">Project Leader:</span> {{ leader }}</div>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 pt-1 text-xs font-bold text-slate-500">
                        <div><span class="text-slate-400 font-semibold uppercase text-[10px]">Implementing Unit:</span> {{ projectContext?.unit_center || 'Unassigned Center' }}</div>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 pt-1 text-xs font-bold text-slate-500">
                        <div v-if="projectContext?.thrust"><span class="text-slate-400 font-semibold uppercase text-[10px]">VSU iCare Program Thrust:</span> {{ projectContext.thrust.name }}</div>
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl text-center min-w-[140px] flex-shrink-0">
                    <div class="text-[10px] text-slate-400 font-black uppercase tracking-wider">Cumulative Score</div>
                    <div class="text-3xl font-black text-emerald-800 tracking-tight mt-0.5">
                        {{ overallTotal.toFixed(2) }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold mt-0.5">out of 100%</div>
                </div>
            </div>

            <div v-for="(cat, catIndex) in categories" :key="cat.id" class="bg-white rounded-xl border border-slate-200/60 shadow-xs overflow-hidden">
                <div class="bg-slate-50 px-6 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                    <div class="flex items-baseline space-x-2">
                        <span class="text-base font-black text-emerald-800">B.{{ catIndex + 1 }}</span>
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-700">{{ cat.name }}</h3>
                    </div>
                    <span class="text-[12px] self-start sm:self-auto font-black uppercase tracking-wide bg-slate-200/70 text-slate-600 px-2.5 py-1 rounded-md">
                        Allocated Block Target: {{ cat.weight_percentage }}%
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-lg text-slate-600 border-collapse">
                        <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-6 w-3/5">Evaluation Criterion / Description Details</th>
                            <th class="py-3 px-6 text-center w-1/5">Max Weights</th>
                            <th class="py-3 px-6 text-right w-1/5">Rating Value (%)</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                        <tr v-for="crit in cat.criteria" :key="crit.id" class="hover:bg-slate-50/20 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800 text-sm">{{ crit.name }}</div>
                                <p class="text-[13px] mt-1 leading-relaxed font-normal whitespace-pre-line">
                                    {{ crit.description }}
                                </p>
                            </td>
                            <td class="py-4 px-6 text-center text-sm font-bold text-slate-500">
                                {{ crit.weight_percentage }}%
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <input
                                        type="number"
                                        step="0.5"
                                        min="0"
                                        :max="crit.weight_percentage"
                                        v-model.number="scoresMap[crit.id]"
                                        @input="restrictInputBounds(crit.id, crit.weight_percentage)"
                                        required
                                        placeholder="0.0"
                                        class="w-24 text-center font-bold text-sm bg-slate-50 border border-slate-200 focus:border-emerald-700 focus:bg-white rounded-lg p-2.5 focus:outline-hidden text-slate-800"
                                    />
                                    <span class="text-sm text-slate-400 font-bold">%</span>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50/40 font-black text-slate-700 text-xs border-t border-slate-200">
                            <td class="py-3.5 px-6 text-right uppercase tracking-wider text-slate-400 text-[10px]">Component Subtotal Metric</td>
                            <td class="py-3.5 px-6 text-center text-slate-500">{{ cat.weight_percentage }}%</td>
                            <td class="py-3.5 px-6 text-right text-emerald-800 text-sm">
                                {{ getSubtotal(cat.criteria).toFixed(2) }}%
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200/60 shadow-xs space-y-2">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-500">Qualitative Panel Remarks / Constructive Critiques</label>
                <textarea
                    v-model="comments"
                    rows="4"
                    placeholder="Provide detailed justification details regarding individual point deductions or developmental field feedback directions for this panel assignment track..."
                    class="w-full text-xs font-medium bg-slate-50 border border-slate-200 focus:border-emerald-700 focus:bg-white p-3.5 rounded-xl focus:outline-hidden text-slate-800 resize-none leading-relaxed"
                ></textarea>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between p-4 bg-white border border-slate-200 rounded-xl shadow-xs gap-4">
                <div class="text-[11px] font-medium text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-600 rounded-full animate-pulse flex-shrink-0"></span>
                    Systems are validating entries locally. Finalizing blocks further data revision passes.
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button
                        type="button"
                        @click="handleSubmission(true)"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition-colors cursor-pointer whitespace-nowrap"
                    >
                        Save Progress Draft
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer whitespace-nowrap"
                    >
                        Submit Final Sheet
                    </button>
                </div>
            </div>

        </form>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

// Target Route Parameter Identification Map
const projectId = ref(route.query.project_id || null);

// View Context Reactive Registers
const loading = ref(true);
const projectContext = ref(null);
const categories = ref([]);
const scoresMap = ref({}); // Maps tracking key pairs: criterion_id -> matching score value
const comments = ref('');
const leader = ref('');

const token = localStorage.getItem('auth_token');
const config = { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } };
const userProfile = localStorage.getItem('user_profile');
const user = userProfile ? JSON.parse(userProfile) : null;

// Dynamic Math Reducer Aggregators
const getSubtotal = (criteriaList) => {
    return criteriaList.reduce((acc, item) => {
        const val = scoresMap.value[item.id] || 0;
        return acc + (typeof val === 'number' ? val : 0);
    }, 0);
};

const overallTotal = computed(() => {
    return Object.values(scoresMap.value).reduce((acc, current) => acc + (typeof current === 'number' ? current : 0), 0);
});

// Front-End Hard Constraint Boundary Caps
const restrictInputBounds = (id, maxValue) => {
    if (scoresMap.value[id] > maxValue) {
        scoresMap.value[id] = maxValue;
    }
    if (scoresMap.value[id] < 0 || isNaN(scoresMap.value[id]) || !scoresMap.value[id]) {
        scoresMap.value[id] = 0;
    }
};

const goBack = () => {
    router.push('/evaluator/dashboard'); // Re-route to master dashboard context
};

// Data Integration Handlers
const loadFormParameters = async () => {
    if (!projectId.value) return;

    try {
        loading.value = true;

        // 1. Resolve localized structural context parameters from endpoints
        const structuralFormUrl = `/api/evaluator/projects/${projectId.value}/evaluation-form`;
        const response = await axios.get(structuralFormUrl, config);

        if (response.data.status === 'success') {
            categories.value = response.data.categories;
            comments.value = response.data.existing_evaluation?.comments || '';
            leader.value = response.data.projectLeader;

            // Map out criteria matrix entries to prevent missing indexes inside v-model maps
            const preExistingAnswers = response.data.existing_scores || {};
            categories.value.forEach(cat => {
                cat.criteria.forEach(crit => {
                    scoresMap.value[crit.id] = preExistingAnswers[crit.id] !== undefined
                        ? parseFloat(preExistingAnswers[crit.id])
                        : 0;
                });
            });
        }

        // 2. Fallback check to capture context names (title/units) from general list endpoint if needed
        const fallbackProjectUrl = `/api/evaluator/projects`;
        const fallbackRes = await axios.get(fallbackProjectUrl, config);
        const datasets = fallbackRes.data.data || fallbackRes.data;
        projectContext.value = datasets.find(p => p.id == projectId.value) || null;

    } catch (err) {
        console.error('Failed reading operational database layout matrix details:', err);
    } finally {
        loading.value = false;
    }
};

const handleSubmission = async (isDraft = false) => {
    if (!isDraft && !confirm('Are you absolutely sure you want to finalize this execution score? This operation commits data parameters permanently and closes your edit window access permissions.')) return;

    try {
        // Flat transform local dictionary entries into a standard API structure
        const scoresPayloadArray = Object.keys(scoresMap.value).map(key => ({
            criterion_id: parseInt(key),
            rating: scoresMap.value[key] || 0
        }));

        const submitUrl = `/api/evaluator/projects/${projectId.value}/submit-evaluation`;
        const payload = {
            scores: scoresPayloadArray,
            comments: comments.value,
            is_draft: isDraft
        };

        const response = await axios.post(submitUrl, payload, config);

        if (response.data.status === 'success') {
            alert(response.data.message);
            if (!isDraft) {
                goBack(); // Redirect back to active tracking grids upon permanent lock
            }
        }
    } catch (err) {
        alert(err.response?.data?.message || 'Data integrity violation error detected while syncing payloads.');
    }
};

onMounted(() => {
    loadFormParameters();
});
</script>
