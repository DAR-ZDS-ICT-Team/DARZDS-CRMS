<script setup>
import VueMultiselect from "vue-multiselect";
import AppLayout from '@/Layouts/AppLayout.vue';
import ModalForm from '@/Pages/Libraries/Division-Sections/Form/Modal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue'

const props = defineProps({
    divisions: Object,
    user: Object,
});

const form = reactive({
    division_id: null,
    section_id: null,
    service_id: null,
});

const rating = async (division_id, section_id = null, service_id = null) => {
    form.division_id = division_id;
    form.section_id = section_id;
    form.service_id = service_id;
    router.get('/csi', form, { preserveState: true });
};

const show_modal = ref(false);
const action_clicked = ref('');
const selected_division = ref({});
const selected_section = ref({});

const goViewPage = async (division_id, section_id = null, service_id = null) => {
    form.division_id = division_id;
    form.section_id = section_id;
    form.service_id = service_id;
    router.get('/csi/view', form, { preserveState: true });
};

const showModal = async (is_show, action, division = null, section = null) => {
    show_modal.value = is_show;
    action_clicked.value = action;
    if (division) {
        selected_division.value = division;
    }
    if (section) {
        selected_section.value = section;
    }
};
</script>

<template>
    <AppLayout title="Divisions, Sections & Services">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Divisions, Sections & Services
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <v-card>
                        <v-row>
                            <v-col class="text-left m-5 mb-1" v-if="user.account_type == 'admin'">
                                <v-btn @click="showModal(true, 'add_new_division', null)" prepend-icon="mdi-plus" color="primary" size="small">
                                    Add New Division
                                </v-btn>
                            </v-col>
                        </v-row>
                       
                        <v-tabs>
                            <v-tab>All</v-tab>
                            <v-tab>Divisions with Sections</v-tab>
                            <v-tab>Divisions with Direct Services</v-tab>
                            
                            <v-tab-item>
                                <!-- All Divisions -->
                                <v-expansion-panels>
                                    <v-expansion-panel v-for="division in divisions" :key="division.id">
                                        <v-expansion-panel-title>
                                            <div class="d-flex align-center">
                                                <v-icon icon="mdi-domain" class="mr-2"></v-icon>
                                                <strong>{{ division.division_name }}</strong>
                                            </div>
                                            <template v-slot:actions>
                                                <v-btn v-if="user.account_type == 'admin'" 
                                                       icon="mdi-plus"
                                                       size="small"
                                                       color="primary"
                                                       @click.stop="showModal(true, 'add_new_section', division)">
                                                </v-btn>
                                            </template>
                                        </v-expansion-panel-title>
                                        <v-expansion-panel-text>
                                            <!-- Direct Services -->
                                            <div v-if="division.services && division.services.length > 0">
                                                <h3 class="text-lg font-bold mb-2">Direct Services</h3>
                                                <v-list>
                                                    <v-list-item v-for="service in division.services" :key="service.id">
                                                        <template v-slot:prepend>
                                                            <v-icon icon="mdi-cogs"></v-icon>
                                                        </template>
                                                        <v-list-item-title>{{ service.service_name }}</v-list-item-title>
                                                        <template v-slot:append>
                                                            <v-btn icon="mdi-eye" size="small" @click="goViewPage(division.id, null, service.id)"></v-btn>
                                                            <v-btn icon="mdi-poll" size="small" color="amber" @click="rating(division.id, null, service.id)"></v-btn>
                                                        </template>
                                                    </v-list-item>
                                                </v-list>
                                                <v-btn v-if="user.account_type == 'admin'" 
                                                       prepend-icon="mdi-plus" 
                                                       class="mt-2" 
                                                       size="small"
                                                       @click="showModal(true, 'add_new_service', division)">
                                                    Add Service
                                                </v-btn>
                                            </div>

                                            <!-- Sections -->
                                            <div v-if="division.sections && division.sections.length > 0" class="mt-4">
                                                <h3 class="text-lg font-bold mb-2">Sections</h3>
                                                <v-expansion-panels>
                                                    <v-expansion-panel v-for="section in division.sections" :key="section.id">
                                                        <v-expansion-panel-title>
                                                            <div class="d-flex align-center">
                                                                <v-icon icon="mdi-office-building" class="mr-2"></v-icon>
                                                                <strong>{{ section.section_name }}</strong>
                                                            </div>
                                                            <template v-slot:actions>
                                                                <v-btn v-if="user.account_type == 'admin'" 
                                                                       icon="mdi-plus"
                                                                       size="small"
                                                                       color="success"
                                                                       @click.stop="showModal(true, 'add_new_service', division, section)">
                                                                </v-btn>
                                                            </template>
                                                        </v-expansion-panel-title>
                                                        <v-expansion-panel-text>
                                                            <!-- Section Services -->
                                                            <div v-if="section.services && section.services.length > 0">
                                                                <v-list>
                                                                    <v-list-item v-for="service in section.services" :key="service.id">
                                                                        <template v-slot:prepend>
                                                                            <v-icon icon="mdi-cogs"></v-icon>
                                                                        </template>
                                                                        <v-list-item-title>{{ service.service_name }}</v-list-item-title>
                                                                        <template v-slot:append>
                                                                            <v-btn icon="mdi-eye" size="small" @click="goViewPage(division.id, section.id, service.id)"></v-btn>
                                                                            <v-btn icon="mdi-poll" size="small" color="amber" @click="rating(division.id, section.id, service.id)"></v-btn>
                                                                        </template>
                                                                    </v-list-item>
                                                                </v-list>
                                                            </div>
                                                            <div v-else>
                                                                <p class="text-gray-500">No services found for this section.</p>
                                                            </div>
                                                            <v-btn v-if="user.account_type == 'admin'" 
                                                                   prepend-icon="mdi-plus" 
                                                                   class="mt-2" 
                                                                   size="small"
                                                                   @click="showModal(true, 'add_new_service', division, section)">
                                                                Add Service
                                                            </v-btn>
                                                        </v-expansion-panel-text>
                                                    </v-expansion-panel>
                                                </v-expansion-panels>
                                            </div>

                                            <div v-if="(!division.services || division.services.length === 0) && (!division.sections || division.sections.length === 0)">
                                                <p class="text-gray-500">No sections or services found for this division.</p>
                                            </div>

                                            <div class="mt-3" v-if="user.account_type == 'admin' && (!division.sections || division.sections.length === 0)">
                                                <v-btn prepend-icon="mdi-plus" size="small" @click="showModal(true, 'add_new_service', division)">
                                                    Add Direct Service
                                                </v-btn>
                                            </div>
                                        </v-expansion-panel-text>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-tab-item>

                            <v-tab-item>
                                <!-- Only Divisions with Sections -->
                                <v-expansion-panels>
                                    <v-expansion-panel v-for="division in divisions.filter(d => d.sections && d.sections.length > 0)" :key="division.id">
                                        <!-- Same content as above, but filtered -->
                                        <v-expansion-panel-title>
                                            <div class="d-flex align-center">
                                                <v-icon icon="mdi-domain" class="mr-2"></v-icon>
                                                <strong>{{ division.division_name }}</strong>
                                            </div>
                                        </v-expansion-panel-title>
                                        <v-expansion-panel-text>
                                            <div class="mt-4">
                                                <h3 class="text-lg font-bold mb-2">Sections</h3>
                                                <v-expansion-panels>
                                                    <v-expansion-panel v-for="section in division.sections" :key="section.id">
                                                        <v-expansion-panel-title>
                                                            <div class="d-flex align-center">
                                                                <v-icon icon="mdi-office-building" class="mr-2"></v-icon>
                                                                <strong>{{ section.section_name }}</strong>
                                                            </div>
                                                            <template v-slot:actions>
                                                                <v-btn v-if="user.account_type == 'admin'" 
                                                                       icon="mdi-plus"
                                                                       size="small"
                                                                       color="success"
                                                                       @click.stop="showModal(true, 'add_new_service', division, section)">
                                                                </v-btn>
                                                            </template>
                                                        </v-expansion-panel-title>
                                                        <v-expansion-panel-text>
                                                            <!-- Section Services -->
                                                            <div v-if="section.services && section.services.length > 0">
                                                                <v-list>
                                                                    <v-list-item v-for="service in section.services" :key="service.id">
                                                                        <template v-slot:prepend>
                                                                            <v-icon icon="mdi-cogs"></v-icon>
                                                                        </template>
                                                                        <v-list-item-title>{{ service.service_name }}</v-list-item-title>
                                                                        <template v-slot:append>
                                                                            <v-btn icon="mdi-eye" size="small" @click="goViewPage(division.id, section.id, service.id)"></v-btn>
                                                                            <v-btn icon="mdi-poll" size="small" color="amber" @click="rating(division.id, section.id, service.id)"></v-btn>
                                                                        </template>
                                                                    </v-list-item>
                                                                </v-list>
                                                            </div>
                                                            <div v-else>
                                                                <p class="text-gray-500">No services found for this section.</p>
                                                            </div>
                                                            <v-btn v-if="user.account_type == 'admin'" 
                                                                   prepend-icon="mdi-plus" 
                                                                   class="mt-2" 
                                                                   size="small"
                                                                   @click="showModal(true, 'add_new_service', division, section)">
                                                                Add Service
                                                            </v-btn>
                                                        </v-expansion-panel-text>
                                                    </v-expansion-panel>
                                                </v-expansion-panels>
                                            </div>
                                        </v-expansion-panel-text>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-tab-item>

                            <v-tab-item>
                                <!-- Only Divisions with Direct Services -->
                                <v-expansion-panels>
                                    <v-expansion-panel v-for="division in divisions.filter(d => d.services && d.services.length > 0)" :key="division.id">
                                        <v-expansion-panel-title>
                                            <div class="d-flex align-center">
                                                <v-icon icon="mdi-domain" class="mr-2"></v-icon>
                                                <strong>{{ division.division_name }}</strong>
                                            </div>
                                            <template v-slot:actions>
                                                <v-btn v-if="user.account_type == 'admin'" 
                                                       icon="mdi-plus"
                                                       size="small"
                                                       color="primary"
                                                       @click.stop="showModal(true, 'add_new_service', division)">
                                                </v-btn>
                                            </template>
                                        </v-expansion-panel-title>
                                        <v-expansion-panel-text>
                                            <div>
                                                <h3 class="text-lg font-bold mb-2">Direct Services</h3>
                                                <v-list>
                                                    <v-list-item v-for="service in division.services" :key="service.id">
                                                        <template v-slot:prepend>
                                                            <v-icon icon="mdi-cogs"></v-icon>
                                                        </template>
                                                        <v-list-item-title>{{ service.service_name }}</v-list-item-title>
                                                        <template v-slot:append>
                                                            <v-btn icon="mdi-eye" size="small" @click="goViewPage(division.id, null, service.id)"></v-btn>
                                                            <v-btn icon="mdi-poll" size="small" color="amber" @click="rating(division.id, null, service.id)"></v-btn>
                                                        </template>
                                                    </v-list-item>
                                                </v-list>
                                                <v-btn v-if="user.account_type == 'admin'" 
                                                       prepend-icon="mdi-plus" 
                                                       class="mt-2" 
                                                       size="small"
                                                       @click="showModal(true, 'add_new_service', division)">
                                                    Add Service
                                                </v-btn>
                                            </div>
                                        </v-expansion-panel-text>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-tab-item>
                        </v-tabs>
                    </v-card>
                </div>
            </div>
        </div>

      <ModalForm 
          :value="show_modal"
          :action_clicked="action_clicked"
          :selected_division="selected_division"
          :selected_section="selected_section"
          :data="props"
          @input="showModal"
      ></ModalForm>
    </AppLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>