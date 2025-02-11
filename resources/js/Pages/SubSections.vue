<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { reactive, watch, ref, onMounted } from "vue";
 import AOS from 'aos'
import 'aos/dist/aos.css'
import { router } from '@inertiajs/vue3'


AOS.init();

defineProps({
    office_id: Number,
    office: Object,
    division_id: Number,
    section_id: Number,
    section: Object,
    sub_sections:Object,
});

const goNext = async (office_id, division_id, section_id, sub_section_id) => {
    if(sub_section_id == 3){
        router.get(`/sections/csf/sub-section/types?office_id=`+ office_id + 
                                `&section_id=`+ section_id + 
                                `&section_id=`+ section_id +
                                `&sub_section_id=` + sub_section_id );   
    }
   
}


const goBack = async (sub_section_id) => {
    window.history.back()
}

</script>

<template >
    <Head title="Division Sections" />   
     <nav 
        data-aos="fade-down" 
        data-aos-duration="500" 
        data-aos-delay="500" 
         style="backdrop-filter: blur(2px);"
        class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="../../../public/images/dar-logo.svg" class="h-8" alt="DOST Logo">
                <span class="self-center text-2xl font-semibold whitespace-nowrap">DOST <span v-if="office">{{ office.code }}</span> Customer Relation Management System</span>
            </a>

            </div>

        
    </nav>  
    <v-container fill-height>
        <v-row class="mx-15" style="margin-top: 100px;" >
            <v-col>
                <div class="w-full border bg-blue">
                <v-card-title class="text-center text-uppercase">{{ section.section_name  }}</v-card-title>
             </div>
            </v-col>
        </v-row>
        <v-row   class=" mx-15 mt-5" align="center" justify="center">        
                <v-col v-for="sub_section in sub_sections" cols="12"sm="4" md="4" lg="4">
                    <Link @click="goNext(office_id, division_id, section_id, sub_section.id)">
                        <div style="height:150px"  class="card max-w-sm p-6 bg-white border shadow border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                                <v-icon color="blue" size="x-large" class="p-3" >mdi-check-circle</v-icon>
                                <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    {{ sub_section.sub_section_name }}
                                </h5>      
                        </div>     
                    </Link>
                </v-col>

        </v-row>
        <v-row>
            <Link @click="goBack()">
            <v-btn prepend-icon="mdi-arrow-left" style="margin-left: 120px">Back</v-btn>
            </Link>
        </v-row>
        
     
  
</v-container>

</template>
<style scoped>
.card {
  transition: box-shadow 0.3s ease, background-color 0.3s ease, color 0.3s ease;
}

.card:hover {
  box-shadow:  10px 10px 15px rgba(0, 0, 0, 0.2);
}
</style>


