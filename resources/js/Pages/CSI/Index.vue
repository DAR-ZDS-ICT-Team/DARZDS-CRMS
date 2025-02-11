<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MonthlyContent from '@/Pages/CSI/Monthly/Content.vue';
import Q1Content from '@/Pages/CSI/Quarterly/Contents/Q1Content.vue';
import Q2Content from '@/Pages/CSI/Quarterly/Contents/Q2Content.vue';
import Q3Content from '@/Pages/CSI/Quarterly/Contents/Q3Content.vue';
import Q4Content from '@/Pages/CSI/Quarterly/Contents/Q4Content.vue';
import YearlyContent from '@/Pages/CSI/Yearly/Content.vue';
import BySectionQ1Report from '@/Pages/CSI/Quarterly/Printouts/BySectionQuarter1.vue';
import BySectionQ2Report from '@/Pages/CSI/Quarterly/Printouts/BySectionQuarter2.vue';
import BySectionQ3Report from '@/Pages/CSI/Quarterly/Printouts/BySectionQuarter3.vue';
import BySectionQ4Report from '@/Pages/CSI/Quarterly/Printouts/BySectionQuarter4.vue';
import BySectionYearlyReport from '@/Pages/CSI/Yearly/BySectionYearly.vue';
import ModalForm from '@/Pages/CSI/Modal.vue';
import VueMultiselect from "vue-multiselect";
import { reactive, ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2';
import { Printd } from "printd";


const props = defineProps({
    division: Object, 
    section: Object,
    sub_section: Object,
    sub_section_types: Object,

    //Monthly
    y_totals: Object,
    grand_vs_total: Number,
    grand_s_total: Number,
    grand_n_total:Number,
    grand_d_total: Number,
    grand_vd_total: Number,
    x_totals: Object,
    x_grand_total: Object,
    likert_scale_rating_totals: Object,
    lsr_grand_total: Number,
    importance_rate_score_totals: Object,
    x_importance_totals: Object,
    importance_ilsr_totals: Object,
    gap_totals: Object,
    gap_grand_total: Number,
    wf_totals: Object,
    gap_grand_total: Number,
    ss_totals: Object,
    ws_totals: Object,
    total_respondents: Number,
    total_vss_respondents: Number,
    percentage_vss_respondents: Number,
    customer_satisfaction_rating: Number,
    customer_satisfaction_index: Number,
    net_promoter_score: Number,
    percentage_promoters: Number,
    percentage_detractors: Number,
    total_comments: Number,
    total_complaints: Number,
    comments: Object,

    
    // Quarterly 
    cc_data: Object,
    user: Object,
    assignatorees: Object,
    dimensions: Object,
    division : Object,
    section: String,
    respondents_list : Object,
    trp_totals: Number,
    grand_total_raw_points: Number,
    vs_grand_total_raw_points: Number,
    vs_grand_total_raw_points: Number,
    s_grand_total_raw_points: Number,
    ndvd_grand_total_raw_points: Object,
    p1_total_scores: Object,
    vs_grand_total_score: Object,
    s_grand_total_score: Object,
    ndvd_grand_total_score: Object,
    grand_total_score: Number,
    lsr_totals: Object,
    lsr_grand_total: Number,
    lsr_average: Number,
    vs_totals: Object,
    s_totals: Object,
    n_totals: Object,
    d_totals: Object,
    vd_totals: Object,
    grand_totals: Object,
    first_month_total_vs_respondents: Object,
    second_month_total_vs_respondents: Object,
    third_month_total_vs_respondents: Object,
    first_month_total_s_respondents: Object,
    second_month_total_s_respondents: Object,
    third_month_total_s_respondents: Object,
    first_month_total_ndvd_respondents: Object,
    second_month_total_ndvd_respondents: Object,
    third_month_total_ndvd_respondents: Object,
    first_month_total_respondents: Object,
    second_month_total_respondents: Object,
    third_month_total_respondents: Object,
    total_respondents: Number,
    total_vss_respondents: Number,
    percentage_vss_respondents: Number,
    total_promoters: Number,
    total_detractors: Number,
    vi_totals: Object,
    i_totals: Object,
    mi_totals: Object,
    si_totals: Object,
    nai_totals: Object,
    i_grand_totals: Object,
    i_trp_totals: Object,
    i_grand_total_raw_points: Object,
    vi_grand_total_raw_points: Object,
    s_grand_total_raw_points: Object,
    misinai_grand_total_raw_points: Object,
    i_total_scores: Object,
    vi_grand_total_score: Number,
    i_grand_total_score: Number,
    misinai_grand_total_score: Number,
    percentage_promoters: Number,
    first_month_percentage_promoters: Number,
    second_month_percentage_promoters: Number,
    third_month_percentage_promoters: Number,
    average_percentage_promoters: Number,
    first_month_percentage_detractors: Number,
    second_month_percentage_detractors: Number,
    third_month_percentage_detractors: Number,
    average_percentage_detractors: Number,
    first_month_net_promoter_score: Number,
    second_month_net_promoter_score: Number,
    third_month_net_promoter_score: Number,
    average_percentage_detractors: Number,
    first_month_net_promoter_score: Number,
    second_month_net_promoter_score: Number,
    third_month_net_promoter_score: Number,
    third_month_net_promoter_score: Number,
    ave_net_promoter_score: Number,
    customer_satisfaction_rating: Number,
    csi: Number,
    first_month_csi: Number,
    second_month_csi: Number,
    third_month_csi: Number,
    first_month_vs_grand_total: Number,
    second_month_vs_grand_total:Number,
    third_month_vs_grand_total:Number,
    first_month_s_grand_total: Number,
    second_month_s_grand_total: Number,
    third_month_s_grand_total: Number,
    first_month_ndvd_grand_total: Number,
    second_month_ndvd_grand_total: Number,
    third_month_ndvd_grand_total: Number,
    first_month_grand_total: Number,
    second_month_grand_total: Number,
    third_month_grand_total: Number,
    total_comments: Number,
    total_complaints: Number,
    comments: Object,

    // Monthly and Date
    customer_satisfaction_index: Number,
    net_promoter_score: Number,
    
    // Yearly

    percentage_promoters: Number,
    q1_percentage_promoters: Number,
    q2_percentage_promoters: Number,
    q3_percentage_promoters: Number,
    q4_percentage_promoters: Number,
    average_percentage_promoters: Number,
    q1_percentage_detractors: Number,
    q2_percentage_detractors: Number,
    q3_percentage_detractors: Number,
    q4_percentage_detractors: Number,
    average_percentage_detractors: Number,
    q1_net_promoter_score: Number,
    q2_net_promoter_score: Number,
    q3_net_promoter_score: Number,
    q4_net_promoter_score: Number,
    ave_net_promoter_score: Number,
    customer_satisfaction_rating: Number,
    q1_csi: Number,
    q2_csi: Number,
    q3_csi: Number,
    q4_csi: Number,
    csi: Number,
});


const form = reactive({
  division: null,
  section:  null,
  section_id: null,

  selected_sub_section: [],

  // form type if all or per section
  form_type: null,

  //by date
  date_from: null,
  date_to: null,

  // by date , monthly ,querterly or yearly
  csi_type: null,

  // by month and year
  selected_month: null,
  selected_year: null,

  //by quarter
  selected_quarter: null,

  // for HR case only
  client_type: null,

  // other properties
  sex:null , 
  age_group:null,

});


const view_form = reactive({
  generated_url: null,
});


const generated = ref(false);

//get year
const years = computed(() => {
    const currentYear = new Date().getFullYear();
    const last9Years = Array.from({ length: 9 }, (_, index) => (currentYear - index).toString());
    return last9Years;
});

const months = [
    'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL',
    'MAY', 'JUNE', 'JULY', 'AUGUST',
    'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
];

const currentYear = ref(getCurrentYear());

  function getCurrentYear() {
      return new Date().getFullYear().toString();
  }

//get month
  const currentMonth = ref(getCurrentMonth());

  function getCurrentMonth() {
      const currentDate = new Date();
      return months[currentDate.getMonth()];
  }

  onMounted(() => {
      form.selected_month = currentMonth.value;
      form.selected_year = currentYear.value;
      generated.value == false;
  });


  const generateCSIReport = async (division, section) => {
   generated.value = true;
   form.division = division;

    if(section.data && section.data.length > 0 && section.data[0]){
      form.section_id = section.data[0].id;
      form.section = section;
    }
    
    if(form.csi_type == 'By Date'){
      if(form.date_from && form.date_to){
            router.post('/csi/generate', form , { preserveState: true, preserveScroll: true})
      }
      else{ 
        Swal.fire({
              title: "Error",
              icon: "error",
              text: "Please fill up Date From and Date To field."           
          });
      }
    }
    else if(form.csi_type == 'By Month'){
          form.selected_quarter = "";
          router.post('/csi/generate', form , { preserveState: true, preserveScroll: true})
    }
    else if(form.csi_type == 'By Quarter'){
          form.selected_month = "";
          if(form.selected_quarter){
              router.post('/csi/generate', form , { preserveState: true, preserveScroll: true})
          }
          else{ 
            Swal.fire({
                  title: "Error",
                  icon: "error",
                  text: "Please select a quarter first!"           
              });
          }
    }
      else if(form.csi_type == 'By Year/Annual'){
          form.selected_quarter = "";
          if(form.selected_year ){
             router.post('/csi/generate', form , { preserveState: true, preserveScroll: true})
          }
          else{         
              Swal.fire({
                  title: "Error",
                  icon: "error",
                  text: "Please select year first!"           
              });
          }     
      }

    
  };

  function refresh() {
      window.history.back()
  }






  watch(
    () => form.csi_type,
    (value) => {
          if(value == ''){
            form.selected_sub_section = null;
          }
    }
);



const sex_options = [
  'Male', 
  'Female' , 
  'Prefer not to say'
];


const is_printing = ref(false);
const printCSIReport = async () => {
      is_printing.value = true;
      //  router.get('/generate-pdf', form , { preserveState: true, preserveScroll: true})
      //Create an instance of Printd
        let d = await new Printd();
        let css = ` 
          @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;800&family=Roboto:wght@100;300;400;500;700;900&display=swap');
          * {
              font-family: 'Time New Roman'
          }
          .new-page {
              page-break-before: always;
          }
          .th-color{
              background-color: #8fd1e8;
          }
          .text-center{
            text-align: center;
          }
          .text-right{
            text-align:end
          }
          table {
            border-collapse: collapse;
            width: 100%; /* Optional: Set a width for the table */
          }

          tr, th, td {
            border: 1px solid rgb(145, 139, 139); /* Optional: Add a border for better visibility */
            padding: 1px; /* Optional: Add padding for better spacing */
          }
          .page-break {
            page-break-before: always; /* or page-break-after: always; */
          }
          .text-left{
            text-align: left;
          }
          .text-center{
            text-align: center;
          }
          .bg-blue{
            background: blue;
          }

        `;

       d.print(document.querySelector(".print-id"), [css]);

};

 const show_modal = ref(false);
//For Modal Print Preview
 const showPrintPreviewModal = async(is_show) => {
      show_modal.value = is_show;
  };
  
</script>

<template>
    <AppLayout title="Dashboard" class="overflow-visible">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customer Satisfaction Index 
            </h2>
        </template>

        
        <div class="py-10 overflow-visible"  style="margin-left:80px; margin-right:80px" >
            <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8  overflow-visibl">
                <div class="bg-white shadow-xl sm:rounded-lg overflow-visible">
                   <v-card class="mb-5 overflow-visible" >
                    <v-card-title class="m-3">
                      <div v-if="division">
                        DIVISION : {{ division.division_name }}
                      </div>
                      <v-divider class="border-opacity-100"></v-divider>
                      <div v-if="section && Array.isArray(section.data) && section.data.length > 0 && section.data[0]">
                        SECTION : {{ section.data[0].section_name }}
                      </div>
                     </v-card-title>
                    </v-card>

                    <v-card class="overflow-visible mb-5" >   
                          <v-divider class="border-opacity-100"></v-divider>
                          <v-row class="p-3 overflow-visible" >
                              <v-col class="my-auto overflow-visible">
                                <div class="my-auto overflow-visible"> 
                                   <vue-multiselect
                                      v-model="form.csi_type"
                                      prepend-icon="mdi-account"
                                      :options="['By Date','By Month', 'By Quarter', 'By Year/Annual']"
                                      :multiple="false"
                                      placeholder="Select Type*"
                                      :allow-empty="false"
                                    >         
                                    </vue-multiselect>        

                                  </div>
                              </v-col>
                              
                              <!-- <v-col class="my-auto"  v-if="section.length && section.data[0].id == 8 || user.account_type == 'planning'" >
                                   <vue-multiselect
                                      v-model="form.client_type"
                                      prepend-icon="mdi-account"
                                      :options="['Internal', 'External']"
                                      :multiple="false"
                                      placeholder="Select Client Type"
                                      :allow-empty="true"
                                    >         
                                    </vue-multiselect>       
                              </v-col> -->


                              <!-- <v-col class="my-auto " v-if=" section.length && section.data[0].sub_sections.length > 0" >
                                    <vue-multiselect
                                      v-model="form.selected_sub_section"
                                      prepend-icon="mdi-account"
                                      :options="section.data[0].sub_sections"
                                      :multiple="false"
                                      placeholder="Select Sub Section*"
                                      label="sub_section_name"
                                      track-by="sub_section_name"
                                      :allow-empty="false"
                                      :disabled="generated"
                                    >         
                                    </vue-multiselect>           
                              </v-col> -->
                             
                          </v-row>
    
                          <v-divider class="border-opacity-100"></v-divider>

                          <v-row class="p-3 overflow-visible" v-if="user.account_type == 'planning'" >
                            <v-col class="my-auto" >
                                <vue-multiselect
                                    v-model="form.sex"
                                    prepend-icon="mdi-account"
                                    :options="['Male','Female', 'Prefer not to say']"
                                    :multiple="false"
                                    placeholder="Select Sex"
                                    :allow-empty="true"
                                  >         
                                </vue-multiselect>      
                            </v-col>
                            <v-col class="my-auto" >
                                <vue-multiselect
                                    v-model="form.age_group"
                                    prepend-icon="mdi-account"
                                    :options="['19 or lower','20-34','35-49','50-64','60+', 'Prefer not to say']"
                                    :multiple="false"
                                    placeholder="Select Age Group"
                                    :allow-empty="true"
                                  >         
                                </vue-multiselect>                
                              </v-col>
                          </v-row>
                          <v-divider class="border-opacity-100"></v-divider>
                          

                          <v-card-body class="overflow-visible mb-2" >

                              <v-row class="p-3" v-if="form.csi_type == 'By Date'" >
                                  <v-col class="my-auto">
                                      <v-text-field
                                          label="Select Date From"
                                          placeholder="Date From"
                                          variant="outlined"
                                          size="x-small"
                                          type="date"
                                          v-model="form.date_from"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col>
                                      <v-text-field
                                          label="Select Date To"
                                          placeholder="Date To"
                                          variant="outlined"
                                          size="x-small"
                                          type="date"
                                          v-model="form.date_to"
                                      ></v-text-field>
                                  </v-col>
                                  <v-col class="ml-5">
                                    <v-btn @click="generateCSIReport(division, section)" >Generate</v-btn>
                                    <v-btn @click="refresh()" icon="mdi-refresh" v-if="generated" variant="text"></v-btn>
                                  </v-col>

                              </v-row>

                              <v-row class="p-3" v-if="form.csi_type == 'By Month'">
                                  <v-col class="my-auto">
                                        <v-select v-model="form.selected_month" 
                                              class="m-3" label="Select Month" 
                                              variant="outlined" 
                                              :items="months" 
                                              outlined="none"> 
                                        </v-select>
                                  </v-col> 
                                  <v-col class="my-auto">
                                      <v-select v-model="form.selected_year" 
                                              class="m-3" label="Select Year" 
                                              variant="outlined" 
                                              :items="years" 
                                              outlined="none"> 
                                        </v-select>
                                  </v-col>   

                                  <v-col class="ml-5 mt-3">
                                    <v-btn @click="generateCSIReport(division, section)" >Generate</v-btn>
                                    <v-btn @click="refresh()" icon="mdi-refresh" v-if="generated" variant="text"></v-btn>
                                  </v-col>
                                <v-col class="text-end mr-5 m-3">
                                  <v-btn  :disabled="generated == false" prepend-icon="mdi-printer" @click="showPrintPreviewModal(true)">Print</v-btn>
                                </v-col>
                              </v-row>

                                <v-row class="p-3" v-if="form.csi_type == 'By Quarter'">
                                  <v-col class="my-auto">
                                        <v-combobox v-model="form.selected_quarter" 
                                              class="m-3" label="Select Quarter" 
                                              variant="outlined" 
                                              :items="['FIRST QUARTER', 'SECOND QUARTER', 'THIRD QUARTER', 'FOURTH QUARTER']" 
                                              outlined="none"> 
                                        </v-combobox>
                                  </v-col> 
                                  <v-col class="my-auto">
                                      <v-combobox v-model="form.selected_year" 
                                              class="m-3" label="Select Year" 
                                              variant="outlined" 
                                              :items="years" 
                                              outlined="none"> 
                                        </v-combobox>
                                  </v-col>   

                                  <v-col class="ml-5 mt-3">
                                    <v-btn  @click="generateCSIReport(division, section)" >Generate</v-btn>
                                    <v-btn @click="refresh()" icon="mdi-refresh" v-if="generated" variant="text"></v-btn>
                                  </v-col>
                                <v-col class="text-end mr-5 m-3">
                                  <v-btn :disabled="generated == false" prepend-icon="mdi-printer" @click="printCSIReport()">Print</v-btn>
                                </v-col>
                              </v-row>
                                <v-row class="p-3" v-if="form.csi_type == 'By Year/Annual'">
                                  <v-col class="my-auto">
                                      <v-combobox v-model="form.selected_year" 
                                              class="m-3" label="Select Year" 
                                              variant="outlined" 
                                              :items="years" 
                                              outlined="none"> 
                                        </v-combobox>
                                  </v-col>   

                                  <v-col class="ml-5 mt-3">
                                    <v-btn @click="generateCSIReport(division, section)" >Generate</v-btn>
                                    <v-btn @click="refresh()" icon="mdi-refresh" v-if="generated" variant="text"></v-btn>
                                  </v-col>
                                <v-col class="text-end mr-5 m-3">
                                  <v-btn  :disabled="generated == false" prepend-icon="mdi-printer" @click="printCSIReport()">Print</v-btn>
                                </v-col>
                              </v-row>

                              </v-card-body>
                    </v-card>

                  <!-- Content Preview-->
                  <MonthlyContent v-if="form.csi_type == 'By Month' && generated == true  || form.csi_type == 'By Date' && generated == true" :form="form"  :data="props" />
                  <Q1Content v-if="form.csi_type == 'By Quarter' && form.selected_quarter == 'FIRST QUARTER' && generated == true "  :form="form"  :data="props" />
                  <Q2Content v-if="form.csi_type == 'By Quarter' && form.selected_quarter == 'SECOND QUARTER' && generated == true" :form="form"  :data="props" />
                  <Q3Content v-if="form.csi_type == 'By Quarter' && form.selected_quarter == 'THIRD QUARTER' && generated == true"  :form="form"  :data="props" />
                  <Q4Content v-if="form.csi_type == 'By Quarter' && form.selected_quarter == 'FOURTH QUARTER' && generated == true" :form="form"  :data="props" />
                  <YearlyContent v-if="form.csi_type == 'By Year/Annual' && generated == true"  :form="form"  :data="props"  />
                  
                    <!-- End Content Preview-->


                  <!-- QUARTER AND YEARLY PRINTOUTS Preview-->
                  <BySectionQ1Report v-if="form.csi_type == 'By Quarter' && form.selected_quarter == 'FIRST QUARTER'" :form="form"  :data="props" />
                  <BySectionQ2Report v-if="form.csi_type == 'By Quarter' && form.selected_quarter == 'SECOND QUARTER'"  :form="form"  :data="props" />
                  <BySectionQ3Report v-if="form.csi_type == 'By Quarter' && form.selected_quarter == 'THIRD QUARTER'"  :form="form"  :data="props" />
                  <BySectionQ4Report v-if="form.csi_type == 'By Quarter' && form.selected_quarter == 'FOURTH QUARTER'"  :form="form"  :data="props" />
                  <BySectionYearlyReport v-if="form.csi_type == 'By Year/Annual'"  :form="form"  :data="props"/>
                 
                  <!-- Modal for Print Preview -->
                  <ModalForm 
                      v-if="generated" 
                      :value="show_modal"
                      :form="form"  
                      :assignatorees="assignatorees"
                      :user="user"
                      @input="showPrintPreviewModal"  
                      :data="props"
                     />
                  
                 
                </div>
            </div>
        </div>

    </AppLayout>

   
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>



