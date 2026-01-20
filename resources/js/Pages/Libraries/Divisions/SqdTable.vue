<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    sqd_rows: {
        type: Array,
        default: () => [],
    },
    sqd_totals: {
        type: Object,
        default: () => ({}),
    },
});

const formatPercent = (value) => `${value}%`;
</script>

<template>
    <AppLayout title="Overall SQD">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Overall SQD
            </h2>
        </template>

        <div class="mx-10 mt-6">
            <v-table density="compact">
                <thead>
                    <tr>
                        <th>Service Quality Dimensions</th>
                        <th class="text-right">Strongly Disagree</th>
                        <th class="text-right">Disagree</th>
                        <th class="text-right">Neither Agree nor Disagree</th>
                        <th class="text-right">Agree</th>
                        <th class="text-right">Strongly Agree</th>
                        <th class="text-right">N/A</th>
                        <th class="text-right">Responses</th>
                        <th class="text-right">Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in props.sqd_rows" :key="row.label">
                        <td>{{ row.label }}</td>
                        <td class="text-right">{{ row.strongly_disagree }}</td>
                        <td class="text-right">{{ row.disagree }}</td>
                        <td class="text-right">{{ row.neither }}</td>
                        <td class="text-right">{{ row.agree }}</td>
                        <td class="text-right">{{ row.strongly_agree }}</td>
                        <td class="text-right">{{ row.na }}</td>
                        <td class="text-right">{{ row.responses }}</td>
                        <td class="text-right">{{ formatPercent(row.rating) }}</td>
                    </tr>
                    <tr class="font-semibold">
                        <td>Total</td>
                        <td class="text-right">{{ props.sqd_totals.strongly_disagree ?? 0 }}</td>
                        <td class="text-right">{{ props.sqd_totals.disagree ?? 0 }}</td>
                        <td class="text-right">{{ props.sqd_totals.neither ?? 0 }}</td>
                        <td class="text-right">{{ props.sqd_totals.agree ?? 0 }}</td>
                        <td class="text-right">{{ props.sqd_totals.strongly_agree ?? 0 }}</td>
                        <td class="text-right">{{ props.sqd_totals.na ?? 0 }}</td>
                        <td class="text-right">{{ props.sqd_totals.responses ?? 0 }}</td>
                        <td class="text-right">{{ formatPercent(props.sqd_totals.rating ?? 0) }}</td>
                    </tr>
                </tbody>
            </v-table>
        </div>
    </AppLayout>
</template>
