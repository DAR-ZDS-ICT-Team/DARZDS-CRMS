<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    external_service_stats: {
        type: Array,
        default: () => [],
    },
    internal_service_stats: {
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

            <div v-if="props.external_service_stats.length" class="mb-8">
                <div class="text-lg font-semibold mb-2">External Services</div>
                <v-table density="compact">
                    <thead>
                        <tr>
                            <th style="width: 48px;">#</th>
                            <th>Services</th>
                            <th class="text-right">Responses</th>
                            <th class="text-right">Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(service, index) in props.external_service_stats" :key="service.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ service.service_name }}</td>
                            <td class="text-right">{{ service.responses }}</td>
                            <td class="text-right">{{ service.transactions }}</td>
                        </tr>
                    </tbody>
                </v-table>
            </div>
            <div v-else class="text-sm text-gray-500 mb-8">No external services found.</div>

            <div v-if="props.internal_service_stats.length">
                <div class="text-lg font-semibold mb-2">Internal Services</div>
                <v-table density="compact">
                    <thead>
                        <tr>
                            <th style="width: 48px;">#</th>
                            <th>Services</th>
                            <th class="text-right">Responses</th>
                            <th class="text-right">Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(service, index) in props.internal_service_stats" :key="service.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ service.service_name }}</td>
                            <td class="text-right">{{ service.responses }}</td>
                            <td class="text-right">{{ service.transactions }}</td>
                        </tr>
                    </tbody>
                </v-table>
            </div>
            <div v-else class="text-sm text-gray-500">No internal services found.</div>
        </div>
    </AppLayout>
</template>
