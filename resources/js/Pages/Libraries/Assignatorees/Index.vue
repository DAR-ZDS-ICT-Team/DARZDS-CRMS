<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ModalForm from '@/Pages/Libraries/Assignatorees/Modal.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { reactive ,ref, watch, onMounted} from 'vue';
    import Swal from 'sweetalert2';
    
    const props = defineProps({
        assignatorees: Object, 
    });


    const show_modal = ref(false);
    const action_clicked = ref(null);

    const form = ref({});
    const assignatoree = ref({});
    const search = ref('');

    watch(
    () => search.value,
        (search) => {
            router.get('/assignatorees', { search },{ preserveState: true})
        }
        
    );
    
    const showAssignatoreeModal = async (is_show, action,assignatoree_data) => {
        show_modal.value = is_show;
        action_clicked.value = action;
        assignatoree.value = assignatoree_data;
    };

    const deleteRecord = async (id) => {

        Swal.fire({
            html: '<div style="font-weight: bold; font-size:25px">Are you sure you want to delete this record?</div> ',
            icon:'warning',
            
            showCancelButton: true,
            confirmButtonText: "Yes, I'm sure",
            showLoaderOnConfirm: true,
        }).then((result) => {
            if (result.isConfirmed) {            
                router.post('/assignatorees/delete', { id } );
            }
        });

    };

    const reloadAssignatorees = async () => {
        assignatoree.value = {};
    };
</script>


<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Assignatorees
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
                                <v-btn @click="showAssignatoreeModal(true, 'Add', null)" size="small" prepend-icon="mdi-plus" color="green">     
                                    Assignatoree                      
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
                                    Designation
                                </th>
                                <th class="text-center">
                                    Actions
                                </th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            <tr 
                                v-for="(assignatoree,index) in assignatorees.data"
                                :key="assignatoree.id"
                                class="hover:bg-gray-200"
                            >
                                <template v-if="assignatoree">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ assignatoree.name }}</td>
                                    <td>{{ assignatoree.designation }}</td>

                                    <td class="text-center">
                                        <v-btn size="small" @click="deleteRecord(assignatoree.id)" prepend-icon="mdi-delete" color="red" class="mr-5">  
                                            Delete                            
                                        </v-btn>
                                        <v-btn @click="showAssignatoreeModal(true, 'Update' , assignatoree)" size="small" prepend-icon="mdi-update" color="primary">     
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
                                        Showing {{ assignatorees.from }} to {{ assignatorees.to }} out of
                                        <b>{{ assignatorees.total }} records</b>
                                    </span>
                                    <div class="text-center">
                                        <v-pagination
                                            :length="assignatorees.last_page"
                                            circle
                                            @click="getAssignatorees"
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
            :assignatoree="assignatoree"
            :action="action_clicked"
            @input="showAssignatoreeModal"
            @reloadAssignatorees="reloadAssignatorees"
        ></ModalForm>
    </AppLayout>
</template>
