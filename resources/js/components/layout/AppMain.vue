<template>
    <section class="flex-1 w-full relative overflow-y-auto min-h-0">
        <router-view v-slot="{ Component }">
            <transition name="fade-transform" mode="out-in">
                <component :is="Component" />
            </transition>
        </router-view>
    </section>
</template>

<style scoped>
/* Smooth page loading transitions */
.fade-transform-enter-active,
.fade-transform-leave-active {
    transition: all 0.2s ease-out;
}
.fade-transform-enter-from {
    opacity: 0;
    transform: translateY(4px);
}
.fade-transform-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>

