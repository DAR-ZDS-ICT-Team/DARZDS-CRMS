<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ModalForm from '@/Pages/Account/Partials/Modal.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { reactive ,ref, watch, onMounted} from 'vue';
    import Swal from 'sweetalert2';
    
    const props = defineProps({
        services: Object, 
    });


    const deleteData = async (id) => {
             Swal.fire({
                html: '<div style="font-weight: bold; font-size:25px">Are you sure you want to delete this?</div> ',
                icon:'warning',
                inputAttributes: {
                    autocapitalize: "off"
                },
                
                showCancelButton: true,
                confirmButtonText: "Yes, I'm sure",
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {            
                    router.post('/services/delete', { id },{
                        onSuccess: () => {
                           Swal.fire({
                                title: 'Success',
                                icon: 'success',
                                text: 'The Service has been successfully deleted.',
                            })
                        },

                        onError: () => {
                            Swal.fire({
                                title: 'Failed',
                                icon: 'error',
                                text: this.error ? this.error: "Something went wrong please check",
                            })

                        }

                    })
    
                }
            });

        
    };

    const show_modal = ref(false);
    const action_clicked = ref(null);

    const form = ref({});
    const account = ref({});
    const search = ref('');

    watch(
    () => search.value,
        (search) => {
            router.get('/accounts', { search },{ preserveState: true})
        }
        
    );
    
    const showModal = async (data) => {
        show_modal.value = is_show;
        action_clicked.value = action;
        account.value  =  user_data;
    };

    const reloadAccounts = async () => {
        account.value = {};
    };
</script>


<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Libraries/Settings
            </h2>
        </template>
       <v-row class="mx-15 mt-5">
                <Link href="/accounts">
                    <div class="py-5 ml-5 mr-5 " style="width:250px">
                        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <v-icon size="x-large" class="p-3" >mdi-account</v-icon>
                            <a href="#">
                                <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    Accounts
                                </h5>
                            </a>  

                        </div>
                    </div>     
                 </Link>

                <Link href="/assignatorees">
                    <div class="py-5 ml-5 mr-5 " style="width:250px">
                        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <v-icon size="x-large" class="p-3" >mdi-account-multiple</v-icon>
                            <a href="#">
                                <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    Assignatorees
                                </h5>
                            </a>  

                        </div>
                    </div>     
                </Link>
            

                <Link href="/division_sections">
                    <div class="py-5 ml-5 mr-5 " style="width:250px">
                        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <v-icon size="x-large" class="p-3" >mdi-domain</v-icon>
                            <a href="#">
                                <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    Division Sections
                                </h5>
                            </a>  

                        </div>
                    </div>     
                </Link>

                    <Link href="/offices">
                    <div class="py-5 ml-5 mr-5 " style="width:250px">
                        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <v-icon size="x-large" class="p-3" >mdi-map-marker</v-icon>
                            <a href="#">
                                <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    Offices
                                </h5>
                            </a>  

                        </div>
                    </div>     
                </Link>
            

                <Link href="/show-date-csf-form">
                    <div class="py-5 ml-5 mr-5 " style="width:250px">
                        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <v-icon size="x-large" class="p-3" >mdi-calendar</v-icon>
                            <a href="#">
                                <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    CSF Date Display
                                </h5>
                            </a>  

                        </div>
                    </div>     
                </Link>

         
        </v-row>


        <ModalForm 
            :value="show_modal"
            :account="account"
            :regions="regions"
            :action="action_clicked"
            @input="showAccountModal"
            @reloadAccounts="reloadAccounts"
        ></ModalForm>
    </AppLayout>
</template>
