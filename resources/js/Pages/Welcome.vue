<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { reactive, watch, ref, onMounted } from "vue";
 import AOS from 'aos'
import 'aos/dist/aos.css'

import * as THREE from 'three';

AOS.init();

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});


</script>

<template >
    <Head title="Homepage" /> 
    <div  class="body-wrapper"     
     >
     <nav 
        data-aos="fade-down" 
        data-aos-duration="500" 
        data-aos-delay="500" 
         style="backdrop-filter: blur(2px);"
        class="bg-transparent dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <!-- <img src="../../../public/images/dar-logo.svg" class="h-8" alt="DOST Logo"> -->
                <span class="self-center text-2xl font-semibold whitespace-nowrap"></span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <a  href="/divisions/csf/offices">
                        <v-btn variant="outlined" id="mega-menu-full-dropdown-button" style="border:1px solid white">
                            <span class="text-white">Get Started</span>
                        </v-btn>
                    </a>
                    <!-- <a :href="route('login')">
                        <button class="mr-3 ml-5 text-blue-800 bg-gray-200 hover:bg-blue-800 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Login
                        </button>
                    </a> -->
                
                <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                    </svg>
                </button>
            </div>
          
        </div>

    </nav> 

    <div style="display: flex;
                justify-content: center;
                align-items: center;
                color: white;
                width: 100%;     
                "
                data-aos="zoom-out" 
                data-aos-duration="500" 
                data-aos-delay="500" 
               >
        <h1 style="
            font-size:60px; 
            font-weight: bold; 
            text-shadow: -2px -2px 4px green, 5px 5px 10px black;
            text-align:center; 
            margin-bottom: 42%;">Customer Relation Management System</h1>
      
    </div><br>
   <div>

   </div>
</div>


</template>


<style>
    #app {
         width:100vw;
         height: 100vh;
         z-index:1;
         margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }


    body{
        background: url('../../../public/images/bg-dar.svg') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 0;
    }

 
</style>
