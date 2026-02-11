<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    yearly_comparison: {
        type: Object,
        default: () => ({ labels: [], values: [] }),
    },
    service_pie_yearly: {
        type: Object,
        default: () => ({ title: '', items: [] }),
    },
    service_pie_quarterly: {
        type: Object,
        default: () => ({ title: '', items: [] }),
    },
});

const bars = computed(() => {
    const labels = props.yearly_comparison?.labels ?? [];
    const values = props.yearly_comparison?.values ?? [];
    const max = Math.max(...values, 1);
    const colors = ['bg-slate-400', 'bg-emerald-500'];
    return labels.map((label, index) => {
        const value = values[index] ?? 0;
        return {
            label,
            value,
            height: Math.round((value / max) * 100),
            color: colors[index % colors.length],
        };
    });
});

const totalResponses = computed(() =>
    bars.value.reduce((sum, bar) => sum + bar.value, 0)
);

const yTicks = computed(() => {
    const values = props.yearly_comparison?.values ?? [];
    const maxValue = Math.max(...values, 0);
    const steps = 4;
    const step = maxValue === 0 ? 1 : Math.ceil(maxValue / steps);
    return Array.from({ length: steps + 1 }, (_, index) => step * (steps - index));
});

const palette = [
    '#0f766e',
    '#2563eb',
    '#f59e0b',
    '#10b981',
    '#ef4444',
    '#8b5cf6',
    '#14b8a6',
    '#f97316',
    '#06b6d4',
    '#4b5563',
];

const normalizePie = (items) => {
    const safeItems = Array.isArray(items) ? items : [];
    const total = safeItems.reduce((sum, item) => sum + (Number(item.value) || 0), 0);
    let acc = 0;
    return safeItems.map((item, index) => {
        const value = Number(item.value) || 0;
        const percent = total ? (value / total) * 100 : 0;
        const start = acc;
        acc += percent;
        return {
            label: item.label,
            value,
            percent,
            start,
            end: acc,
            color: palette[index % palette.length],
        };
    });
};

const yearlyPie = computed(() => normalizePie(props.service_pie_yearly?.items));
const quarterlyPie = computed(() => normalizePie(props.service_pie_quarterly?.items));
const yearlyTotal = computed(() =>
    (props.service_pie_yearly?.items || []).reduce((sum, item) => sum + (Number(item.value) || 0), 0)
);
const quarterlyTotal = computed(() =>
    (props.service_pie_quarterly?.items || []).reduce((sum, item) => sum + (Number(item.value) || 0), 0)
);

const makeConicGradient = (slices) => {
    const segments = slices
        .filter((slice) => slice.percent > 0)
        .map((slice) => `${slice.color} ${slice.start}% ${slice.end}%`);
    return segments.length
        ? `conic-gradient(${segments.join(', ')})`
        : 'conic-gradient(#e5e7eb 0% 100%)';
};

const yearlyGradient = computed(() => makeConicGradient(yearlyPie.value));
const quarterlyGradient = computed(() => makeConicGradient(quarterlyPie.value));
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="mx-8 mt-6">
            <div class="rounded-xl border bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Responses Comparison
                        </h3>
                        <p class="text-sm text-gray-500">
                            Current year vs last year (CSF submissions)
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        Total: {{ totalResponses }}
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex">
                        <div class="mr-3 flex flex-col items-center">
                            <div class="mb-2 text-xs text-gray-500">Responses</div>
                            <div class="flex h-52 flex-col justify-between text-xs text-gray-500">
                                <span v-for="tick in yTicks" :key="tick">{{ tick }}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex h-52 items-end justify-around rounded-lg bg-slate-100 p-4">
                                <div v-for="bar in bars" :key="bar.label" class="flex flex-col items-center gap-2">
                                    <div
                                        class="w-20 rounded-md transition-all"
                                        :class="bar.color"
                                        :style="{ height: bar.height + '%' }"
                                    />
                                    <div class="text-xs text-gray-600">
                                        {{ bar.value }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center justify-around text-xs text-gray-500">
                                <span v-for="bar in bars" :key="bar.label">{{ bar.label }}</span>
                            </div>
                            <div class="mt-1 text-center text-xs text-gray-400">Year</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-xl border bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-1">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ service_pie_yearly.title || 'Yearly Service Share' }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Total responses: {{ yearlyTotal }}
                        </p>
                    </div>

                    <div class="mt-6 flex flex-col gap-6 md:flex-row">
                        <div class="mx-auto h-56 w-56 rounded-full border" :style="{ background: yearlyGradient }" />
                        <div class="flex-1 space-y-2 overflow-auto pr-1">
                            <div
                                v-for="slice in yearlyPie"
                                :key="slice.label"
                                class="flex items-center gap-2 text-sm"
                            >
                                <span class="h-3 w-3 rounded-sm" :style="{ background: slice.color }" />
                                <span class="truncate text-gray-700">{{ slice.label }}</span>
                                <span class="ml-auto text-gray-500">
                                    {{ slice.percent.toFixed(1) }}%
                                </span>
                            </div>
                            <div v-if="!yearlyPie.length" class="text-sm text-gray-500">
                                No data available.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-1">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ service_pie_quarterly.title || 'Quarterly Service Share' }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Total responses: {{ quarterlyTotal }}
                        </p>
                    </div>

                    <div class="mt-6 flex flex-col gap-6 md:flex-row">
                        <div class="mx-auto h-56 w-56 rounded-full border" :style="{ background: quarterlyGradient }" />
                        <div class="flex-1 space-y-2 overflow-auto pr-1">
                            <div
                                v-for="slice in quarterlyPie"
                                :key="slice.label"
                                class="flex items-center gap-2 text-sm"
                            >
                                <span class="h-3 w-3 rounded-sm" :style="{ background: slice.color }" />
                                <span class="truncate text-gray-700">{{ slice.label }}</span>
                                <span class="ml-auto text-gray-500">
                                    {{ slice.percent.toFixed(1) }}%
                                </span>
                            </div>
                            <div v-if="!quarterlyPie.length" class="text-sm text-gray-500">
                                No data available.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
