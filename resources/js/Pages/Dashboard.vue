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

        
        <v-row class="mx-15 mt-5">
                <Link href="/settings/accounts">
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

                <Link href="/services">
                    <div class="py-5 ml-5 mr-5 " style="width:250px">
                        <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <v-icon size="x-large" class="p-3" >mdi-map-marker</v-icon>
                            <a href="#">
                                <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    Services
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
