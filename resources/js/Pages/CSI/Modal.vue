<script setup lang="ts">
import { reactive, watch, ref, onMounted } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
import VueMultiselect from "vue-multiselect";
import BySectionMonthlyReport from '@/Pages/CSI/Monthly/BySectionMonthly.vue';
import { Printd } from "printd";
const emit = defineEmits(["input"]);
const props = defineProps({
    form: {
        type: Object,
        default: null,
    },
    assignatorees: {
        type: Object,
        default: null,
    },
    user: {
        type: Object,
        default: null,
    },
    value: {
        type: Boolean,
        default: false,
    },
    data: {
        type: Boolean,
    },
    generated:{
        type: Boolean,
    }

});


const show_form_modal = ref(false);
watch(
    () => props.value,
    (value) => {
        show_form_modal.value = value;
    }
);


const form_assignatorees = reactive({
    prepared_by: props.user,
    noted_by:{},
});




const printPReview = async () => {
   
   
    emit("input", false);

    form.id= '';
    form.name='';
    form.designation='';
};

const closeDialog = (value) => {
    emit("input", value);
};

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
            padding: 3px; /* Optional: Add padding for better spacing */
          }
          .page-break {
            page-break-before: always; /* or page-break-after: always; */
          }

        `;

       d.print(document.querySelector(".print-id"), [css]);

        emit("input", false);
};


</script>

<template>
    <v-dialog v-model="show_form_modal" width="800" height="800" scrollable persistent>
        <v-card>
            <v-card-title class="bg-indigo">
                <span class="text-h5">Select Assignatoree</span>
            </v-card-title>
            <v-card-text>

                
                <v-row style="margin-bottom:-30px;">
                     <v-col>
                        <label >Prepared By:</label>
                        <v-text-field size="small" variant="text" readonly>
                            {{ user.name }}
                        </v-text-field>
                               
                  </v-col>
                </v-row>
                  <v-row style="margin-top:-50px;">
                    <v-col>
                        <label >Noted By:</label>
                        <vue-multiselect
                          v-model="form_assignatorees.noted_by"
                          :options="assignatorees"
                          :multiple="false"
                          placeholder="Noted By:"
                          label="name"
                          track-by="name"
                          :allow-empty="false"
                          class="ml-5"
                        >
                        </vue-multiselect>          
                  </v-col>
                </v-row>

            </v-card-text>
            <v-spacer></v-spacer>
            <v-card-action>
                <v-divider></v-divider>
                <div style="text-align: end">
                    <v-btn
                        class="ma-2"
                        color="blue-grey-lighten-2"
                        @click="closeDialog(false)"
                    >
                        <v-icon start icon="mdi-cancel"></v-icon>
                        Cancel
                    </v-btn>
                    <v-btn
                        class="ma-2"
                        color="green-darken-1"
                        type="button"
                        @click="printCSIReport()"
                    >
                        Print Preview
                        <v-icon end icon="mdi-print"></v-icon>
                    </v-btn>
                </div>
            </v-card-action>
        </v-card>
    </v-dialog>

    <!-- Printouts-->
    <BySectionMonthlyReport v-if="form.csi_type == 'By Month'" :form="form"  :data="data" :form_assignatorees="form_assignatorees" />
 
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
