<script setup lang="ts">
import { reactive, watch, ref, onMounted } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
const emit = defineEmits([ "input"]);
const props = defineProps({
    data: {
        type: Object,
        default: null,
    },
    value: {
        type: Boolean,
        default: false,
    },
    message: {
        type: Boolean,
        default: false,
    },
     status: {
        type: Boolean,
        default: false,
    },
});



const show_form_modal = ref(false);
watch(
    () => props.value,
    (value) => {
        show_form_modal.value = value;
    }
);


const message = ref('');
watch(
    () => props.message,
    (value) => {
        message.value = value;
    }
);

const status = ref('');
watch(
    () => props.status,
    (value) => {
        status.value = value;
    }
);

const csf_status =ref(null);

// watch(
//     () => csf_status.value,
//     (value) => {
//         if(value == true){
//             Swal.fire({
//                 title: "Success",
//                 icon: status.value,
//                 text: message.value,
//             });
//         }
//         else if(value == false){
//             Swal.fire({
//                 title: "Failed",
//                 icon: status.value,
//                 text: message.value,
//             });
//         }
//     }
// );



const submitForm = async () => {
   
   router.post('/csf_submission', props.data )
    
   
    emit("input", false);
};



const closeDialog = (value) => {
    emit("input", value);
};



</script>

<template>
    <v-dialog v-model="show_form_modal" width="400" scrollable persistent>
        <v-card >

        <v-card-item>
                <img src="/captcha/flat" alt="CAPTCHA" style="display: block; margin: 0 auto; margin-bottom:10px ">
                <v-row>
                    <v-col cols="12">
                        <div style="font-weight: bold; font-size:25px; text-align:center">Enter Captcha Code</div> 

                        <v-text-field
                            v-model="data.captcha"
                            variant="outlined"
                            
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>
    </v-card-item>
     <v-card-item class="mb-3">
         <v-row>
            <v-col cols="12" class="text-center" >
                    <v-btn
                    class="ma-2"
                    color="blue-grey-lighten-2"
                    @click="closeDialog(false)"
                >
                    <v-icon start icon="mdi-cancel"></v-icon>
                    Cancel
                </v-btn>
                    <v-btn
                    class="ma-2"
                    color="green-darken-1"
                    type="button"
                    @click="submitForm()"
                >
                    Submit
                    <v-icon end icon="mdi-check"></v-icon>
                </v-btn>
            </v-col>
        </v-row>
 </v-card-item>

            
              
        </v-card>
    </v-dialog>
</template>
