<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { reactive ,ref, watch, onMounted} from 'vue';
    import Swal from 'sweetalert2';
    
    const props = defineProps({
        date_display: String, 
    });

    const show_modal = ref(false);
    
    const showDateDisplayModal = async (is_show) => {
        show_modal.value = is_show;
    };

    const reloadDateDisplay = async () => {
        date_display.value = {};
    };

    watch(
        () => props.date_display[0].is_displayed,
        (value) => {
            router.post('/show-date-csf-form/update', { value } );
        }
    );

</script>


<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                CSF Form Date Display
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <v-row style="margin-bottom:-30px">
       
                        <v-col>
                            <div class="text-right m-5">
                                <v-btn @click="showDateDisplayModal(true, 'Add', null)" size="small" prepend-icon="mdi-plus" color="green">     
                                    Update                      
                                </v-btn>
                            </div>
                        </v-col>
                    </v-row>
                         
                    <v-card class="m-3 d-flex align-center justify-center">
                        <v-card-title class="text-center">Is Displayed?</v-card-title>
                        <v-switch
                            v-model="date_display[0].is_displayed"
                            color="blue"
                            hide-details
                            class="my-4"
                            :true-value="1"
                            :false-value="0"
                        ></v-switch>
                        </v-card>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
