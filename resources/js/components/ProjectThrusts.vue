<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 rounded-xl shadow-xs border border-slate-200/60">

            <div class="flex flex-col sm:flex-row flex-1 max-w-xl gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input
                        v-model="searchQuery"
                        @input="handleSearch"
                        type="text"
                        placeholder="Search strategic focus lines..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-colors"
                    />
                </div>
            </div>

            <button
                @click="openAddModal"
                class="bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-sm py-2 px-4 rounded-lg shadow-xs transition duration-150 inline-flex items-center justify-center space-x-2 cursor-pointer whitespace-nowrap"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add Focus Thrust</span>
            </button>
        </div>

        <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl shadow-xs border border-slate-100">
            <div class="w-10 h-10 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-4"></div>
            <div class="text-slate-500 font-semibold text-sm tracking-wide">Retrieving focus lines repository context...</div>
        </div>

        <div v-else class="bg-white rounded-xl shadow-xs border border-slate-200/60 overflow-hidden">
            <div v-if="thrusts.length === 0" class="flex flex-col items-center justify-center py-20 text-slate-400">
                <svg class="w-12 h-12 mb-3 stroke-slate-300" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-sm font-medium">No strategic thrust definitions match the requested parameters.</span>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Strategic Institutional Thrust Description Name</th>
                        <th class="py-3.5 px-6">Active Linked Projects</th>
                        <th class="py-3.5 px-6 text-right">Repository Management Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <tr v-for="thrust in thrusts" :key="thrust.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900 max-w-lg">
                            <div class="truncate" :title="thrust.name">{{ thrust.name }}</div>
                        </td>
                        <td class="py-4 px-6">
                                <span :class="thrust.projects_count > 0
                                    ? 'bg-emerald-50 text-emerald-800 border-emerald-100'
                                    : 'bg-slate-100 text-slate-500 border-slate-200'"
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                >
                                    {{ thrust.projects_count ?? 0 }} Tracking Items
                                </span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                            <button
                                @click="openEditModal(thrust)"
                                class="text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                            >
                                Modify
                            </button>
                            <button
                                @click="deleteThrust(thrust)"
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
                        {{ isEditMode ? 'Modify Thrust Definition' : 'Configure New Focus Thrust' }}
                    </h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 focus:outline-hidden cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveThrust" class="p-6 space-y-4">
                    <div v-if="errorMessage" class="p-3 text-xs bg-red-50 border border-red-200 text-red-700 font-semibold rounded-lg">
                        {{ errorMessage }}
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Focus Line Core Title</label>
                        <textarea
                            v-model="form.name"
                            required
                            rows="3"
                            placeholder="Enter institutional focus title description clearly..."
                            class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-all text-slate-800 resize-none"
                        ></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer"
                        >
                            Discard
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-bold text-white bg-emerald-800 hover:bg-emerald-900 rounded-lg shadow-xs transition-colors cursor-pointer"
                        >
                            {{ isEditMode ? 'Apply Updates' : 'Commit Configuration' }}
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

// System Repository Reactive Matrices
const loading = ref(true);
const thrusts = ref([]);
const searchQuery = ref('');
const errorMessage = ref('');

// Modal Configuration Parameters
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditingId = ref(null);
const form = ref({
    name: ''
});

// Fetch Listing Records through the API utilizing standard search parameters
const fetchThrusts = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('auth_token');

        const response = await axios.get('/api/admin/thrusts', {
            headers: { Authorization: `Bearer ${token}` },
            params: { search: searchQuery.value } // 🌟 backend when() integration
        });

        thrusts.value = response.data;

    } catch (error) {
        console.error('Failed to load focus line context parameters:', error);
    } finally {
        loading.value = false;
    }
};

// Simple input trigger to evaluate query search execution loops
let searchTimeout;
const handleSearch = () => {
    clearTimeout(searchTimeout);
    // Debounce processing to prevent hitting the backend query engine excessively
    searchTimeout = setTimeout(() => {
        fetchThrusts();
    }, 300);
};

// Modal Initialization Triggers
const openAddModal = () => {
    isEditMode.value = false;
    currentEditingId.value = null;
    errorMessage.value = '';
    form.value = { name: '' };
    isModalOpen.value = true;
};

const openEditModal = (thrust) => {
    isEditMode.value = true;
    currentEditingId.value = thrust.id;
    errorMessage.value = '';
    form.value = { name: thrust.name };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

// Form Post Actions Execution Loop
const saveThrust = async () => {
    try {
        errorMessage.value = '';
        const token = localStorage.getItem('auth_token');
        const config = { headers: { Authorization: `Bearer ${token}` } };

        if (isEditMode.value) {
            await axios.put(`/api/admin/thrusts/${currentEditingId.value}`, form.value, config);
        } else {
            await axios.post('/api/admin/thrusts', form.value, config);
        }

        closeModal();
        await fetchThrusts(); // Reload list to update structures and refresh values
    } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = 'An unexpected tracking error occurred during configuration sync.';
        }
    }
};

// Deletion Management Operations Block
const deleteThrust = async (thrust) => {
    if (thrust.projects_count > 0) {
        alert(`Cannot remove "${thrust.name}" because it currently has ${thrust.projects_count} active projects assigned to it.`);
        return;
    }

    if (!confirm(`Are you completely sure you want to delete "${thrust.name}"?`)) return;

    try {
        const token = localStorage.getItem('auth_token');
        await axios.delete(`/api/admin/thrusts/${thrust.id}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        await fetchThrusts();
    } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
            alert(error.response.data.message);
        } else {
            alert('Failed to drop thrust execution parameter line safely.');
        }
    }
};

// Lifecycle Trigger Hook setup
onMounted(() => {
    fetchThrusts();
});
</script>

<style scoped>
.animate-modal-pop {
    animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
