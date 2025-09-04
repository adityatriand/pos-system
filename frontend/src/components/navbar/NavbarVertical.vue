<template>
    <aside class="w-[250px] shrink-0 border-r flex flex-col">
        <div class="px-2 py-1 min-h-[60px] text-lg font-semibold border-b flex flex-row gap-2 items-center">
            <img :src="logoPos" alt="Logo POS" class="w-12 h-12" />
            <span>POS System</span>
        </div>
        <nav class="flex-1 overflow-y-auto">
            <ul class="p-2 space-y-1">
                <li>
                    <router-link v-for="value in MENUS" :key="`menu-${value.label}`" :to="value.link"
                        class="block rounded-lg p-2 hover:bg-primary hover:text-white my-2"
                        active-class="bg-primary font-medium text-white">
                        <div class="flex flex-row items-center p-2 text-sm font-medium gap-2">
                            <component :is="value.icon" class="w-5 h-5" />
                            <span>{{ value.label }}</span>
                        </div>
                    </router-link>
                </li>
            </ul>
        </nav>
    </aside>
    <main class="flex-1 overflow-y-auto">
        <header class="sticky top-0 z-10 border-b px-2 py-1 content-center min-h-[60px]">
            <div class="flex flex-row justify-between w-[100%] items-center">
                <h1 class="text-xl font-semibold">Hello, Admin!</h1>
                <Avatar :name="'Admin'" />
            </div>
        </header>

        <section class="p-4">
            <router-view />
        </section>
    </main>
</template>

<script setup lang="ts">
import logoPos from '@/assets/logo-pos.png';
import Avatar from '../avatar/Avatar.vue';
import { MENUS } from './helpers';

</script>