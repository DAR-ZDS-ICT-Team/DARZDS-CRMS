<script setup>
import VueMultiselect from "vue-multiselect";
import AppLayout from '@/Layouts/AppLayout.vue';
import ModalForm from '@/Pages/Libraries/Division-Sections/Form/Modal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive ,ref, watch} from 'vue'

const props = defineProps({
    division_sections: Object,
    sub_sections: Object,
    user: Object,
});

const form = reactive({
  division_id: null,
  section_id: null,
})

const rating = async (division_id, section_id) => {

   form.division_id = division_id;
   form.section_id = section_id;
   router.get('/csi', form , { preserveState: true });
};

const all_division_section_rating = async () => {
   form.form_type = "all sections";
   router.get('/csi/all-sections', form , { preserveState: true })
};

const show_modal = ref(false);
const action_clicked = ref('');
const selected_division = ref({});


const goViewPage = async (division_id, section_id) => {
   form.division_id = division_id;
   form.section_id = section_id;
   router.get('/csi/view', form , { preserveState: true });

};

const showDivisionModal = async (is_show, action , division) => {
    show_modal.value =is_show;
    action_clicked.value = action;
    if(division){
        selected_division.value = division;
    }
};

const openPDF = () => {
    // Replace 'path/to/your/pdf/file.pdf' with the actual path to your PDF file
    const pdfPath = 'https://drive.google.com/file/d/1s7hgXu2_3znCrcKrXX0PWJUQfwb7SMWU/view?usp=sharing';
    
    // Open the PDF in a new tab or window
    window.open(pdfPath, '_blank');
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                 <!-- <v-breadcrumbs :items="['Dashboard', 'Division Sections']"></v-breadcrumbs> -->
                 Division Sections
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <v-card>
                      
                        <v-row>
                            <v-col class="text-left m-5 mb-1" v-if="user.account_type == 'admin'">
                                <v-btn @click="showDivisionModal(true, 'add_new_division', null)" prepend-icon="mdi-plus" color="primary"  size="small">
                                       Add New Division
                                </v-btn>
                            </v-col>
                            <v-col class="text-right m-5 mb-1">
                                <!-- <v-btn @click="all_division_section_rating()" prepend-icon="mdi-file" color="yellow" style="margin-right:100px"  size="small">
                                        All Section Ratings
                                </v-btn> -->

                                <!-- <v-btn 
                                    prepend-icon="mdi-printer" 
                                    class="mr-5"
                                    color="success"
                                    size="small"
                                    @click="openPDF()"
                                        >CSF Form(manual)
                                </v-btn>    -->
                                </v-col>
                            </v-row>
                        
                       
                        <table class="w-full">
                            
                            <thead class="font-bold text-center ">
                                <th class="pb-4 pt-6 px-6" colspan="2">Division Sections</th>
                                <th class="pb-4 pt-6 px-6">Actions</th>
                            </thead>
                            
                            <template v-if="division_sections" v-for="(division_section, index) in division_sections.data" :key="division_section.id">
                                <tr class="border border-solid bg-blue-100">                
                                    <td class="m5 p-5  border border-solid font-black" colspan="2" >
                                         {{ division_section.division_name }}
                                    </td>  
                                    <td class="m5 p-5  border border-solid font-black text-center" colspan="3" v-if="user.account_type == 'admin'">
                                         <v-btn @click="showDivisionModal(true, 'add_new_section', division_section )" prepend-icon="mdi-plus" color="primary" size="small">
                                            Add New Section
                                        </v-btn>
                                    </td>  
                                </tr>       

                                <tr v-if="division_section.sections && division_section.sections.length" v-for="(section, index) in division_section.sections" :key="section.id"> 
                                    <td class="text-center p-2 border border-solid hover:bg-gray-100 focus-within:bg-gray-100">
                                        {{ section.id }}
                                    </td>  
                                    <td  class="p-2 mr-2  border border-solid hover:bg-gray-100 focus-within:bg-gray-100">
                                        {{ section.section_name }}
                                    </td>  
                                     <td class="text-center px-4 py-2 p-2 mr-2 border border-solid" 
                                     >

                                       <div>
                                         <v-btn prepend-icon="mdi-eye" class="mr-3" size="small" @click="goViewPage(division_section.id, section.id)"
                                            :disabled="user.account_type == 'user' && user.section_id != section.id"
                                        >
                                            View 
                                        </v-btn>
          
                                        <v-btn
                                            @click="rating(division_section.id, section.id)" 
                                            prepend-icon="mdi-file" color="yellow" 
                                            size="small"
                                            :disabled="user.account_type == 'user' && user.section_id != section.id"
                                        >
                                            Rating
                                        </v-btn>
                                        </div>
                                    </td>  
                                </tr>      
                                
                                <tr v-else> 
                                    <td colspan="2"></td>
                                    <td class="text-center px-4 py-2 p-2 mr-2 border border-solid" 
                                     >

                                       <div>
                                         <v-btn prepend-icon="mdi-eye" class="mr-3" size="small" @click="goViewPage(division_section.id, section.id)"
                                            :disabled="user.account_type == 'user'"
                                        >
                                            View 
                                        </v-btn>
          
                                        <v-btn
                                            @click="rating(division_section.id)" 
                                            prepend-icon="mdi-file" color="yellow" 
                                            size="small"
                                            :disabled="user.account_type == 'user'"
                                        >
                                            Rating
                                        </v-btn>
                                        </div>
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
          :account="account"
          :selected_division="selected_division"
          :data="props"
          @input="showDivisionModal"
          @reloadAccounts="reloadAccounts"
      ></ModalForm>
    </AppLayout>


</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
   table {
    border-collapse: collapse;
    width: 100%; /* Optional: Set a width for the table */
    border: none;
  }
  tr, th,td {
    border: 1px solid none; /* Optional: Add a border for better visibility */
    padding: 8px; /* Optional: Add padding for better spacing */
  }


  
</style>
