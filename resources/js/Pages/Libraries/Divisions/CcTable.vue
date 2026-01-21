<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    cc_tables: {
        type: Array,
        default: () => [],
    },
});

const formatPercent = (value) => `${value}%`;
</script>

<template>
    <AppLayout title="Overall Rating - CC">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Overall Rating - CC
            </h2>
        </template>

        <div class="mx-10 mt-6 space-y-6">
            <div>
                <Link href="/libraries">
                    <v-btn variant="outlined" prepend-icon="mdi-arrow-left">Back</v-btn>
                </Link>
            </div>
            <v-table v-for="table in props.cc_tables" :key="table.title" density="compact">
                <thead>
                    <tr>
                        <th>{{ table.title }}</th>
                        <th class="text-right">Responses</th>
                        <th class="text-right">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in table.rows" :key="row.label">
                        <td>{{ row.label }}</td>
                        <td class="text-right">{{ row.count }}</td>
                        <td class="text-right">{{ formatPercent(row.percentage) }}</td>
                    </tr>
                </tbody>
            </v-table>
        </div>
    </AppLayout>
</template>
