<script setup lang="ts">
import { reactive, watch, ref, onMounted } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
const emit = defineEmits(["reloadAccounts", "input"]);
const props = defineProps({
    account: {
        type: Object,
        default: null,
    },
    value: {
        type: Boolean,
        default: false,
    },
    regions: {
        type: Object,
        default: null,
    },
    action: {
        type: String,
    }

});

watch(
    () => props.account,
    (value) => {
        if(value){
            form.id = value.id;
            form.name = value.name;
            form.email = value.email;
            form.selected_region = value.region;
        }
    }
     
);

const form = reactive({
    id: null,
    name:null,
    email: null,
    selected_region: [],
});


const show_form_modal = ref(false);
watch(
    () => props.value,
    (value) => {
        show_form_modal.value = value;
    }
);


const action_clicked = ref('');
watch(
    () => props.action,
    (value) => {
        action_clicked.value = value;
    }
);

const saveAccount = async () => {
   
    if(action_clicked.value == 'Add'){
        router.post('/accounts/add', form );
 
    }
    else if(action_clicked.value == 'Update'){
        router.post('/accounts/update', form );
    }
   
    emit("input", false);
    emit("reloadAccounts");
};



const closeDialog = (value) => {
    emit("input", value);
    emit("reloadAccounts");
};



</script>

<template>
    <v-dialog v-model="show_form_modal" width="600" scrollable persistent>
        <v-card>
            <v-card-title class="bg-indigo">
                <span class="text-h5">{{ props.action }} Account</span>
            </v-card-title>
            <v-card-text>
                
                <v-row style="margin-bottom:-30px;">
                    <v-col cols="12" >
                        <v-text-field
                            prepend-icon="mdi-account"
                            label="Name*"
                            v-model="form.name"
                            variant="solo"
                        ></v-text-field>
                    </v-col>
                </v-row>

                <v-row style="margin-bottom:-30px;">
                    <v-col cols="12" >
                        <v-text-field
                            prepend-icon="mdi-email"
                            label="Email*"
                            v-model="form.email"
                            variant="solo"
                            type="email"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>

                <v-row>
                   <v-col cols="12">
                        <v-select
                            prepend-icon="mdi-map-marker"
                            label="Region*"
                            v-model="form.selected_region"
                            variant="solo"
                            :items="regions"
                            item-title="name"
                            item-value="id"
                            required
                        ></v-select>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-spacer></v-spacer>
            <v-card-action>
                <v-divider></v-divider>
                <div style="text-align: end">
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
                        @click="saveAccount()"
                    >
                        Save
                        <v-icon end icon="mdi-check"></v-icon>
                    </v-btn>
                </div>
            </v-card-action>
        </v-card>
    </v-dialog>
</template>
