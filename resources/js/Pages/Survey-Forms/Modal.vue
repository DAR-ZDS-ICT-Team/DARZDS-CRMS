<script setup lang="ts">
import { reactive, watch, ref, onMounted } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
const emit = defineEmits(["input"]);
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
        type: String,
        default: "",
    },
    status: {
        type: String,
        default: "",
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

const csf_status = ref(null);
const isSubmitting = ref(false);

const submitForm = async () => {
    if (!props.data || !props.data.captcha) {
        Swal.fire({
            title: "Error",
            icon: "error",
            text: "Please enter the CAPTCHA code",
        });
        return;
    }
    
    isSubmitting.value = true;
    
    // Debug log what we're sending
    console.log('Submitting from modal with data:', {
        office_id: props.data.office_id,
        division_id: props.data.division_id,
        section_id: props.data.section_id,
        service_id: props.data.service_id,
        captcha: props.data.captcha
        // Add other fields as needed
    });
    
    // Fix for section_id handling
    const formData = {...props.data};
    if (!formData.section_id || formData.section_id === 'undefined') {
        formData.section_id = null;
    }

    router.post('/csf_submission', formData, {
        onSuccess: () => {
            isSubmitting.value = false;
            emit("input", false);
            Swal.fire({
                title: "Success",
                icon: "success",
                text: "Your form has been submitted successfully.",
            });
        },
        onError: (errors) => {
            isSubmitting.value = false;
            console.error('Submission errors:', errors);
            let errorMessage = "There was an error submitting your form.";
            if (errors && Object.keys(errors).length > 0) {
                errorMessage = Object.values(errors).join('<br>');
            }
            Swal.fire({
                title: "Error",
                icon: "error",
                html: errorMessage,
            });
        }
    });
};

const closeDialog = (value) => {
    emit("input", value);
};
</script>

<template>
    <v-dialog v-model="show_form_modal" width="400" scrollable persistent>
        <v-card>
            <v-card-item>
                <img src="/captcha/flat" alt="CAPTCHA" style="display: block; margin: 0 auto; margin-bottom:10px">
                <v-row>
                    <v-col cols="12">
                        <div style="font-weight: bold; font-size:25px; text-align:center">Enter Captcha Code</div>
                        <v-text-field
                            v-model="data.captcha"
                            variant="outlined"
                            :rules="[v => !!v || 'CAPTCHA is required']"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-card-item>
            <v-card-item class="mb-3">
                <v-row>
                    <v-col cols="12" class="text-center">
                        <v-btn
                            class="ma-2"
                            color="blue-grey-lighten-2"
                            @click="closeDialog(false)"
                            :disabled="isSubmitting"
                        >
                            <v-icon start icon="mdi-cancel"></v-icon>
                            Cancel
                        </v-btn>
                        <v-btn
                            class="ma-2"
                            color="green-darken-1"
                            type="button"
                            @click="submitForm()"
                            :loading="isSubmitting"
                            :disabled="isSubmitting"
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