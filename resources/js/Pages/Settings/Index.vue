<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const items = [
    { label: 'Accounts', href: '/settings/accounts', icon: 'mdi-account' },
    { label: 'Offices', href: '/settings/offices', icon: 'mdi-map-marker' },
    { label: 'Divisions', href: '/settings/divisions', icon: 'mdi-domain' },
    { label: 'Sections', href: '/settings/sections', icon: 'mdi-view-list' },
    { label: 'Services', href: '/settings/services', icon: 'mdi-briefcase' },
    { label: 'CSF Date Display', href: '/show-date-csf-form', icon: 'mdi-calendar' },
];
</script>

<template>
    <AppLayout title="Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Settings
            </h2>
        </template>

        <div class="mx-8 mt-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                <Link v-for="item in items" :key="item.label" :href="item.href" class="block">
                    <div class="rounded-xl border bg-white p-6 shadow-sm transition hover:shadow-md">
                        <div class="mb-4">
                            <v-icon :icon="item.icon" size="32" />
                        </div>
                        <div class="text-lg font-semibold text-gray-800">
                            {{ item.label }}
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
