<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import VueMultiselect from "vue-multiselect";
    import { router } from '@inertiajs/vue3';
    import { reactive, ref, watch } from 'vue';
    import QrcodeVue from "qrcode.vue";
    import { Printd } from "printd";
    import CSFPrint from '@/Pages/Libraries/Division-Sections/Form/PrintCSF.vue'; // Updated path
   
    const props = defineProps({
        division: Object, 
        service: Object, 
        sub_services: Object, 
        user: Object,
    });

    const form = reactive({
        generated_url: null,
        selected_sub_service: '', 
        sub_service_id: '', 
        client_type: ''
    });

    const qr_link_type = ref(null);
    const generated = ref(false);
    const baseURL = window.location.origin;

    const generateURL = async (sub_service, sub_service_id) => { 
        generated.value = true;

        if(props.service && Array.isArray(props.service.data) && props.service.data.length > 0 && props.service.data[0]) {
            // Fix the syntax error by removing the semicolon
            if(sub_service) {
                if(sub_service_id) {
                    qr_link_type.value = 1.1;
                    form.generated_url = baseURL + '/divisions/csf?' +
                                    'office_id=' + props.user.office_id + 
                                    '&division_id=' + props.division.id + 
                                    '&service_id=' +  props.service.data[0].id +
                                    '&sub_service_id=' + sub_service.id +
                                    '&sub_service_id=' + sub_service_id.id;
                } else {
                    qr_link_type.value = 1.2;
                    form.generated_url = baseURL + '/divisions/csf?' +
                                    'office_id=' + props.user.office_id + 
                                    '&division_id=' + props.division.id + 
                                    '&service_id=' +  props.service.data[0].id +
                                    '&sub_service_id=' + sub_service.id;
                }
            } else {
                qr_link_type.value = 0;
                form.generated_url = baseURL + '/divisions/csf?' +
                                'office_id=' + props.user.office_id + 
                                '&division_id=' + props.division.id + 
                                '&service_id=' +  props.service.data[0].id;
            }
        } else {
            qr_link_type.value = 1;
            form.generated_url = baseURL + '/divisions/csf?' +
                                'office_id=' + props.user.office_id + 
                                '&division_id=' + props.division.id;
        }
    }

    const copied = ref(false);
    // Function to copy text to clipboard
    const copyToClipboard = () => {
        // Create a temporary textarea element
        const textarea = document.createElement('textarea');
        textarea.value = form.generated_url;

        // Append the textarea to the document
        document.body.appendChild(textarea);

        // Select and copy the text
        textarea.select();
        document.execCommand('copy');

        // Remove the textarea from the document
        document.body.removeChild(textarea);
        copied.value = true;

        setTimeout(() => {
            copied.value = false;
        }, 2000);
    };

    const is_printing = ref(false);
    const printCSFForm = async () => {
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

            /* Styles for v-text-field */
              .v-text-field {
              display: inline-block;
              position: relative;
              width: 100%;
              max-width: 700px;
              padding: 0.625rem 0.75rem;
              font-size: 1rem;
              line-height: 1.5;
              color: rgba(0, 0, 0, 0.87);
              background-color: #fff;
              background-clip: padding-box;
              border: 1px solid #ced4da;
              border-radius: 0.25rem;
              transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
              }
          `;

        d.print(document.querySelector(".print-id"), [css]);
    };
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
               <!-- <v-breadcrumbs :items="['Dashboard', 'Division Services', service.service_name]"></v-breadcrumbs> -->
               View
            </h2>
        </template>
        <div class="py-10" style="margin-left:80px; margin-right:80px">
            <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <v-card class="mb-3">
                        <v-card-title class="m-3" >
                            <div v-if="division">
                                DIVISION: {{ division.division_name }}
                            </div>
                            <v-divider class="border-opacity-100"></v-divider>
                            <div v-if="service && Array.isArray(service.data) && service.data.length > 0 && service.data[0]">
                                SERVICE: {{ service.data[0].service_name }}
                            </div>
                        </v-card-title>
                    </v-card>
                    <v-card class="mb-3" height="600px" >
                      <v-card-body class="overflow-visible">
                        <v-row class="p-5 " key="">
                        <!-- You might want to uncomment this if you need the selection functionality -->
                        <!-- <v-col class="my-auto ml-5" v-if="service.data && service.data[0] && service.data[0].sub_services && service.data[0].sub_services.length > 0" >
                            <vue-multiselect
                                v-model="form.selected_sub_service"
                                prepend-icon="mdi-account"
                                :options="service.data[0].sub_services"
                                :multiple="false"
                                placeholder="Select Sub Service*"
                                label="sub_service_name"
                                track-by="sub_service_name"
                                :allow-empty="false"
                            >         
                            </vue-multiselect>           
                        </v-col> -->

                        <v-col class="my-auto" v-if="sub_services && sub_services.length > 0 && form.selected_sub_service" >
                            <vue-multiselect
                                v-model="form.sub_service_id"
                                :options="sub_services"
                                :multiple="false"
                                placeholder="Select Sub Service"
                                label="service_name"
                                track-by="service_name"
                                :allow-empty="false"
                            >
                            </vue-multiselect>          
                        </v-col>

                        <v-col class="my-auto text-right" >                            
                            <v-btn 
                            prepend-icon="mdi-plus"
                            @click="generateURL(form.selected_sub_service, form.sub_service_id)" >Generate URL </v-btn>           
                        </v-col>
                        </v-row>
                        
                        <div class="p-5 m-5" label="URL">
                            <v-row>
                                <v-col cols="10" md="11">
                                <v-text-field v-model="form.generated_url" variant="outlined" label="URL" readonly></v-text-field>                                       
                                </v-col>
                                <v-col>
                                <v-btn color="none" icon="mdi-content-copy" @click="copyToClipboard()" >
                                    
                                </v-btn>
                                <span v-if="copied">copied</span>
                                </v-col>
                            </v-row>
                        </div>

                        <div style="display: flex;justify-content: center;align-items: center;" class="mb-10">
                             <QrcodeVue
                                v-if="qr_link_type == 0"
                                :render-as="'svg'"
                                :value="`${baseURL}/divisions/csf?office_id=${user.office_id}&division_id=${division.id}&service_id=${service.data[0].id}`" 
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="
                                border: 3px #ffffff solid;
                                width: 300px;
                                height: 300px;
                                "
                            />
                            <QrcodeVue
                                v-if="qr_link_type == 1"
                                :render-as="'svg'"
                                :value="`${baseURL}/divisions/csf?office_id=${user.office_id}&division_id=${division.id}`"
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="
                                border: 3px #ffffff solid;
                                width: 300px;
                                height: 300px;
                                "
                            />
                            <QrcodeVue
                                v-if="qr_link_type == 1.1"
                                :render-as="'svg'"
                                :value="`${baseURL}/divisions/csf?office_id=${user.office_id}&division_id=${division.id}&service_id=${service.data[0].id}&sub_service_id=${form.selected_sub_service.id}`" 
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="
                                border: 3px #ffffff solid;
                                width: 300px;
                                height: 300px;
                                "
                            />

                             <QrcodeVue
                                v-if="qr_link_type == 1.2"
                                :render-as="'svg'"
                                :value="`${baseURL}/divisions/csf?office_id=${user.office_id}&division_id=${division.id}&service_id=${service.data[0].id}&sub_service_id=${form.selected_sub_service.id}&sub_service_id=${form.sub_service_id.id}`" 
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="
                                border: 3px #ffffff solid;
                                width: 300px;
                                height: 300px;
                                "
                            />

                            <QrcodeVue
                                v-if="qr_link_type == 2"
                                :render-as="'svg'"
                                :value="`${baseURL}/divisions/csf?office_id=${user.office_id}&division_id=${division.id}&service_id=${service.data[0].id}&sub_service_id=${form.selected_sub_service.id}`" 
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="
                                border: 3px #ffffff solid;
                                width: 300px;
                                height: 300px;
                                "
                            />

                            <QrcodeVue
                                v-if="qr_link_type == 3"
                                :render-as="'svg'"
                                :value="`${baseURL}/divisions/csf?office_id=${user.office_id}&division_id=${division.id}&service_id=${service.data[0].id}`" 
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="
                                border: 3px #ffffff solid;
                                width: 300px;
                                height: 300px;
                                "
                            />

                        </div>
                        
                      </v-card-body>
                    </v-card>
                </div>
            </div>
        </div>
        <CSFPrint v-if="generated == true" :is_printing="is_printing" :form="form" :data="props" />
    </AppLayout>
</template>


<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
   table {
    border-collapse: collapse;
    width: 100%; /* Optional: Set a width for the table */
  }
  tr, th, td {
    border: 1px solid rgb(145, 139, 139); /* Optional: Add a border for better visibility */
    padding: 8px; /* Optional: Add padding for better spacing */
  }
</style>