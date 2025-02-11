<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

import { Head, Link, router } from '@inertiajs/vue3';

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <!-- <v-row class="mx-15 mt-5" >
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
                              
                              <v-col class="my-auto"  v-if="unit.data[0].id == 8 || user.account_type == 'planning'" >
                                   <vue-multiselect
                                      v-model="form.client_type"
                                      prepend-icon="mdi-account"
                                      :options="['Internal', 'External']"
                                      :multiple="false"
                                      placeholder="Select Client Type"
                                      :allow-empty="true"
                                    >         
                                    </vue-multiselect>       
                              </v-col>


                              <v-col class="my-auto " v-if="unit.data[0].sub_units.length > 0" >
                                    <vue-multiselect
                                      v-model="form.selected_sub_unit"
                                      prepend-icon="mdi-account"
                                      :options="unit.data[0].sub_units"
                                      :multiple="false"
                                      placeholder="Select Sub Unit*"
                                      label="sub_unit_name"
                                      track-by="sub_unit_name"
                                      :allow-empty="false"
                                      :disabled="generated"
                                    >         
                                    </vue-multiselect>           
                              </v-col>

                                <v-col class="my-auto mr-5 ml-5" v-if="unit_pstos.length > 0" >
                                    <vue-multiselect
                                      v-model="form.selected_unit_psto"
                                      :options="unit_pstos"
                                      :multiple="false"
                                      placeholder="Select Unit PSTO"
                                      label="psto_name"
                                      track-by="psto_name"
                                      :allow-empty="false"
                                    >
                                    </vue-multiselect>          
                              </v-col>

                                <v-col class="my-auto mr-5" v-if="sub_unit_pstos.length > 0" >
                                    <vue-multiselect
                                      v-model="form.selected_sub_unit_psto"
                                      :options="sub_unit_pstos"
                                      :multiple="false"
                                      placeholder="Select Sub Unit PSTO"
                                      label="psto_name"
                                      track-by="psto_name"
                                      :allow-empty="false"
                                    >
                                    </vue-multiselect>          
                              </v-col>      

                                <v-col class="my-auto" v-if="sub_unit_types.length > 0 && form.selected_sub_unit" >
                                    <vue-multiselect
                                      v-model="form.sub_unit_type"
                                      :options="sub_unit_types"
                                      :multiple="false"
                                      placeholder="Select Driving Type"
                                      label="type_name"
                                      track-by="type_name"
                                      :allow-empty="false"
                                    >
                                    </vue-multiselect>          
                              </v-col>

                             
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
                                    <v-btn @click="generateCSIReport(service, unit)" >Generate</v-btn>
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
                              </v-row>
        
                              </v-card-body>
                    </v-card>
        </v-row>
        <v-row class="mx-15 mt-5" >
            <v-col cols="12" sm="6" md="3" lg="3" :key="n">
                <Link href="/accounts">
                    <v-card class="mx-auto my-4" elevation="16" max-width="344">
                        <v-card-item>
                        <v-card-title>
                            Total Number of Respondents 
                            
                        </v-card-title>

                        <v-card-subtitle>
                            <v-icon size="x-large" class="p-3" >mdi-account</v-icon>
                        </v-card-subtitle>
                        </v-card-item>

                        <v-card-text>
                            <h1>100%</h1>
                        </v-card-text>
                    </v-card>
                </Link>
            </v-col>
            <v-col cols="12" sm="6" md="3" lg="3" :key="n">
                <Link href="/accounts">
                    <v-card class="mx-auto my-4" elevation="16" max-width="344">
                        <v-card-item>
                            <v-card-title class="text-wrap" style="white-space: normal; word-break: break-word;">
                                Total Respondents who rated VS/S
                            </v-card-title>

                        </v-card-item>

                        <v-card-text>
                            <h1>100%</h1>
                        </v-card-text>
                    </v-card>
                </Link>
            </v-col>
            <v-col cols="12" sm="6" md="3" lg="3" :key="n">
                <Link href="/accounts">
                    <v-card class="mx-auto my-4" elevation="16" max-width="344">
                        <v-card-item>
                        <v-card-title class="text-wrap" style="white-space: normal; word-break: break-word;">
                            Percentage of Respondents who rated VS/S
                        </v-card-title>
                        </v-card-item>

                        <v-card-text>
                            <h1>100%</h1>
                        </v-card-text>
                    </v-card>
                </Link>
            </v-col>
            <v-col cols="12" sm="6" md="3" lg="3" :key="n">
                <Link href="/accounts">
                    <v-card class="mx-auto my-4" elevation="16" max-width="344">
                        <v-card-item>
                        <v-card-title>
                            Net Promoter Score(NPS)
                        </v-card-title>

                        <v-card-subtitle>
                            <v-icon size="x-large" class="p-3" >mdi-account</v-icon>
                        </v-card-subtitle>
                        </v-card-item>

                        <v-card-text>
                            <h1>100%</h1>
                        </v-card-text>
                    </v-card>
                </Link>
            </v-col>

          
       
        </v-row>
        <v-row>
            <v-col cols="12" sm="4" md="4" lg="4" :key="n">
                <Link href="/accounts">
                    <v-card class="mx-auto my-4" elevation="16" max-width="344">
                        <v-card-item>
                        <v-card-title>
                            Customer Satisfaction Rating
                        </v-card-title>

                        <v-card-subtitle>
                            <v-icon size="x-large" class="p-3" >mdi-account</v-icon>
                        </v-card-subtitle>
                        </v-card-item>

                        <v-card-text>
                            <h1>100%</h1>
                        </v-card-text>
                    </v-card>
                </Link>
            </v-col>
            <v-col cols="12" sm="6" md="4" lg="4" :key="n">
                <Link href="/accounts">
                    <v-card class="mx-auto my-4" elevation="16" max-width="344">
                        <v-card-item>
                        <v-card-title>
                            Customer Satisfaction Index
                        </v-card-title>

                        <v-card-subtitle>
                            <v-icon size="x-large" class="p-3" >mdi-account</v-icon>
                        </v-card-subtitle>
                        </v-card-item>

                        <v-card-text>
                            <h1>100%</h1>
                        </v-card-text>
                    </v-card>
                </Link>
            </v-col>
            <v-col cols="12" sm="6" md="4" lg="4" :key="n">
                <Link href="/accounts">
                    <v-card class="mx-auto my-4" elevation="16" max-width="344">
                        <v-card-item>
                        <v-card-title>
                            Likert Scale Rating
                        </v-card-title>

                        <v-card-subtitle>
                            <v-icon size="x-large" class="p-3" >mdi-account</v-icon>
                        </v-card-subtitle>
                        </v-card-item>

                        <v-card-text>
                            <h1>100%</h1>
                        </v-card-text>
                    </v-card>
                </Link>
            </v-col>
        </v-row> -->
        
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
            

                <Link href="/division_sections">
                    <div class="py-5 ml-5 mr-5 " style="width:250px">
                        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <v-icon size="x-large" class="p-3" >mdi-domain</v-icon>
                            <a href="#">
                                <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    Divisions Units
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

                
        </v-row>

     

 
    </AppLayout>
</template>
