<template>
    <div class="space-y-6 max-w-6xl mx-auto pb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-xl border border-slate-200/60 shadow-xs">
            <div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-emerald-700 rounded-sm inline-block"></span>
                    Account & Role Management
                </h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Register system profiles, adjust authentication vectors, and provision access keys.</p>
            </div>
            <button
                @click="openCreateModal"
                class="bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs py-2.5 px-5 rounded-xl transition-colors cursor-pointer shadow-2xs"
            >
                + Register New Account
            </button>
        </div>

        <div v-if="feedback" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold rounded-xl text-xs">
            {{ feedback }}
        </div>

        <div v-if="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl border border-slate-100 shadow-xs">
            <div class="w-8 h-8 border-4 border-emerald-700 border-t-amber-400 rounded-full animate-spin mb-3"></div>
            <div class="text-slate-400 font-semibold text-xs">Parsing identity database maps...</div>
        </div>

        <div v-else class="bg-white rounded-xl border border-slate-200/60 shadow-xs overflow-hidden animate-fade-in">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-5">User Profile Details</th>
                        <th class="py-3.5 px-5">Email Address Endpoint</th>
                        <th class="py-3.5 px-5 text-center">Assigned Role Authority</th>
                        <th class="py-3.5 px-5 text-right">System Configuration</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <tr v-for="u in users" :key="u.id" class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-5 font-bold text-slate-900">{{ u.name }}</td>
                        <td class="py-3.5 px-5 text-xs text-slate-500">{{ u.email }}</td>
                        <td class="py-3.5 px-5 text-center">
                                <span :class="u.role === 'admin' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200'" class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded border">
                                    {{ u.role }}
                                </span>
                        </td>
                        <td class="py-3.5 px-5 text-right space-x-2 whitespace-nowrap">
                            <button @click="openEditModal(u)" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                Modify
                            </button>
                            <button @click="deleteUser(u.id)" class="text-xs bg-red-50 hover:bg-red-600 text-red-700 hover:text-white px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                Drop
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden animate-fade-in">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-800">
                        {{ isEditMode ? 'Modify Profile Matrix' : 'Register New Account Model' }}
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <form @submit.prevent="saveUser" class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Full Name</label>
                        <input v-model="form.name" type="text" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-700" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                        <input v-model="form.email" type="email" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-700" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Access Password <span v-if="isEditMode" class="text-[10px] text-slate-400 lowercase">(Leave blank to keep current)</span>
                        </label>
                        <input v-model="form.password" type="password" :required="!isEditMode" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-700" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Role Authority Clearance</label>
                        <select v-model="form.role" class="w-full px-3 py-2 text-sm border border-slate-200 bg-white rounded-lg focus:outline-hidden focus:border-emerald-700 font-medium">
                            <option value="evaluator">Evaluator</option>
                            <option value="admin">Admin (System Infrastructure Manager)</option>
                        </select>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-900 rounded-lg transition-colors cursor-pointer shadow-xs">
                            Commit Specifications
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

const users = ref([]);
const loading = ref(true);
const showModal = ref(false);
const isEditMode = ref(false);
const targetUserId = ref(null);
const feedback = ref('');

const form = ref({ name: '', email: '', password: '', role: 'evaluator' });
const token = localStorage.getItem('auth_token');

const fetchUsers = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/admin/users', { headers: { Authorization: `Bearer ${token}` } });
        if (response.data.status === 'success') users.value = response.data.data;
    } catch (err) {
        console.error('Failed processing index arrays:', err);
    } finally { loading.value = false; }
};

const openCreateModal = () => {
    isEditMode.value = false;
    form.value = { name: '', email: '', password: '', role: 'evaluator' };
    showModal.value = true;
};

const openEditModal = (user) => {
    isEditMode.value = true;
    targetUserId.value = user.id;
    form.value = { name: user.name, email: user.email, password: '', role: user.role };
    showModal.value = true;
};

const saveUser = async () => {
    try {
        let response;
        const config = { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } };

        if (isEditMode.value) {
            response = await axios.put(`/api/admin/users/${targetUserId.value}`, form.value, config);
        } else {
            response = await axios.post('/api/admin/users', form.value, config);
        }

        if (response.data.status === 'success') {
            feedback.value = response.data.message;
            showModal.value = false;
            await fetchUsers();
            setTimeout(() => feedback.value = '', 4000);
        }
    } catch (err) {
        alert(err.response?.data?.message || 'Data integrity fault discovered processing payloads.');
    }
};

const deleteUser = async (id) => {
    if (!confirm('Are you absolutely certain you want to purge this profile registry line permanently?')) return;
    try {
        const response = await axios.delete(`/api/admin/users/${id}`, {
            headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
        });
        if (response.data.status === 'success') {
            feedback.value = response.data.message;
            fetchUsers();
            setTimeout(() => feedback.value = '', 4000);
        }
    } catch (err) {
        alert(err.response?.data?.message || 'Access intercept violation during dropped profile commit.');
    }
};

onMounted(() => { fetchUsers(); });
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
</style>
