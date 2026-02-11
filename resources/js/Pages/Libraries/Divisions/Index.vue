<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ModalForm from '@/Pages/Settings/Accounts/Partials/Modal.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { reactive ,ref, watch, onMounted, computed} from 'vue';
    import Swal from 'sweetalert2';
    
    const props = defineProps({
  divisions: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const overallRatingItems = [
    { label: 'Services', href: '/libraries/overall/services' },
    { label: 'Demographic', href: '/libraries/overall/demographic' },
    { label: 'CC', href: '/libraries/overall/cc' },
    { label: 'SQD-Overall Rating', href: '/libraries/overall/sqd' },
    { label: 'Services Overall Rating', href: '/libraries/overall/services-rating' },
];

const months = [
    'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL',
    'MAY', 'JUNE', 'JULY', 'AUGUST',
    'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER',
];

const quarters = [
    'FIRST QUARTER',
    'SECOND QUARTER',
    'THIRD QUARTER',
    'FOURTH QUARTER',
];

const years = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 10 }, (_, index) => (currentYear - index).toString());
});

const currentYear = ref(new Date().getFullYear().toString());
const currentMonth = ref(months[new Date().getMonth()]);
const currentQuarter = ref(getCurrentQuarter());

function getCurrentQuarter() {
    const month = new Date().getMonth();
    if (month <= 2) return 'FIRST QUARTER';
    if (month <= 5) return 'SECOND QUARTER';
    if (month <= 8) return 'THIRD QUARTER';
    return 'FOURTH QUARTER';
}

const filterForm = reactive({
    period_type: props.filters.period_type || 'By Month',
    selected_month: props.filters.selected_month || currentMonth.value,
    selected_quarter: props.filters.selected_quarter || currentQuarter.value,
    selected_year: props.filters.selected_year || currentYear.value,
});

watch(
    () => filterForm.period_type,
    (value) => {
        if (value === 'By Month') {
            filterForm.selected_month = filterForm.selected_month || currentMonth.value;
            filterForm.selected_year = filterForm.selected_year || currentYear.value;
        } else if (value === 'By Quarter') {
            filterForm.selected_quarter = filterForm.selected_quarter || currentQuarter.value;
            filterForm.selected_year = filterForm.selected_year || currentYear.value;
        } else if (value === 'By Year/Annual') {
            filterForm.selected_year = filterForm.selected_year || currentYear.value;
        }
    }
);

const buildFilterParams = () => {
    const params = {
        period_type: filterForm.period_type,
    };

    if (filterForm.period_type === 'By Month') {
        params.selected_month = filterForm.selected_month;
        params.selected_year = filterForm.selected_year;
    } else if (filterForm.period_type === 'By Quarter') {
        params.selected_quarter = filterForm.selected_quarter;
        params.selected_year = filterForm.selected_year;
    } else if (filterForm.period_type === 'By Year/Annual') {
        params.selected_year = filterForm.selected_year;
    }

    return params;
};

const applyFilters = () => {
    router.get('/libraries', buildFilterParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const goToOverall = (item) => {
    if (!item.href) {
        return;
    }
    router.get(item.href, buildFilterParams());
};

const goToReport = () => {
    router.get('/libraries/report', buildFilterParams());
};

const selectedItem = ref(null);

const selectItem = (type, item, parent = null) => {
    selectedItem.value = { type, item, parent };
};
</script>

<template>
  <AppLayout title="Libraries">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Libraries
      </h2>
    </template>

    <div class="mx-8 mt-6">
      <v-card class="mb-4">
        <v-card-text>
          <v-row>
            <v-col cols="12" md="4">
              <v-select
                v-model="filterForm.period_type"
                label="Results Type"
                variant="outlined"
                :items="['By Month', 'By Quarter', 'By Year/Annual']"
              />
            </v-col>
            <v-col v-if="filterForm.period_type === 'By Month'" cols="12" md="4">
              <v-select
                v-model="filterForm.selected_month"
                label="Select Month"
                variant="outlined"
                :items="months"
              />
            </v-col>
            <v-col v-if="filterForm.period_type === 'By Month'" cols="12" md="4">
              <v-select
                v-model="filterForm.selected_year"
                label="Select Year"
                variant="outlined"
                :items="years"
              />
            </v-col>
            <v-col v-if="filterForm.period_type === 'By Quarter'" cols="12" md="4">
              <v-select
                v-model="filterForm.selected_quarter"
                label="Select Quarter"
                variant="outlined"
                :items="quarters"
              />
            </v-col>
            <v-col v-if="filterForm.period_type === 'By Quarter'" cols="12" md="4">
              <v-select
                v-model="filterForm.selected_year"
                label="Select Year"
                variant="outlined"
                :items="years"
              />
            </v-col>
            <v-col v-if="filterForm.period_type === 'By Year/Annual'" cols="12" md="4">
              <v-select
                v-model="filterForm.selected_year"
                label="Select Year"
                variant="outlined"
                :items="years"
              />
            </v-col>
          </v-row>
          <v-btn color="primary" @click="applyFilters">Apply</v-btn>
        </v-card-text>
      </v-card>
      <v-expansion-panels variant="accordion">
        <v-expansion-panel>
          <v-expansion-panel-title>Overall Rating</v-expansion-panel-title>
          <v-expansion-panel-text>
            <v-list density="compact">
              <v-list-item
                v-for="item in overallRatingItems"
                :key="item.label"
                class="cursor-pointer"
                @click="goToOverall(item)"
                >
                <v-list-item-title>{{ item.label }}</v-list-item-title>
                </v-list-item>
            </v-list>
          </v-expansion-panel-text>
        </v-expansion-panel>

        <v-expansion-panel>
          <v-expansion-panel-title>Division and Sections</v-expansion-panel-title>
          <v-expansion-panel-text>
            <div v-if="!props.divisions.length" class="text-sm text-gray-500">
              No divisions found.
            </div>

            <div v-else class="space-y-3">
              <div v-for="division in props.divisions" :key="division.id">
                <button
                  type="button"
                  class="font-semibold cursor-pointer hover:underline"
                  @click="selectItem('division', division)"
                >
                  {{ division.division_name }}
                </button>

                <div v-if="division.sections?.length" class="pl-4">
                  <div v-for="section in division.sections" :key="section.id" class="mt-2">
                    <button
                      type="button"
                      class="font-medium cursor-pointer hover:underline"
                      @click="selectItem('section', section, division)"
                    >
                      {{ section.section_name }}
                    </button>
                    <ul v-if="section.services?.length" class="list-disc pl-5 text-sm text-gray-600">
                      <li v-for="service in section.services" :key="service.id">
                        <button
                          type="button"
                          class="cursor-pointer hover:underline"
                          @click="selectItem('service', service, section)"
                        >
                          {{ service.service_name }}
                        </button>
                      </li>
                    </ul>
                    <div v-else class="text-sm text-gray-500">No services</div>
                  </div>
                </div>

                <div v-if="division.services?.length" class="pl-4 mt-2">
                  <div class="text-sm font-medium">Services</div>
                  <ul class="list-disc pl-5 text-sm text-gray-600">
                    <li v-for="service in division.services" :key="service.id">
                      <button
                        type="button"
                        class="cursor-pointer hover:underline"
                        @click="selectItem('service', service, division)"
                      >
                        {{ service.service_name }}
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
      <div class="mt-6 text-right">
        <v-btn variant="outlined" prepend-icon="mdi-printer" @click="goToReport">Print</v-btn>
      </div>
    </div>
  </AppLayout>
</template>
