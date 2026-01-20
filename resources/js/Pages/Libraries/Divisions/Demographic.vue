<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    client_types: { type: Array, default: () => [] },
    age_groups: { type: Array, default: () => [] },
    sexes: { type: Array, default: () => [] },
    total_customers: { type: Number, default: 0 },
});

const totalLabel = (label, group) => {
    if (group === 'sex' && label === 'Not Indicated') {
        return 'No Sex (Not Indicated)';
    }
    return label;
};
</script>

<template>
    <AppLayout title="Overall Rating - Demographic">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Overall Rating - Demographic
            </h2>
        </template>

        <div class="mx-10 mt-6 space-y-6">
            <v-table density="compact">
                <thead>
                    <tr>
                        <th>Type of Client Served</th>
                        <th class="text-right">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in props.client_types" :key="item.label">
                        <td>{{ item.label }}</td>
                        <td class="text-right">{{ item.count }}</td>
                    </tr>
                    <tr class="font-semibold">
                        <td>Total</td>
                        <td class="text-right">{{ props.total_customers }}</td>
                    </tr>
                </tbody>
            </v-table>

            <v-table density="compact">
                <thead>
                    <tr>
                        <th>Age Group</th>
                        <th class="text-right">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in props.age_groups" :key="item.label">
                        <td>{{ item.label }}</td>
                        <td class="text-right">{{ item.count }}</td>
                    </tr>
                    <tr class="font-semibold">
                        <td>Total</td>
                        <td class="text-right">{{ props.total_customers }}</td>
                    </tr>
                </tbody>
            </v-table>

            <v-table density="compact">
                <thead>
                    <tr>
                        <th>Sex Disaggregation</th>
                        <th class="text-right">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in props.sexes" :key="item.label">
                        <td>{{ totalLabel(item.label, 'sex') }}</td>
                        <td class="text-right">{{ item.count }}</td>
                    </tr>
                    <tr class="font-semibold">
                        <td>Total</td>
                        <td class="text-right">{{ props.total_customers }}</td>
                    </tr>
                </tbody>
            </v-table>
        </div>
    </AppLayout>
</template>
