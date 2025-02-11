<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ModalForm from '@/Pages/Libraries/Offices/Modal.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { reactive ,ref, watch, onMounted} from 'vue';
    import Swal from 'sweetalert2';
    
    const props = defineProps({
        offices: Object, 
    });


    const show_modal = ref(false);
    const action_clicked = ref(null);

    const form = ref({});
    const office = ref({});
    const search = ref('');

    watch(
    () => search.value,
        (search) => {
            router.get('/offices', { search },{ preserveState: true})
        }
        
    );
    
    const showOfficeModal = async (is_show, action,office_data) => {
        show_modal.value = is_show;
        action_clicked.value = action;
        office.value = office_data;
    };

    const reloadOfficess = async () => {
        offices.value = {};
    };
</script>


<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Offices
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <v-row style="margin-bottom:-30px">
                       <v-col>
                         <div class="w-full m-5 text-right">
                            <v-text-field
                                :loading="loading"
                                append-inner-icon="mdi-magnify"
                                density="compact"
                                label="Search"
                                variant="solo"
                                hide-details
                                single-line
                                v-model="search"
                                @click:append-inner="onClick"
                            ></v-text-field>   
                         </div>
                        </v-col>
                        <v-col>
                            <div class="text-right m-5">
                                <v-btn @click="showOfficeModal(true, 'Add', null)" size="small" prepend-icon="mdi-plus" color="green">     
                                    Office                      
                                </v-btn>
                            </div>
                        </v-col>
                    </v-row>
                         
                    <v-card class="m-3">
                       
                        <v-table>
                            <thead>
                            <tr >
                                <th class="text-left">
                                    #
                                </th>
                                <th class="text-left">
                                    Name
                                </th>
                                <th class="text-left">
                                    Code
                                </th>
                                    <th class="text-left">
                                    Short Name
                                </th>
                                <th class="text-left">
                                    Order
                                </th>

                                
                                  <th class="text-center">
                                    Actions
                                </th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            <tr 
                                v-for="(office,index) in offices.data"
                                :key="office.id"
                                class="hover:bg-gray-200"
                            >
                                <template v-if="office">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ office.name }}</td>
                                    <td>{{ office.code }}</td>
                                    <td>{{ office.short_name }}</td>
                                    <td>{{ office.order }}</td>

                                    <td class="text-center">
                                        <v-btn @click="showOfficeModal(true, 'Update' , office)" size="small" prepend-icon="mdi-update" color="primary">     
                                            Update                         
                                        </v-btn>
                                    </td>
                                </template>
                                 <template v-else>
                                    <td span=""> No data at the moment</td>
                                </template>
                            </tr>
                            </tbody>

                            <template v-slot:bottom>
                                 <div class="m-2">
                                    <span style="color: gray">
                                        Showing {{ offices.from }} to {{ offices.to }} out of
                                        <b>{{ offices.total }} records</b>
                                    </span>
                                    <div class="text-center">
                                        <v-pagination
                                            :length="offices.last_page"
                                            circle
                                            @click="getOffices"
                                        ></v-pagination>
                                    </div>   
                                </div>           
                            </template> 
                        </v-table>
                    </v-card>
                </div>
            </div>
        </div>

        <ModalForm 
            :value="show_modal"
            :office="office"
            :action="action_clicked"
            @input="showOfficeModal"
            @reloadOffices="reloadOffices"
        ></ModalForm>
    </AppLayout>
</template>
