<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    service_ratings: {
        type: Array,
        default: () => [],
    },
});

const formatRating = (value) => {
    if (value === null || value === undefined) {
        return 'N/A';
    }
    return `${value}%`;
};
</script>

<template>
    <AppLayout title="Services Overall Rating">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Services Overall Rating
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
                        <th class="text-right">Overall Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(service, index) in props.service_ratings" :key="service.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ service.service_name }}</td>
                        <td class="text-right">{{ formatRating(service.rating) }}</td>
                    </tr>
                </tbody>
            </v-table>
        </div>
    </AppLayout>
</template>
