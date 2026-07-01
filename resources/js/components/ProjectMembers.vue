<template>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-4 xl:col-span-3">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white mb-5 p-4 rounded-xl shadow-xs border border-slate-200/60">
                <div class="relative flex-1 max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                    <input
                        v-model="searchProject"
                        @input="handleSearchProject"
                        type="text"
                        placeholder="Search project here..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-colors"
                    />
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/60 p-4 sticky top-6">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-4 px-2">Institutional Projects</h3>

                <div class="space-y-1 max-h-[70vh] overflow-y-auto pr-1 custom-scrollbar">
                    <button
                        v-for="project in projectOptions"
                        :key="project.id"
                        @click="selectProject(project)"
                        :class="[
                            'w-full text-left px-4 py-3 rounded-lg text-sm transition-colors flex justify-between items-center group',
                            selectedProjectId === project.id
                                ? 'bg-emerald-50 text-emerald-900 border border-emerald-200 font-bold'
                                : 'hover:bg-slate-50 text-slate-600 font-semibold border border-transparent'
                        ]"
                        style="cursor: pointer"
                    >
                        <span class="truncate pr-2">{{ project.title }}</span>
                        <svg
                            v-if="selectedProjectId === project.id"
                            class="w-4 h-4 text-emerald-600 shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div v-if="projectOptions.length === 0" class="text-center py-8 text-slate-400 text-xs font-medium">
                        No projects available.
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 xl:col-span-9">

            <div v-if="!selectedProjectId" class="h-96 flex flex-col items-center justify-center bg-white rounded-xl shadow-xs border border-dashed border-slate-300 text-slate-400">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 stroke-slate-300" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-600 mb-1">No Project Selected</h3>
                <p class="text-sm font-medium">Select a project from the sidebar to manage its proponents.</p>
            </div>

            <div v-else class="space-y-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 rounded-xl shadow-xs border border-slate-200/60">
                    <div class="relative flex-1 max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input
                            v-model="searchQuery"
                            @input="handleSearch"
                            type="text"
                            placeholder="Search proponents within this project..."
                            class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white transition-colors"
                        />
                    </div>

                    <button
                        @click="openAddModal"
                        class="bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-sm py-2 px-4 rounded-lg shadow-xs transition duration-150 inline-flex items-center justify-center space-x-2 cursor-pointer whitespace-nowrap"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add Proponent</span>
                    </button>
                </div>

                <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl shadow-xs border border-slate-100">
                    <div class="w-10 h-10 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-4"></div>
                    <div class="text-slate-500 font-semibold text-sm tracking-wide">Retrieving team roster...</div>
                </div>

                <div v-else class="bg-white rounded-xl shadow-xs border border-slate-200/60 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h2 class="font-black text-slate-800">{{ activeProjectTitle }}</h2>
                        <p class="text-xs text-slate-500 font-medium">Assigned Proponents Roster</p>
                    </div>

                    <div v-if="members.length === 0" class="flex flex-col items-center justify-center py-20 text-slate-400">
                        <svg class="w-12 h-12 mb-3 stroke-slate-300" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">No proponents assigned to this project yet.</span>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-6">Proponent Profile</th>
                                <th class="py-3.5 px-6">Designation</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            <tr v-for="member in members" :key="member.id" class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ member.name }}</div>
                                    <div class="text-xs text-slate-400 font-medium">{{ member.email }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span :class="member.role === 'Project leader'
                                        ? 'bg-amber-50 text-amber-800 border-amber-100'
                                        : 'bg-emerald-50 text-emerald-800 border-emerald-100'"
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border capitalize"
                                    >
                                        {{ member.role }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                    <button
                                        @click="openEditModal(member)"
                                        class="text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                                    >
                                        Modify
                                    </button>
                                    <button
                                        @click="deleteMember(member)"
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-lg w-full flex flex-col overflow-hidden animate-modal-pop">

                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-base font-black text-slate-800">
                        {{ isEditMode ? 'Modify Proponent Details' : 'Register New Proponent' }}
                    </h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 focus:outline-hidden cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveMember" class="p-6 space-y-4">
                    <div v-if="errorMessage" class="p-3 text-xs bg-red-50 border border-red-200 text-red-700 font-semibold rounded-lg">
                        {{ errorMessage }}
                    </div>

                    <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-xs text-emerald-800 font-bold uppercase tracking-wider">Target Project</p>
                            <p class="text-sm font-medium text-emerald-900 leading-tight">{{ activeProjectTitle }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Full Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g. Dr. Jane Doe"
                            class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white text-slate-800"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Address</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="username@institution.edu"
                            class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white text-slate-800"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Project Designation</label>
                        <select
                            v-model="form.role"
                            required
                            class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-600 focus:bg-white text-slate-800"
                        >
                            <option value="Program leader">Program leader</option>
                            <option value="Project member">Project member</option>
                            <option value="Project leader">Project leader</option>
                            <option value="Component leader">Component leader</option>
                            <option value="Study leader">Study leader</option>
                            <option value="Project staff">Project staff</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">
                            Discard
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-emerald-800 hover:bg-emerald-900 rounded-lg shadow-xs transition-colors cursor-pointer">
                            {{ isEditMode ? 'Apply Updates' : 'Commit Configuration' }}
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

// System Repository Reactive Matrices
const loading = ref(false);
const members = ref([]);
const projectOptions = ref([]);
const searchQuery = ref('');
const searchProject = ref('');
const selectedProjectId = ref(null);
const errorMessage = ref('');

// Computed Property to get the active project's title
const activeProjectTitle = computed(() => {
    const project = projectOptions.value.find(p => p.id === selectedProjectId.value);
    return project ? project.title : '';
});

// Modal Flags
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditingId = ref(null);
const form = ref({
    project_id: '',
    name: '',
    email: '',
    role: 'Project member'
});
const token = localStorage.getItem('auth_token');
const user = JSON.parse(localStorage.getItem('user_profile')) || null;

// Selection Logic
const selectProject = (project) => {
    selectedProjectId.value = project.id;
    searchQuery.value = ''; // Reset search on project switch
    fetchMembers();
};

// Fetch Member Roster Dataset
const fetchMembers = async () => {
    if (!selectedProjectId.value) return;

    try {
        loading.value = true;
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/admin/members', {
            headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
            params: {
                search: searchQuery.value,
                project_id: selectedProjectId.value
            }
        });

        members.value = response.data.data ? response.data.data : response.data;
    } catch (error) {
        console.error('Failed to resolve database user arrays:', error);
    } finally {
        loading.value = false;
    }
};

// Fetch Project Selection Option Lists
const fetchProjectsList = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/admin/projects', {
            headers: { Authorization: `Bearer ${token}` },
            params: { label: user.label, search: searchProject.value }
        });
        projectOptions.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to gather index metadata listings:', error);
    }
};

// 1. Separate your timeout variables to prevent collisions
let projectSearchTimeout;
let memberSearchTimeout;

// 2. Project Search Handler (Sidebar)
const handleSearchProject = () => {
    clearTimeout(projectSearchTimeout);

    // Wait 300ms, then fetch the updated PROJECTS list
    projectSearchTimeout = setTimeout(() => {
        fetchProjectsList();
    }, 300);
};

// 3. Proponent/Member Search Handler (Main Content Area)
const handleSearch = () => {
    clearTimeout(memberSearchTimeout);

    // Wait 300ms, then fetch the updated MEMBERS list
    memberSearchTimeout = setTimeout(() => {
        fetchMembers();
    }, 300);
};

// Modal Controls
const openAddModal = () => {
    isEditMode.value = false;
    currentEditingId.value = null;
    errorMessage.value = '';
    // Auto-fill context using the selected project ID
    form.value = { project_id: selectedProjectId.value, name: '', email: '', role: 'Project member' };
    isModalOpen.value = true;
};

const openEditModal = (member) => {
    isEditMode.value = true;
    currentEditingId.value = member.id;
    errorMessage.value = '';
    form.value = {
        project_id: member.project_id,
        name: member.name,
        email: member.email,
        role: member.role
    };
    isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

// Form Write Actions Loop
const saveMember = async () => {
    try {
        errorMessage.value = '';
        const token = localStorage.getItem('auth_token');
        const config = { headers: { Authorization: `Bearer ${token}` } };

        if (isEditMode.value) {
            await axios.put(`/api/admin/members/${currentEditingId.value}`, form.value, config);
        } else {
            await axios.post('/api/admin/members', form.value, config);
        }

        closeModal();
        await fetchMembers();
    } catch (error) {
        if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = 'An unexpected server syncing layout error occurred.';
        }
    }
};

// Delete Operations
const deleteMember = async (member) => {
    if (!confirm(`Are you sure you want to remove "${member.name}" from this project profile?`)) return;

    try {
        const token = localStorage.getItem('auth_token');
        await axios.delete(`/api/admin/members/${member.id}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        await fetchMembers();
    } catch (error) {
        alert('Failed to drop member listing context properties safely.');
    }
};

onMounted(() => {
    fetchProjectsList();
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
}
.animate-modal-pop {
    animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
