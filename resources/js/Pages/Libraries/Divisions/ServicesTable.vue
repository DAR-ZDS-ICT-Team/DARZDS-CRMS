<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    service_stats: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const backHref = computed(() => {
    const params = new URLSearchParams();
    if (props.filters.period_type) params.set('period_type', props.filters.period_type);
    if (props.filters.selected_month) params.set('selected_month', props.filters.selected_month);
    if (props.filters.selected_quarter) params.set('selected_quarter', props.filters.selected_quarter);
    if (props.filters.selected_year) params.set('selected_year', props.filters.selected_year);
    const query = params.toString();
    return query ? `/libraries?${query}` : '/libraries';
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
                <Link :href="backHref">
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
