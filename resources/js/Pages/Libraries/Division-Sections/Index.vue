<script setup>
import VueMultiselect from "vue-multiselect";
import AppLayout from '@/Layouts/AppLayout.vue';
import ModalForm from '@/Pages/Libraries/Division-Sections/Form/Modal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref, watch} from 'vue'

const props = defineProps({
    division_sections: Object,
    user: Object,
});

const form = reactive({
  division_id: null,
  division_name: null,
  section_id: null,
  section_name: null
})

const show_modal = ref(false);
const action_clicked = ref('');
const selected_division = ref({});
const selected_section = ref({});

const goViewPage = async (division_id, division_name, section_id = null, section_name = null) => {
   form.division_id = division_id;
   form.division_name = division_name;
   form.section_id = section_id;
   form.section_name = section_name;
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

const openPDF = () => {
    const pdfPath = 'https://drive.google.com/file/d/1s7hgXu2_3znCrcKrXX0PWJUQfwb7SMWU/view?usp=sharing';
    window.open(pdfPath, '_blank');
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                 Divisions and Sections Management
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
                            <v-col class="text-right m-5 mb-1">
                                <!-- Other buttons if needed -->
                            </v-col>
                        </v-row>
                       
                        <table class="w-full">
                            <thead class="font-bold text-center">
                                <th class="pb-4 pt-6 px-6" colspan="2">Divisions and Sections</th>
                                <th class="pb-4 pt-6 px-6">Actions</th>
                            </thead>
                            
                            <!-- Divisions with Sections -->
                            <template v-if="division_sections" v-for="(division_section, index) in division_sections.data" :key="division_section.id">
                                <tr class="border border-solid bg-blue-100">                
                                    <td class="m5 p-5 border border-solid font-black" colspan="2">
                                        {{ division_section.division_name }}
                                    </td>  
                                    <td class="m5 p-5 border border-solid font-black text-center" colspan="3" v-if="user.account_type == 'admin'">
                                        <v-btn @click="showModal(true, 'add_new_section', division_section)" prepend-icon="mdi-plus" color="primary" size="small" class="mr-2">
                                            Add New Section
                                        </v-btn>
                                    </td>  
                                </tr>       

                                <tr v-if="division_section.sections && division_section.sections.length" v-for="(section, sIndex) in division_section.sections" :key="section.id"> 
                                    <td class="text-center p-2 border border-solid hover:bg-gray-100 focus-within:bg-gray-100">
                                        {{ section.id }}
                                    </td>  
                                    <td class="p-2 mr-2 border border-solid hover:bg-gray-100 focus-within:bg-gray-100">
                                        {{ section.section_name }}
                                    </td>  
                                    <td class="text-center px-4 py-2 p-2 mr-2 border border-solid">
                                        <v-btn prepend-icon="mdi-eye" 
                                                class="mr-3" 
                                                size="small" 
                                                @click="goViewPage(division_section.id, division_section.division_name, section.id, section.section_name)"
                                                :disabled="user.account_type == 'user' && user.section_id != section.id">
                                            View
                                        </v-btn>
                                    </td>  
                                </tr>
                            </template>
                        </table>
                       <v-divider :thickness="1" class="border-opacity-100 mb-5"></v-divider>
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

<style scoped>
   table {
    border-collapse: collapse;
    width: 100%;
    border: none;
  }
  tr, th, td {
    border: 1px solid none;
    padding: 8px;
  }
</style>