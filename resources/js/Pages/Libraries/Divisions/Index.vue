<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ModalForm from '@/Pages/Account/Partials/Modal.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { reactive ,ref, watch, onMounted} from 'vue';
    import Swal from 'sweetalert2';
    
    const props = defineProps({
  divisions: {
    type: Array,
    default: () => [],
  },
});

const overallRatingItems = [
    { label: 'Services', href: '/libraries/overall/services' },
    { label: 'Demographic', href: '/libraries/overall/demographic' },
    { label: 'CC', href: '/libraries/overall/cc' },
    { label: 'SQD-Overall Rating', href: '/libraries/overall/sqd' },
    { label: 'Services Overall Rating', href: '/libraries/overall/services-rating' },
];

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
      <v-expansion-panels variant="accordion">
        <v-expansion-panel>
          <v-expansion-panel-title>Overall Rating</v-expansion-panel-title>
          <v-expansion-panel-text>
            <v-list density="compact">
              <v-list-item
                v-for="item in overallRatingItems"
                :key="item.label"
                class="cursor-pointer"
                @click="item.href && router.get(item.href)"
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
    </div>
  </AppLayout>
</template>
