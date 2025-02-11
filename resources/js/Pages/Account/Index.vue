<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ModalForm from '@/Pages/Account/Partials/Modal.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { reactive ,ref, watch, onMounted} from 'vue';
    import Swal from 'sweetalert2';
    
    const props = defineProps({
        accounts: Object, 
        offices: Object, 
        divisions: Object, 
    });


    const resetPassword = async (id) => {
             Swal.fire({
                html: '<div style="font-weight: bold; font-size:25px">Are you sure you want to reset password for this account?</div> ',
                icon:'warning',
                inputAttributes: {
                    autocapitalize: "off"
                },
                
                showCancelButton: true,
                confirmButtonText: "Yes, I'm sure",
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {            
                    router.post('/accounts/reset-password', { id },{
                        onSuccess: () => {
                           Swal.fire({
                                title: 'Success',
                                icon: 'success',
                                text: 'Password has been successfully reset.',
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
    
    const showAccountModal = async (is_show, action, user_data) => {
        show_modal.value = is_show;
        action_clicked.value = action;
        account.value  =  user_data;
    };

    const reloadAccounts = async () => {
        account.value = {};
    };

    let page_number = 1;
    const getAccounts = async (page) => {
       router.visit('/accounts?page=' + page , { preserveState: true});
       page_number = page;
    };

</script>


<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Accounts
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
                                <v-btn @click="showAccountModal(true, 'Add', {})" size="small" prepend-icon="mdi-plus" color="green">     
                                    Account                      
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
                                <th class="text-left">
                                    Email
                                </th>
                                <th class="text-center">
                                    Role/Account Type
                                </th>
                                <!-- <th class="text-center">
                                    Is Active Status
                                </th> -->
                                
                                  <th class="text-center">
                                    Actions
                                </th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            <tr 
                                v-for="(user,index) in accounts.data"
                                :key="user.id"
                                class="hover:bg-gray-200"
                            >
                                <template v-if="user">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.designation}}</td>
                                    <td>{{ user.email }}</td>
                                    <td class="text-center">
                                        <v-chip v-if="user.account_type == 'admin'" class="bg-indigo" size="small">
                                            {{ user.account_type }}
                                        </v-chip>
                                         <v-chip v-else class="bg-green" size="small">
                                            {{ user.account_type }}
                                        </v-chip>
                                    </td>
                                    <!-- <td style="text-align: center;">
                                        <div style="display: inline-block;">
                                            <v-switch
                                                v-model="user.is_active"
                                                :true-value="1" 
                                                :false-value="0"
                                                color="green"
                                                hide-details
                                                readonly
                                            ></v-switch>
                                        </div>
                                    </td> -->

                                    <td class="text-center">
                                        <v-btn size="small" @click="resetPassword(user.id)" prepend-icon="mdi-lock-reset" color="secondary" class="mr-5">  
                                            Reset                            
                                        </v-btn>
                                        <v-btn @click="showAccountModal(true, 'Update' , user)" size="small" prepend-icon="mdi-update" color="primary">     
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
                                        Showing {{ accounts.meta.from }} to {{ accounts.meta.to }} out of
                                        <b>{{ accounts.meta.total }} records</b>
                                    </span>
                                    <div class="text-center">
                                        <v-pagination
                                            v-model="page_number"
                                            :length="accounts.meta.last_page"
                                            circle
                                            @click="getAccounts(page_number)"
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
            :account="account"
            :data="props"
            :action="action_clicked"
            @input="showAccountModal(is_show, action, user_data)"
            @reloadAccounts="reloadAccounts"
        ></ModalForm>
    </AppLayout>
</template>
