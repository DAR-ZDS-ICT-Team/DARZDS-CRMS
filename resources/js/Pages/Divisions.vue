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
    divisions: Object,
});

const goDivisionSections = async (office_id,division_id) => {
    router.get(`/divisions/csf/division_sections?office_id=`+office_id + `&division_id=`+division_id)
}

const goBack = async () => {
    window.history.back()
}



</script>

<template >
    <Head title="Divisions" />   
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
                <div class="w-full border bg-primary">
                <v-card-title class="text-center">Divisions</v-card-title>
             </div>
            </v-col>
        </v-row>
        <v-row class="mx-10 mt-5" justify="center" align="center">
            <v-col v-for="division in divisions" :key="division.id" cols="12" sm="6" md="6" lg="6" justify="center" align="center">
            <Link @click="goDivisionSections(office_id, division.id)" class="card-link">
                <div class="card mx-5 max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 d-flex flex-column align-left justify-center" style="height:180px; text-align: center;">
                <v-icon color="green" size="x-large" class="p-3">mdi-check-circle</v-icon>
                <p class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white text-left">
                    {{ division.division_name }}
                </p>
                </div>
            </Link>
            </v-col>
        </v-row>
        <v-row>
            <Link @click="goBack()">
            <v-btn prepend-icon="mdi-arrow-left" style="margin-left: 130px">Back</v-btn>
            </Link>
        </v-row>
        
     
  
</v-container>
        
</template>

<style scoped>
.card {
  transition: box-shadow 0.3s ease, background-color 0.3s ease, color 0.3s ease;
}

.card:hover {
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
}

</style>


