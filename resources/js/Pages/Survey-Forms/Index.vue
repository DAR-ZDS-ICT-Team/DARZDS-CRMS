<script setup>
import { ref,reactive, onMounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SignaturePad from 'signature_pad';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Swal from 'sweetalert2';

 const props = defineProps({
        cc_questions: Object,
        dimensions: Object, 
        division: Object,
        section: Object,
        sub_section: Object,  
        status: String,
        errors: Object,
        captcha_img: String,
        date_display: String,
    });


const cc1_options = [
    { label: "1. I know what a CC is and I saw this office's CC.", value: '1' },
    { label: "2. I know what a CC is but I did NOT see this office's CC. ", value: '2' },
    { label: "3. I learned the CC when I saw this office's CC.", value: '3' },
    { label: "4. I do not know what a CC is  and I did not see one in this office.(Answer 'N/A' on CC2 and CC3)", value: '4' },
];
const cc2_options = [
    { label: "1. Easy to see", value: '1' },
    { label: "2. Somewhat easy to see", value: '2' },
    { label: "3. Difficult to see", value: '3' },
    { label: "4. not Visible at all", value: '4' },
    { label: "5. N/A", value: '5' },
];
const cc3_options = [
    { label: "1. Helped Very Much", value: '1' },
    { label: "2. Somewhat helped", value: '2' },
    { label: "3. Did not help", value: '3' },
    { label: "4. N/A", value: '4' },
];
const options = [
    { label: 'Strongly Agree', value: '5', icon: 'mdi-emoticon-excited', color: '#FFEB3B' },
    { label: 'Agree', value: '4', icon: 'mdi-emoticon-happy', color: '#FFC107' },
    { label: 'Neither', value: '3', icon: 'mdi-emoticon-neutral', color: '#263238' },
    { label: 'Disagree', value: '2', icon: 'mdi-emoticon-sad', color: '#F44336' },
    { label: 'Very Disagree', value: '1', icon: 'mdi-emoticon-frown', color: '#6200EA' },
    { label: 'N/A', value: '6', icon: 'mdi-close-circle-outline', color: 'red' },
];
const attribute_numbers = [
    { label: '5', value: '5' },
    { label: '4', value: '4' },
    { label: '3', value: '3' },
    { label: '2', value: '2' },
    { label: '1', value: '1' },
];
const recommendation_numbers = [
    { label: '10', value: '10' },
    { label: '9', value: '9' },
    { label: '8', value: '8' },
    { label: '7', value: '7' },
    { label: '6', value: '6' },
    { label: '5', value: '5' },
    { label: '4', value: '4' },
    { label: '3', value: '3' },
    { label: '2', value: '2' },
    { label: '1', value: '1' },
];

const getCurrentDate = () => {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
      const day = String(today.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
};
const form = reactive({
    office_id: null,
    division_id: null,
    section_id: null,
    sub_section_id: null,
    date: getCurrentDate(),
    client_type: null,
    sub_section_type: null,
    email: null,
    name: null,
    sex: null,
    age_group: null,
    pwd: 0,
    pregnant: 0,
    senior_citizen: 0,
    cc1: null,
    cc2: null,
    cc3: null,
    recommend_rate_score: null,
    comment: null,
    is_complaint: false,
    indication: null,
    // signature: null,
    dimension_form: {
        id: [],
        rate_score: [],
        importance_rate_score: [],
    },
    cc_form: {
        id: [],
        answer: [],
    },
    captcha: null,
    current_url: null,

    complaint_scanner: {
        value: [],
    },
});

const formSubmitted = ref(false);

const getCC = (index,cc_id, answer) => {

    form.cc_form.id[index] = cc_id;
    form.cc_form.answer[index] = answer;

};


const getDimension = (index,dimension_id) => {
    form.dimension_form.id[index] = dimension_id;
};

// const signaturePad = ref(null);
// const canvas = document.querySelector('.signature-pad canvas');

onMounted(() => {
    AOS.init();

    // signaturePad.value = new SignaturePad(signaturePad.value);
    // const canvas = signaturePad.value;
    // canvas.width = 400;
    // canvas.height = 200;

    const currentURL = window.location.href;
    // Extract query parameters from the URL
    const searchParams = new URLSearchParams(currentURL.split("?")[1]);

    // Get office_id, division_id, and section_id values
    form.office_id = searchParams.get("office_id");
    form.division_id = searchParams.get("division_id");
    form.section_id = searchParams.get("section_id");
    form.sub_section_id = searchParams.get("sub_section_id");
    form.sub_section_type = searchParams.get("sub_section_type");
    form.current_url =currentURL; 

    Swal.fire({
        title: "Disclaimer",
        icon: "warning",
        text: "The DOST is committed to protect and respect your personal data privacy. All information collected will only be used for documentation purposes and will not be published in any platform.",
    });
});



const saveCSF = async () => {
    formSubmitted.value = true;

    // const canvas = document.querySelector('.signature-pad');
    // const ctx = canvas.getContext('2d');
    
    // const imageDataUrl = canvas.toDataURL();

    // Include the data URL in your form data
    // form.signature = imageDataUrl;
   
    let captcha_code = Math.random(); 
     // Function to generate a new CAPTCHA image
    const generateCaptcha = () => {
        const captchaImageUrl = '/captcha/flat?rand=' + captcha_code; // Construct the URL with captcha_code
        return '<img src="' + captchaImageUrl + '" alt="CAPTCHA" style="display: block; margin: 0 auto; ">';
    };

    try {

        Swal.fire({
            title: generateCaptcha(),
            html: '<div style="font-weight: bold; font-size:25px">Enter Captcha Code</div> ' +
                '<input id="captcha-input" class="swal2-input text-center">' +
                '<p id="invalid-captcha-text" style="color: red; font-size: 14px; margin-top: 5px; display: none;">Invalid CAPTCHA code</p>',
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Submit",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const enteredCaptcha = document.getElementById('captcha-input').value;
                form.captcha = enteredCaptcha;
                return true; 
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.post('/csf_submission', form);     
            }
        });
    } catch (error) {
        Swal.fire({
            title: 'Failed',
            icon: 'error',
            text: "Something went wrong please check",
        })
    }


};

const updateIsComplaint = (index ,rate_score)=> {
    if(form.dimension_form.rate_score[index] > 0 && form.dimension_form.rate_score[index] < 4   ){
        form.complaint_scanner.value[index] = true; 
    }
    else{
        form.complaint_scanner.value[index] = false; 
    }

    if(form.dimension_form.rate_score[index] == 6){
        form.dimension_form.importance_rate_score[index] = 5;
    }


    form.is_complaint = false;
    form.complaint_scanner.value.forEach((value) => {
        if (value === true) {
            form.is_complaint = true;
            return; // If we found any true value, we can exit the loop
        }
    });


};

// const clearSignature = () => {
//     new SignaturePad(signaturePad.value);
// };

// watch(
//     () => props.errors.captcha,
//     (value) => {
//         if(value){
//             Swal.fire({
//                 title: "Error Captcha",
//                 text: "Wrong captcha code!" ,
//                 icon: "error",         
//             })
//         }
//     }
     
// );

watch(
    () => props.errors,
    (value) => {
        if(value){
            Swal.fire({
                title: 'Failed',
                icon: 'error',
                text: "Something went wrong. Please check the fields and make sure the captcha is correctly entered. If you continue to get errors, please contact the administrator.",      
            })
        }
    }
     
);


</script>

<template>
    <Head title="CSF Form" />
    

     <nav 
        data-aos="fade-down" 
        data-aos-duration="500" 
        data-aos-delay="500" 
        class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="../../../../public/images/dar-logo.svg" class="h-8" alt="DOST Logo" >
                <span class="self-center lg:text-2xl md:text-base sm:text-sm font-semibold whitespace-nowrap dark:text-white text-black">Department of Science and Technology </span>
            </a>
        </div>
    </nav>
        <v-card
            class="w-full"
            data-aos="fade-up" 
            data-aos-duration="2000" 
            data-aos-delay="500" 
        >
    

            <v-row justify="center" class="py-3 bg-gray-200 w-full">
                <v-col cols="12" md="8" sm="6">
                    <v-form class="max-w" @submit.prevent="saveCSF">
                        <div class="py-20 bg-gray-200 ">
                            <v-card class="mb-3 md:mb-0 sm:mb-0 text-center" >
                                <v-card-title class="m-5 font-black flex flex-col items-center">
                                    <div>
                                        <img
                                        data-aos="zoom-in" 
                                        data-aos-duration="500" 
                                        data-aos-delay="500"
                                        class="mx-auto sm:mb-0" 
                                        style="width:200px; height:200px" 
                                        src="../../../../public/images/dar-logo.svg" 
                                        alt="..">
                                    </div>
                                    <span 
                                    class="font-black text-base lg:text-2xl md:text-base sm:text-sm"  
                                    data-aos="fade-down" 
                                    data-aos-duration="500" 
                                    data-aos-delay="500"
                                    >CUSTOMER SATISFACTION FEEDBACK 
                                </span><br>

                                
                                </v-card-title>                           
                            </v-card>

                        
                            <v-card 
                                data-aos="zoom-out-up" 
                                data-aos-duration="1000" 
                                data-aos-delay="500" 
                                class="mb-5 mx-auto text-base md:text-base sm:text-sm"
                                >
                                    <a href="#">
                                        <img class="rounded-t-lg" src="/docs/images/blog/image-1.jpg" alt="" />
                                        </a>
                                        <div class="p-5">
                                            <a href="#">
                                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                                    <span v-if="division.length > 0"> {{ division[0].division_name }} </span> <br>
                                                    <span v-if="section.data.length > 0"> {{ section.data[0].section_name }} </span> 
                                                    <!-- <span v-if="sub_section.data.length > 0"> {{ sub_section.data[0].sub_section_name }}</span> -->
                                                    <!-- <span v-if="form.sub_section_type" class="ml-3"> {{ form.sub_section_type }}</span> -->
                                                </h5>
                                            </a>
                                            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400 ">This questionaire aims to solicit your honest assessment of our services. Please take a minute in filling out this form and help us serve you better.</p>
                                            <div>

                                                <v-text-field   
                                                    v-if="date_display[0].is_displayed == 1"                                 
                                                    v-model="form.date" 
                                                    type="date" 
                                                    label="Date"
                                                    variant="outlined" 
                                                >
                                                </v-text-field>


                                                <v-text-field    
                                                    v-if="form.is_complaint == true"                                
                                                    v-model="form.email" 
                                                    type="email" 
                                                    placeholder="email@gmail.com"
                                                    label="Email*"
                                                    variant="outlined" 
                                                    :rules="[ (v) => !!v || formSubmitted && !form.email ||  'This field is required',]"
                                                >
                                                </v-text-field>

                                                <v-text-field      
                                                    v-else-if="form.is_complaint == false"                                     
                                                    v-model="form.email" 
                                                    type="email" 
                                                    placeholder="email@gmail.com"
                                                    label="Email(Optional)"
                                                    variant="outlined" 
                                                >
                                                </v-text-field>

                                         

                                                <v-text-field                                    
                                                    v-model="form.name" 
                                                        placeholder="Enter your full name"
                                                        label="Name(Optional)"
                                                        variant="outlined" 
                                                >
                                                </v-text-field>

                                                <v-row class="mb-5"  >
                                                    <v-col cols="12" md="" sm="4" style="margin-bottom:-23px">
                                                        <v-select
                                                            label="Client_type*"
                                                            variant="outlined"
                                                            v-model="form.client_type"
                                                            :items="['General Public','Internal Employees','Business/Organization','Government Employees' ]"
                                                            :rules="[v => !!v || errors.client_type || 'This field is required']"

                                                        >
                                                        </v-select>
                                                    </v-col >

                                                    <v-col cols="12"  md="" sm="4" style="margin-bottom:-23px">
                                                        <v-select
                                                            label="Sex*"
                                                            variant="outlined"
                                                            v-model="form.sex"
                                                            :items="['Male','Female','Prefer not to say']"
                                                            :rules="[
                                                                (v) => !!v || errors.sex || 'This field is required',]
                                                            "
                                                        >
                                                        </v-select>

                                                    </v-col >

                                                    <v-col cols="12" md="" sm="4" style="margin-bottom:-23px">
                                                            <v-select
                                                                label="Age Group*"
                                                                variant="outlined"
                                                                v-model="form.age_group"
                                                                :items="['19 or lower','20-34','35-49','50-64','60+', 'Prefer not to say']"
                                                                :rules="[
                                                                    (v) => !!v || errors.sex ||  'This field is required',]
                                                                "
                                                            >
                                                            </v-select>                                  
                                                    </v-col >

                                                </v-row>
<!-- 
                                                <div class="border border-w-2 p-3 mb-5">
                                                    <div>
                                                        Other Informations
                                                        (<span class="text-blue-500">Optional</span>)
                                                    </div>
                                                    <v-row>
                                                        <v-col cols="12"  md="" sm="4" class="flex items-center ps-4  rounded" style="margin-bottom:-25px">
                                                            <input v-model="form.pwd" id="bordered-checkbox-2" type="checkbox" value="" name="bordered-checkbox" class="w-4 h-4 text-blue-600 bg-gray-100  rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                            <label for="bordered-checkbox-2" class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Person with Disability</label>
                                                        </v-col>
                                                        <v-col cols="12"  md="" sm="4" class="flex items-center ps-4  rounded" style="margin-bottom:-25px">
                                                            <input  v-model="form.pregnant" id="bordered-checkbox-3" type="checkbox" value="" name="bordered-checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                            <label for="bordered-checkbox-3" class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Pregnant Woman</label>
                                                        </v-col>
                                                        <v-col cols="12"  md="" sm="4" class="flex items-center ps-4  rounded " style="margin-bottom:-25px">
                                                            <input  v-model="form.senior_citizen" id="bordered-checkbox-4" type="checkbox" value="" name="bordered-checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                            <label for="bordered-checkbox-4" class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Senior Citizen</label>
                                                        </v-col>
                    
                                                    </v-row>
                                                </div> -->


                                            </div>
                                        </div>
                            </v-card >
                        
                
                            <v-card 
                                data-aos="zoom-out-up" 
                                data-aos-duration="1000" 
                                data-aos-delay="500" 
                                class="mb-5 mx-auto text-base sm:text-sm"
                                
                            >  
                                <div class="m-5 text-gray-600">
                                    <h2>
                                    Select your answer to the Citizen's Charter(CC) questions. The Citizen's Charter is an official document that reflects
                                    the services of a government agency/office including its requirements, fees and processing times among others.
                                    </h2>
                                </div>
                                <div v-for="(cc_question, i) in cc_questions" :key="i" class="mb-0" >
                                    <div class="mx-5">
                                        <div class="font-black">
                                            <h2>
                                                {{ cc_question.title }}. {{ cc_question.question }}
                                            </h2>
                                        </div>

                                        <v-radio-group v-if="i == 0" v-model="form.cc1" color="primary" class="mx-2">
                                            <h5 v-for="(option, index) in cc1_options" :key="index">
                                                <v-radio 
                                                    @click="getCC(i, cc_question.id, option.value)"
                                                    :label="option.label"
                                                    :value="option.value"
                                                ></v-radio>
                                            </h5>
                                        </v-radio-group>


                                        <v-radio-group v-if="i == 1" v-model="form.cc2" color="primary" class="mx-2">
                                            <h5 v-for="(option, index) in cc2_options" :key="index">
                                                <v-radio 
                                                    @click="getCC(i, cc_question.id, option.value)"
                                                    :label="option.label"
                                                    :value="option.value"
                                                ></v-radio>
                                            </h5>
                                        </v-radio-group>

                                        <v-radio-group v-if="i == 2" v-model="form.cc3" color="primary" class="mx-2">
                                            <h5 v-for="(option, index) in cc3_options" :key="index">
                                                <v-radio 
                                                    @click="getCC(i, cc_question.id, option.value)"
                                                    :label="option.label"
                                                    :value="option.value"
                                                ></v-radio>
                                            </h5>
                                        </v-radio-group>
                                    </div>

                                    
                                    <div class="text-red-800 m-5" style="margin-left:80px" v-if="formSubmitted && !form.cc_form.answer[i]  ">{{ 'This selection is required' }}</div> 
                                </div>

                            </v-card>
                
                            <v-card 
                                data-aos="fade-left" 
                                data-aos-duration="1000" 
                                data-aos-delay="500" 
                                class="mb-5 mx-auto">
                                <div class=" text-white bg-blue_grey p-3 ">
                                    <h3>HOW WOULD YOU RATE OUR 
                                        SERVICES?</h3>
                                </div>
                                <div>
            
                                        <v-card
                                            data-aos="fade-left" 
                                            data-aos-duration="1000" 
                                            data-aos-delay="500" 
                                            class="text-center over-flowhidden scroll-none mb-1"
                                            border="1"
                                            v-for="(dimension, index) in dimensions" :key="dimension.id"
                                        >
                                            <h5 class="mt-5 mb-3 text-left mx-5 bg-gray-200 p-3">
                                                <span  style="font-size: 18px">{{ dimension.id }}. {{ dimension.description }}({{ dimension.name }})</span>
                                            </h5>
                                            
                                            <input type="hidden" :value="getDimension(index, dimension.id)" />  
                                            <div >
                                                    <v-btn-toggle class="mb-5" v-model="form.dimension_form.rate_score[index]" v-for="option in options" :key="option.value"
                                                    :rules="[() => formSubmitted ? !!form.dimension_form.rate_score[index] || 'This selection is required' : true]"
                                                    >     
                                                        <v-btn @click="updateIsComplaint(index , form.dimension_form.rate_score[index])"  rounded class="mr-2 bg-gray-200" :value="option.value" color="secondary" >
                                                            <v-icon :color="form.dimension_form.rate_score[index] === option.value ? option.color : 'gray'" size="40">{{ option.icon }}</v-icon><br>
                                                            <label>{{ option.label }}</label>
                                                        </v-btn>      

                                                    </v-btn-toggle> 
                                                    <div class="text-red-800" v-if="formSubmitted && !form.dimension_form.rate_score[index]">{{ 'This selection is required' }}</div>
                                                </div>
                                                <div class="overflow-hidden mb-3" v-if="form.dimension_form.rate_score[index]  && form.dimension_form.rate_score[index] !=6" >
                                                    <div>How important is this attribute?</div>
                                                    <div>
                                                        <div class="ml-2 mb-3">
                                                            <v-btn-toggle  v-model="form.dimension_form.importance_rate_score[index]"  v-for="option in attribute_numbers "  :key="option.value"  mandatory>
                                                                <v-btn                      
                                                                    class=" mr-2"
                                                                    :value="option.value"                        
                                                                    color="secondary"
                                                                    style="border-radius:40%;"                         
                                                                >
                                                                <v-chip >
                                                                    <label >{{ option.label }}</label>
                                                                </v-chip>
                                                                </v-btn>

                                                            </v-btn-toggle>
                                                            <div class="text-red-800" v-if="formSubmitted && !form.dimension_form.importance_rate_score[index]">{{ 'This selection is required' }}</div>
                                                        </div>
                                                    </div>

                                                </div>

                                  
                                            

                                        </v-card>                     

                                    <v-divider></v-divider> 
                                
                                </div>
                            </v-card>
                            <v-card 
                                data-aos="zoom-out-up" 
                                data-aos-duration="1000" 
                                data-aos-delay="500" 
                                class="mb-5 mx-auto"
                            >
                                <div class="p-3 font-bold text-lg">Considering your complete experience with our agency, how likely would you recommend our services to others? <span class="text-red-800">*</span></div>

                                    <div class="ml-2 mb-3 mx-auto my-auto mb-5 d-flex justify-center text-center" style="margin-right: 50px ; margin-left: 50px">
                                        <v-btn-toggle 
                                            v-model="form.recommend_rate_score" 
                                            mandatory 
                                            v-for="option in recommendation_numbers "
                                            :key="option.value"
                                        >
                                            <v-btn
                                                :value="option.value"
                                                class=" mr-2 "
                                                :color="form.recommend_rate_score === option.color ? 'secondary' : 'secondary'"
                                                style="border-radius:40%"
                                        
                                            >
                                                <v-chip>
                                                    <label >{{ option.label }}</label>
                                                </v-chip>
                                            </v-btn>

                                        </v-btn-toggle>            
                                        </br>
                                        <div class="text-red-800" v-if="formSubmitted && !form.recommend_rate_score">{{ 'This selection is required' }}</div>
                                    </div>
                                

                            </v-card>
                        
                            <v-card 
                                data-aos="zoom-out-up" 
                                data-aos-duration="1000" 
                                data-aos-delay="500" 
                                class="mb-5 mx-auto"
                            >
                                <div class="p-3 mt-0 font-bold text-lg">Please write your comment/suggestions below.            
                                <span  v-if="form.is_complaint == true" class="text-red-800">*</span>
                                    <span  v-else="form.is_complaint == false" class="text-blue-400">(Optional)</span>
                                </div>
                                <v-container fluid>
                                    <v-textarea
                                        v-if="form.is_complaint == true"
                                        v-model="form.comment"
                                        placeholder="Input here!"
                                        :rules="[ (v) => !!v || formSubmitted && !form.comment ||  'This field is required',]"
                                    ></v-textarea>     
                                    <v-textarea
                                        v-else-if="form.is_complaint == false"
                                        v-model="form.comment"
                                        placeholder="Input here"
                                    ></v-textarea>                         
                                </v-container>

                                <div class="text-red-800 p-5" v-if="formSubmitted && form.is_complaint == true ">{{ 'This selection is required because you rate low to our services with the options above.' }}<br>
                                Please input the reason/s why you have rated low.</div>
                            </v-card>

                            <!-- <v-card 
                                data-aos="zoom-out-up" 
                                data-aos-duration="1000" 
                                data-aos-delay="500" 
                                class="mb-5 mx-auto"
                            >
                                <div class="p-3 mt-0 font-bold text-lg">Please indicate other important attribute/s which you think is important to your needs. (
                                    <span class="text-blue-400">Optional</span>
                                    )</div>
                                    <v-container fluid>
                                        <v-textarea
                                            v-model="form.indication"
                                            placeholder="Input here"
                                        ></v-textarea>
                                        
                                    </v-container>
                            </v-card> -->

                            <!-- <v-card 
                                data-aos="zoom-out-up" 
                                data-aos-duration="1000" 
                                data-aos-delay="500" 
                                class="mb-5 mx-auto"
                                >
                                <div class="p-3 mt-0 font-bold text-lg" >Please write your signature on the box. (
                                    <span class="text-blue-400">Optional</span>
                                    )</div>
                                    <v-container class="text-center">
                                        <v-row>
                                            <v-col >
                                            <div>
                                                <canvas class="signature-pad mb-3 mx-auto" ref="signaturePad">
                                                </canvas>
                                                </div>
                                                <v-btn @click="clearSignature" class="">Clear</v-btn>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                            </v-card> -->

                            <v-card
                                data-aos="zoom-out-up" 
                                data-aos-duration="1000" 
                                data-aos-delay="500" 
                                class="mb-5 mx-auto"
                            >
                                <v-row   class="mt-5 mb-5 text-center">

                                    <v-col cols="6" class="text-right">
                                        <a href="/" class="btn bg-secondary">
                                            <v-btn class="bg-secondary">Back</v-btn>
                                        </a>
                                    </v-col>
                                    <v-col cols="6" class="text-left">
                                        <v-btn color="success" type="submit" class="mr-2" prepend-icon="mdi-send" :disabled="form.processing || form.is_complaint && !form.comment">Submit</v-btn>
                                    </v-col>
                            </v-row >
                            </v-card>
                        </div>


                    </v-form>
                </v-col>
            </v-row>
        </v-card>

</template>
<style>
    canvas {
    border: 1px solid #000;
    }

</style>