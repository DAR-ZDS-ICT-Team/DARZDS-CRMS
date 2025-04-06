<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import VueMultiselect from "vue-multiselect";
    import { router } from '@inertiajs/vue3';
    import { reactive, ref, watch } from 'vue';
    import QrcodeVue from "qrcode.vue";
    import { Printd } from "printd";
    import CSFPrint from '@/Pages/Libraries/Division-Sections/Form/PrintCSF.vue';
   
    const props = defineProps({
        division: Object, 
        section: Object,
        services: Object,
        service: Object,
        user: Object,
    });

    const form = reactive({
        generated_url: null,
        selected_service: '',
        client_type: ''
    });

    const qr_link_type = ref(null);
    const generated = ref(false);
    
    const generateURL = async (service = null) => {
        generated.value = true;

        // Get base URL
        const baseURL = window.location.origin;
        
        // For a specific service in a section
        if (props.section && props.section.data[0] && service) {
            qr_link_type.value = 1;
            form.generated_url = `${baseURL}/divisions/csf?` +
                            `office_id=${props.user.office_id}` + 
                            `&division_id=${props.division.id}` + 
                            `&section_id=${props.section.data[0].id}` +
                            `&service_id=${service.id}` +
                            `&client_type=${form.client_type}`;
        }
        // For a specific service directly in a division (no section)
        else if (props.division && service) {
            qr_link_type.value = 2;
            form.generated_url = `${baseURL}/divisions/csf?` +
                            `office_id=${props.user.office_id}` + 
                            `&division_id=${props.division.id}` + 
                            `&service_id=${service.id}` +
                            `&client_type=${form.client_type}`;
        }
        // For a section with no specific service selected
        else if (props.section && props.section.data[0]) {
            qr_link_type.value = 3;
            form.generated_url = `${baseURL}/divisions/csf?` +
                            `office_id=${props.user.office_id}` + 
                            `&division_id=${props.division.id}` + 
                            `&section_id=${props.section.data[0].id}` +
                            `&client_type=${form.client_type}`;
        }
        // For a division with no section or specific service selected
        else {
            qr_link_type.value = 4;
            form.generated_url = `${baseURL}/divisions/csf?` +
                            `office_id=${props.user.office_id}` + 
                            `&division_id=${props.division.id}` +
                            `&client_type=${form.client_type}`;
        }
    }

    const baseURL = window.location.origin;
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
                width: 100%;
            }

            tr, th, td {
                border: 1px solid rgb(145, 139, 139);
                padding: 3px;
            }
            .page-break {
                page-break-before: always;
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
               View
            </h2>
        </template>
        <div class="py-10" style="margin-left:80px; margin-right:80px">
            <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <v-card class="mb-3">
                        <v-card-title class="m-3">
                            <div v-if="division">
                                DIVISION: {{ division.division_name }}
                            </div>
                            <v-divider class="border-opacity-100"></v-divider>
                            <div v-if="section && section.data && section.data[0]">
                                SECTION: {{ section.data[0].section_name }}
                            </div>
                            <div v-if="service">
                                SERVICE: {{ service.service_name }}
                            </div>
                        </v-card-title>
                    </v-card>
                    <v-card class="mb-3" height="600px">
                      <v-card-body class="overflow-visible">
                        <v-row class="p-5" key="">
                            <!-- If we're viewing a section, show available services -->
                            <v-col class="my-auto ml-5" v-if="section && section.data && section.data[0] && section.data[0].services && section.data[0].services.length > 0">
                                <vue-multiselect
                                    v-model="form.selected_service"
                                    prepend-icon="mdi-account"
                                    :options="section.data[0].services"
                                    :multiple="false"
                                    placeholder="Select Service*"
                                    label="service_name"
                                    track-by="service_name"
                                    :allow-empty="false"
                                >         
                                </vue-multiselect>           
                            </v-col>
                            
                            <!-- If we're viewing a division with direct services -->
                            <v-col class="my-auto ml-5" v-else-if="division && division.services && division.services.length > 0">
                                <vue-multiselect
                                    v-model="form.selected_service"
                                    prepend-icon="mdi-account"
                                    :options="division.services"
                                    :multiple="false"
                                    placeholder="Select Service*"
                                    label="service_name"
                                    track-by="service_name"
                                    :allow-empty="false"
                                >         
                                </vue-multiselect>           
                            </v-col>

                            <!-- Client type selection field -->
                            <v-col class="my-auto">
                                <v-select
                                    v-model="form.client_type"
                                    :items="['Internal', 'External', 'Government', 'Business']"
                                    label="Client Type"
                                    placeholder="Select Client Type"
                                    required
                                ></v-select>
                            </v-col>

                            <v-col class="my-auto text-right">                            
                                <v-btn 
                                    :disabled="!form.client_type" 
                                    prepend-icon="mdi-plus"
                                    @click="generateURL(form.selected_service)">
                                    Generate URL
                                </v-btn>           
                            </v-col>
                        </v-row>
                        
                        <div class="p-5 m-5" label="URL">
                            <v-row>
                                <v-col cols="10" md="11">
                                    <v-text-field v-model="form.generated_url" variant="outlined" label="URL" readonly></v-text-field>                                       
                                </v-col>
                                <v-col>
                                    <v-btn color="none" icon="mdi-content-copy" @click="copyToClipboard()"></v-btn>
                                    <span v-if="copied">copied</span>
                                </v-col>
                            </v-row>
                        </div>

                        <div style="display: flex; justify-content: center; align-items: center;" class="mb-10">
                            <!-- QR for section with service -->
                            <QrcodeVue
                                v-if="qr_link_type === 1 && form.selected_service"
                                :render-as="'svg'"
                                :value="form.generated_url"
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="border: 3px #ffffff solid; width: 300px; height: 300px;"
                            />
                            
                            <!-- QR for direct division service -->
                            <QrcodeVue
                                v-else-if="qr_link_type === 2 && form.selected_service"
                                :render-as="'svg'"
                                :value="form.generated_url"
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="border: 3px #ffffff solid; width: 300px; height: 300px;"
                            />
                            
                            <!-- QR for entire section -->
                            <QrcodeVue
                                v-else-if="qr_link_type === 3"
                                :render-as="'svg'"
                                :value="form.generated_url"
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="border: 3px #ffffff solid; width: 300px; height: 300px;"
                            />
                            
                            <!-- QR for entire division -->
                            <QrcodeVue
                                v-else-if="qr_link_type === 4"
                                :render-as="'svg'"
                                :value="form.generated_url"
                                :size="145"
                                :foreground="'#000'"
                                level="L"
                                style="border: 3px #ffffff solid; width: 300px; height: 300px;"
                            />
                        </div>
                        
                        <div class="text-center mt-4" v-if="generated">
                            <v-btn color="primary" @click="printCSFForm">
                                <v-icon left>mdi-printer</v-icon>
                                Print CSF Form
                            </v-btn>
                        </div>
                    </v-card-body>
                   </v-card>
                </div>
            </div>
        </div>
        <CSFPrint v-if="generated" :is_printing="is_printing" :form="form" :data="props" />
    </AppLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
   table {
    border-collapse: collapse;
    width: 100%;
  }
  tr, th, td {
    border: 1px solid rgb(145, 139, 139);
    padding: 8px;
  }
</style>