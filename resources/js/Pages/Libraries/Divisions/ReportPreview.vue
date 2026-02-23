<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Printd } from 'printd';
import ByQuarter1 from '@/Pages/CSI/Quarterly/Printouts/ByQuarter1.vue';
import ByQuarter2 from '@/Pages/CSI/Quarterly/Printouts/ByQuarter2.vue';
import ByQuarter3 from '@/Pages/CSI/Quarterly/Printouts/ByQuarter3.vue';
import ByQuarter4 from '@/Pages/CSI/Quarterly/Printouts/ByQuarter4.vue';
import ByMonthly from '@/Pages/CSI/Monthly/Printouts/ByMonthly.vue';
import ByYearly from '@/Pages/CSI/Yearly/Printouts/ByYearly.vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    form: {
        type: Object,
        default: () => ({}),
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

const periodType = computed(() => props.form?.period_type);
const selectedQuarter = computed(() => props.form?.selected_quarter);

const isPrinting = ref(false);

const printReport = async () => {
    isPrinting.value = true;
    setTimeout(async () => {
        try {
            const printElement = document.querySelector('.print-id');
            if (!printElement) {
                alert('Error: Could not find content to print.');
                return;
            }
            const d = new Printd();
            const css = `
                @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');
                * { font-family: 'Times New Roman', serif; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #333; padding: 3px; }
                .page-break { page-break-before: always; }
                @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
            `;
            await d.print(printElement, [css]);
        } finally {
            isPrinting.value = false;
        }
    }, 200);
};
</script>

<template>
    <AppLayout title="Libraries Report Preview">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Libraries Report Preview
            </h2>
        </template>

        <div class="mx-8 mt-6">
            <div class="mb-4 flex items-center justify-between">
                <Link :href="backHref">
                    <v-btn variant="outlined" prepend-icon="mdi-arrow-left">Back</v-btn>
                </Link>
                <v-btn :loading="isPrinting" variant="outlined" prepend-icon="mdi-printer" @click="printReport">
                    Print
                </v-btn>
            </div>

            <div v-if="periodType === 'By Quarter'">
                <ByQuarter1 v-if="selectedQuarter === 'FIRST QUARTER'" :data="props.data" :form="props.form" />
                <ByQuarter2 v-else-if="selectedQuarter === 'SECOND QUARTER'" :data="props.data" :form="props.form" />
                <ByQuarter3 v-else-if="selectedQuarter === 'THIRD QUARTER'" :data="props.data" :form="props.form" />
                <ByQuarter4 v-else-if="selectedQuarter === 'FOURTH QUARTER'" :data="props.data" :form="props.form" />
                <div v-else class="text-sm text-gray-500">Select a quarter to preview the report.</div>
            </div>

            <div v-else-if="periodType === 'By Month'">
                <ByMonthly :data="props.data" :form="props.form" />
            </div>

            <div v-else-if="periodType === 'By Year/Annual'">
                <ByYearly :data="props.data" :form="props.form" />
            </div>

            <div v-else class="text-sm text-gray-500">
                No report preview available for the selected period yet.
            </div>
        </div>
    </AppLayout>
</template>
