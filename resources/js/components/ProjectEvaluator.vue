<template>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <div class="md:col-span-4 lg:col-span-3">
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/60 p-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-4 px-2">Program Thrusts</h3>
                <div class="space-y-1">
                    <button
                        v-for="thrust in thrustOptions"
                        :key="thrust.id"
                        @click="selectThrust(thrust)"
                        :class="[
                            'w-full text-left px-4 py-3 rounded-lg text-sm font-semibold transition-colors flex justify-between items-center',
                            selectedThrustId === thrust.id
                                ? 'bg-emerald-50 text-emerald-800 border border-emerald-100'
                                : 'hover:bg-slate-50 text-slate-600'
                        ]"
                    >
                        {{ thrust.name }}
                        <span v-if="selectedThrustId === thrust.id" class="text-emerald-500">→</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:col-span-8 lg:col-span-9">
            <div v-if="!selectedThrustId" class="h-64 flex flex-col items-center justify-center bg-white rounded-xl shadow-xs border border-dashed border-slate-300 text-slate-400">
                <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                <p class="font-medium">Select a Program Thrust to manage evaluators</p>
            </div>

            <div v-else class="space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-800">
                        Evaluators: {{ activeThrustName }}
                    </h2>
                    <button
                        @click="openAddModal"
                        class="bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-sm py-2 px-4 rounded-lg shadow-xs transition duration-150 inline-flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Add Evaluator</span>
                    </button>
                </div>

                <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl shadow-xs border border-slate-200/60">
                    <div class="w-8 h-8 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-4"></div>
                </div>

                <div v-else class="bg-white rounded-xl shadow-xs border border-slate-200/60 overflow-hidden">
                    <div v-if="evaluators.length === 0" class="py-20 text-center text-slate-400">
                        No evaluators found for this thrust.
                    </div>
                    <table v-else class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6 font-bold uppercase text-xs">Profile</th>
                            <th class="py-3 px-6 font-bold uppercase text-xs">Role</th>
                            <th class="py-3 px-6 font-bold uppercase text-xs text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        <tr v-for="evaluator in evaluators" :key="evaluator.id" class="hover:bg-slate-50">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ evaluator.name }}</div>
                                <div class="text-xs text-slate-400">{{ evaluator.email }}</div>
                            </td>
                            <td class="py-4 px-6">
                                    <span :class="evaluator.role === 'Chairperson' ? 'bg-amber-50 text-amber-800' : 'bg-emerald-50 text-emerald-800'" class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize">
                                        {{ evaluator.role }}
                                    </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button @click="openEditModal(evaluator)" class="text-emerald-700 hover:text-emerald-900 font-bold px-2 py-1">Modify</button>
                                <button @click="deleteEvaluator(evaluator)" class="text-red-600 hover:text-red-900 font-bold px-2 py-1">Remove</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-base font-black text-slate-800">{{ isEditMode ? 'Modify Evaluator' : 'New Evaluator' }}</h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 cursor-pointer">×</button>
                </div>
                <form @submit.prevent="saveEvaluator" class="p-6 space-y-4">
                    <div v-if="errorMessage" class="p-3 text-xs bg-red-50 border border-red-200 text-red-700 font-semibold rounded-lg">
                        {{ errorMessage }}
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Full Name</label>
                        <input v-model="form.name" type="text" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg" placeholder="ex... Jeffrey tomas">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Email</label>
                        <input v-model="form.email" type="email" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg" placeholder="(ex. example@vsu.edu.ph)">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Role</label>
                        <select v-model="form.role" required class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg">
                            <option value="Chairperson">Chairperson</option>
                            <option value="Member">Member</option>
                        </select>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-semibold text-slate-500">Discard</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-emerald-800 hover:bg-emerald-900 rounded-lg">
                            {{ isEditMode ? 'Apply Updates' : 'Save Evaluator' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const loading = ref(false);
const evaluators = ref([]);
const thrustOptions = ref([]);
const selectedThrustId = ref(null);
const errorMessage = ref('');

// Modal State
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditingId = ref(null);
const userProfile = localStorage.getItem('user_profile');
const user = userProfile ? JSON.parse(userProfile) : null;

const form = ref({ project_thrusts_id: '', name: '', email: '', role: 'Member', label: '' });

const activeThrustName = computed(() => {
    const thrust = thrustOptions.value.find(t => t.id === selectedThrustId.value);
    return thrust ? thrust.name : '';
});

// Logic
const fetchThrustsList = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/admin/thrusts', {
            headers: { Authorization: `Bearer ${token}` },
            params: { label: user.label }
        });
        thrustOptions.value = response.data.data || response.data;
        if (response.data.length > 0) {
            selectThrust(response.data[0])
        }
    } catch (error) { console.error(error); }
};

const selectThrust = (thrust) => {
    selectedThrustId.value = thrust.id;
    fetchEvaluators();
};

const fetchEvaluators = async () => {
    if (!selectedThrustId.value) return;
    try {
        loading.value = true;
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/admin/evaluators', {
            headers: { Authorization: `Bearer ${token}` },
            params: { thrust_id: selectedThrustId.value }
        });
        evaluators.value = response.data.data || response.data;
    } finally { loading.value = false; }
};

const openAddModal = () => {
    isEditMode.value = false;
    form.value = { project_thrusts_id: selectedThrustId.value, name: '', email: '', role: 'Member', label: user.label };
    errorMessage.value = '';
    isModalOpen.value = true;
};

const openEditModal = (evaluator) => {
    isEditMode.value = true;
    currentEditingId.value = evaluator.id;
    errorMessage.value = '';
    form.value = { ...evaluator, project_thrusts_id: evaluator.project_thrusts_id };
    isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

const saveEvaluator = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const config = { headers: { Authorization: `Bearer ${token}` } };
        if (isEditMode.value) {
            await axios.put(`/api/admin/evaluators/${currentEditingId.value}`, form.value, config);
        } else {
            await axios.post('/api/admin/evaluators', form.value, config);
        }
        isModalOpen.value = false;
        await fetchEvaluators();
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Action failed.';
    }
};

const deleteEvaluator = async (evaluator) => {
    if (!confirm(`Are you sure you want to remove ${evaluator.name}?`)) return;
    const token = localStorage.getItem('auth_token');
    await axios.delete(`/api/admin/evaluators/${evaluator.id}`, { headers: { Authorization: `Bearer ${token}` } });
    await fetchEvaluators();
};

onMounted(() => fetchThrustsList());
</script>
