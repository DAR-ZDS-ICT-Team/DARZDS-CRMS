<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    service_stats: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout title="Overall Rating - Services">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Overall Rating - Services
            </h2>
        </template>

        <div class="mx-10 mt-6">
            <div class="mb-4">
                <Link href="/libraries">
                    <v-btn variant="outlined" prepend-icon="mdi-arrow-left">Back</v-btn>
                </Link>
            </div>
            <v-table density="compact">
                <thead>
                    <tr>
                        <th style="width: 48px;">#</th>
                        <th>Services</th>
                        <th class="text-right">Transactions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(service, index) in props.service_stats" :key="service.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ service.service_name }}</td>
                        <td class="text-right">{{ service.responses }}</td>
                        <td class="text-right">{{ service.transactions }}</td>
                    </tr>
                </tbody>
            </v-table>
        </div>
    </AppLayout>
</template>
