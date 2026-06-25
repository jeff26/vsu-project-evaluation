<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 rounded-xl shadow-xs border border-slate-200/60">

            <div class="flex flex-col sm:flex-row flex-1 max-w-2xl gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search project title..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-colors"
                    />
                </div>

                <div class="w-full sm:w-56">
                    <select
                        v-model="selectedThrust"
                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-colors text-slate-600"
                        @change="fetchProjects"
                    >
                        <option value="">All Institutional Thrusts</option>
                        <option v-for="thrust in thrustOptions" :key="thrust.id" :value="thrust.id">
                            {{ thrust.name }}
                        </option>
                    </select>
                </div>
            </div>

            <button
                @click="openAddModal"
                class="bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-sm py-2 px-4 rounded-lg shadow-xs transition duration-150 inline-flex items-center justify-center space-x-2 cursor-pointer whitespace-nowrap"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Register Project</span>
            </button>
        </div>

        <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl shadow-xs border border-slate-100">
            <div class="w-10 h-10 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-4"></div>
            <div class="text-slate-500 font-semibold text-sm tracking-wide">Retrieving institutional tracks...</div>
        </div>

        <div v-else class="bg-white rounded-xl shadow-xs border border-slate-200/60 overflow-hidden">
            <div v-if="projects.length === 0" class="flex flex-col items-center justify-center py-20 text-slate-400">
                <svg class="w-12 h-12 mb-3 stroke-slate-300" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-sm font-medium">No projects match the specified search parameters.</span>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Project Context Details</th>
                        <th class="py-3.5 px-6">VSU iCare Program thrust</th>
                        <th class="py-3.5 px-6">Implementing Unit</th>
                        <th class="py-3.5 px-6 text-right">System Management Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <tr v-for="project in projects" :key="project.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 max-w-md vertical-align-top">
                            <div class="font-bold text-slate-900 truncate" :title="project.title">{{ project.title }}</div>

                            <div class="mt-1.5 flex items-center">
                                <button
                                    v-if="project.evaluators && project.evaluators.length > 0"
                                    @click="toggleEvaluators(project.id)"
                                    class="inline-flex items-center text-[11px] font-bold text-slate-500 hover:text-emerald-800 transition-colors cursor-pointer focus:outline-hidden space-x-1"
                                >
                                    <svg
                                        class="w-3 h-3 transform transition-transform duration-200 text-slate-400"
                                        :class="{ 'rotate-90 text-emerald-700': isExpanded(project.id) }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <span>{{ isExpanded(project.id) ? 'Hide Committee List' : `View Committee (${project.evaluators.length})` }}</span>
                                </button>
                                <span v-else class="text-[10px] text-slate-400 font-medium">No evaluators assigned</span>
                            </div>

                            <div
                                v-if="project.evaluators && project.evaluators.length > 0 && isExpanded(project.id)"
                                class="flex flex-wrap gap-1.5 mt-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60 animate-fade-in"
                            >
                                <span
                                    v-for="ev in project.evaluators"
                                    :key="ev.id"
                                    :title="ev.email"
                                    class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-800 border border-emerald-100 px-2 py-0.5 rounded-md shadow-2xs"
                                >
                                    <svg class="w-2.5 h-2.5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ ev.name }}
                                </span>
                            </div>
                        </td>

                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-100">
                                {{ project.thrust?.name || 'No Thrust' }}
                            </span>
                        </td>

                        <td class="py-4 px-6 text-slate-500 font-medium">
                            {{ project.unit_center || 'Unassigned Center' }}
                        </td>

                        <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                            <button
                                @click="openEvaluatorModal(project)"
                                class="text-amber-700 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                            >
                                Panel Setup
                            </button>
                            <button
                                @click="openEditModal(project)"
                                class="text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                            >
                                Modify
                            </button>
                            <button
                                @click="deleteProject(project.id)"
                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-lg w-full flex flex-col overflow-hidden animate-modal-pop">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-base font-black text-slate-800">
                        {{ isEditMode ? 'Modify Project Records' : 'Register New Project Workspace' }}
                    </h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 focus:outline-hidden cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveProject" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Project Scope / Title</label>
                        <textarea
                            v-model="form.title"
                            required
                            rows="3"
                            placeholder="Provide exact administrative project title naming details..."
                            class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-all text-slate-800 resize-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">VSU iCare Program thrust</label>
                        <select
                            v-model="form.project_thrusts_id"
                            required
                            class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-all text-slate-800"
                        >
                            <option value="" disabled selected>Select assigned institutional thrust area</option>
                            <option v-for="thrust in thrustOptions" :key="thrust.id" :value="thrust.id">
                                {{ thrust.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Assigned Operations Center Unit</label>
                        <input
                            v-model="form.unit_center"
                            type="text"
                            placeholder="e.g., Department of Agronomy, OvRE, etc."
                            class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-all text-slate-800"
                        />
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-semibold text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">Discard</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-emerald-800 hover:bg-emerald-900 rounded-lg shadow-xs transition-colors cursor-pointer">
                            {{ isEditMode ? 'Apply Updates' : 'Commit Registration' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="isEvaluatorModalOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full flex flex-col overflow-hidden animate-modal-pop">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Configure Evaluation Committee</h3>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Select peer experts cleared to grade this workflow.</p>
                    </div>
                    <button @click="isEvaluatorModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">&times;</button>
                </div>

                <form @submit.prevent="submitEvaluatorAssignments" class="p-6 space-y-4">
                    <div class="max-h-60 overflow-y-auto space-y-2 pr-1">
                        <div v-if="evaluatorsOptions.length === 0" class="text-xs text-slate-400 py-4 text-center">
                            No evaluators found in the system user registries.
                        </div>

                        <label
                            v-for="ev in evaluatorsOptions"
                            :key="ev.id"
                            class="flex items-center space-x-3 p-2.5 border border-slate-100 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer select-none text-sm font-medium text-slate-700"
                        >
                            <input
                                type="checkbox"
                                :value="ev.id"
                                v-model="selectedEvaluatorIds"
                                class="w-4 h-4 text-emerald-700 border-slate-300 rounded focus:ring-emerald-600"
                            />
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 text-xs">{{ ev.name }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">{{ ev.email }}</span>
                            </div>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                        <button type="button" @click="isEvaluatorModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-900 rounded-lg shadow-xs transition-colors cursor-pointer">
                            Save Assessor List
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// System State Repositories
const loading = ref(true);
const projects = ref([]);
const searchQuery = ref('');
const selectedThrust = ref('');
const thrustOptions = ref([]);

// Project Modal Configurations
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditingId = ref(null);
const form = ref({ title: '', project_thrusts_id: '', unit_center: '' });

// Evaluator Committee Management Reactive Elements
const isEvaluatorModalOpen = ref(false);
const evaluatorsOptions = ref([]);
const selectedEvaluatorIds = ref([]);
const targetProjectForEvaluator = ref(null);

// 🌟 ADDED: Reactive tracking array for expanded row IDs
const expandedProjectIds = ref([]);

const token = localStorage.getItem('auth_token');
const config = { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } };

// 🌟 ADDED: Accordion view controllers
const toggleEvaluators = (projectId) => {
    if (expandedProjectIds.value.includes(projectId)) {
        expandedProjectIds.value = expandedProjectIds.value.filter(id => id !== projectId);
    } else {
        expandedProjectIds.value.push(projectId);
    }
};

const isExpanded = (projectId) => expandedProjectIds.value.includes(projectId);

// Trigger Evaluator Overlay Configuration Map
const openEvaluatorModal = async (project) => {
    targetProjectForEvaluator.value = project;
    selectedEvaluatorIds.value = project.evaluators ? project.evaluators.map(e => e.id) : [];
    isEvaluatorModalOpen.value = true;

    if (evaluatorsOptions.value.length === 0) {
        await fetchSystemEvaluators();
    }
};

// Pull all system accounts where role === 'evaluator'
const fetchSystemEvaluators = async () => {
    try {
        const response = await axios.get('/api/admin/users', config);
        if (response.data.status === 'success') {
            evaluatorsOptions.value = response.data.data.filter(u => u.role === 'evaluator');
        }
    } catch (err) {
        console.error('Failed retrieving administrative identity catalogs:', err);
    }
};

// Process assignment synchronization request payloads
const submitEvaluatorAssignments = async () => {
    try {
        const url = `/api/admin/projects/${targetProjectForEvaluator.value.id}/assign-evaluators`;
        const response = await axios.post(url, { evaluator_ids: selectedEvaluatorIds.value }, config);

        if (response.data.status === 'success') {
            alert('Evaluation panel profile parameters updated successfully.');
            isEvaluatorModalOpen.value = false;

            // 🌟 UX Optimization: Auto-expand the row to showcase updates upon submission
            if (!expandedProjectIds.value.includes(targetProjectForEvaluator.value.id)) {
                expandedProjectIds.value.push(targetProjectForEvaluator.value.id);
            }

            await fetchProjects();
        }
    } catch (err) {
        alert(err.response?.data?.message || 'Failed connecting synchronization protocols.');
    }
};

// Core Project CRUD Hooks
const openAddModal = () => {
    isEditMode.value = false;
    currentEditingId.value = null;
    form.value = { title: '', project_thrusts_id: '', unit_center: '' };
    isModalOpen.value = true;
};

const openEditModal = (project) => {
    isEditMode.value = true;
    currentEditingId.value = project.id;
    form.value = {
        title: project.title,
        project_thrusts_id: project.project_thrusts_id,
        unit_center: project.unit_center
    };
    isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

const fetchThrusts = async () => {
    try {
        const response = await axios.get('/api/admin/projects/thrusts', config);
        thrustOptions.value = response.data;
    } catch (error) {
        console.error('Failed to fetch institutional thrust data:', error);
    }
};

const fetchProjects = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/admin/projects', {
            headers: { Authorization: `Bearer ${token}` },
            params: { search: searchQuery.value, thrust_id: selectedThrust.value }
        });
        projects.value = response.data;
    } catch (error) {
        console.error('Failed to resolve data repository context items:', error);
    } finally { loading.value = false; }
};

const saveProject = async () => {
    try {
        if (isEditMode.value) {
            await axios.put(`/api/admin/projects/${currentEditingId.value}`, form.value, config);
        } else {
            await axios.post('/api/admin/projects', form.value, config);
        }
        await fetchProjects();
        closeModal();
    } catch (error) {
        console.error('Failed to save project records:', error);
    }
};

const deleteProject = async (id) => {
    if (!confirm('Are you sure you want to completely remove this project record?')) return;
    try {
        await axios.delete(`/api/admin/projects/${id}`, config);
        projects.value = projects.value.filter(p => p.id !== id);
    } catch (error) {
        console.error('Failed to delete project target:', error);
    }
};

onMounted(() => {
    fetchThrusts();
    fetchProjects();
});
</script>

<style scoped>
.animate-modal-pop { animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-2px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
