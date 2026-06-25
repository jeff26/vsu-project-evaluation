<template>
    <div :class="classObj" class="app-wrapper min-h-screen w-full flex bg-slate-50 relative overflow-hidden font-sans antialiased">

        <div
            v-if="isMobile && isSidebarOpen"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 transition-opacity"
            @click="closeSidebar"
        />

        <Sidebar
            :class="[
        isMobile
          ? (isSidebarOpen ? 'translate-x-0' : '-translate-x-full')
          : (isSidebarOpen ? 'w-64' : 'w-0 !p-0 overflow-hidden border-none')
      ]"
            class="fixed md:relative transition-all duration-300 ease-in-out h-full"
        />

        <div class="flex-1 flex flex-col min-w-0 min-h-screen">

            <div class="p-6 pb-2 flex-shrink-0">
                <Navbar @toggle-sidebar="toggleSidebar" />
            </div>

            <main class="flex-1 px-6 py-2 flex flex-col overflow-hidden">
                <AppMain class="bg-white rounded-xl shadow-xs border border-slate-200/40 p-6 flex-1" />
            </main>

            <footer class="h-12 border-t border-slate-200 bg-white/80 backdrop-blur-xs flex items-center justify-center text-xs text-slate-400 font-medium mt-4 flex-shrink-0">
                <p>Developed by VSU Management Information System</p>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import Navbar from './Navbar.vue';
import Sidebar from './Sidebar.vue';
import AppMain from './AppMain.vue';

// Responsive Screen Layout States
const isSidebarOpen = ref(true);
const isMobile = ref(false);

const classObj = computed(() => ({
    hideSidebar: !isSidebarOpen.value,
    openSidebar: isSidebarOpen.value,
    mobileDevice: isMobile.value
}));

// Actions
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};
const closeSidebar = () => {
    isSidebarOpen.value = false;
};

// Pure Vue 3 Window Resize Handler (Replaces legacy mixin approach)
const handleResize = () => {
    isMobile.value = window.innerWidth < 768;
    if (isMobile.value) {
        isSidebarOpen.value = false;
    } else {
        isSidebarOpen.value = true;
    }
};

onMounted(() => {
    handleResize();
    window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
});
</script>
