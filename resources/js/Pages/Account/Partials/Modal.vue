<script setup lang="ts">
import axios from 'axios';
import { reactive, watch, ref, onMounted } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
const emit = defineEmits(["reloadAccounts", "input"]);
const props = defineProps({
    data: {
        type: Object,
        default: null,
    },
    account: {
        type: Object,
        default: null,
    },

    value: {
        type: Boolean,
        default: false,
    },

    action: {
        type: String,
    }

});

const form = reactive({
    id: null,
    name:null,
    email: null,
    password: null,
    designation:'',
    office: null,
    account_type: null,
    division: null,
    section: null,
});

watch(
    () => props.account,
    (value) => {
        if( props.action == "Update"){
            form.id = value.id;
            form.name = value.name;
            form.designation = value.designation;
            form.email = value.email;
            form.account_type = value.account_type;
            form.office = value.office.id;
            form.division = value.division.id;
            if(value.section){
                form.section = value.section.id;
            }
          
        }

    }
     
);




const show_form_modal = ref(false);
watch(
    () => props.value,
    (value) => {
        show_form_modal.value = value;
    }
);


const action_clicked = ref('');
const sections = ref();



watch(
    () => props.action,
    (value) => {
        action_clicked.value = value;
    }
);

watch(
    () => form.division,
    (value) => {
        if(value){
            sections.value = fetchSections(value);
        }
    }
);



const fetchSections = (code) => {
    axios.get('/division/sections', {
        params: {
            option: 'section',
            code: code,
        }
    })
    .then(response => {
        sections.value = response.data;
    })
    .catch(err => console.log(err));
};



  // Reactive variable to control password visibility
  const showPassword = ref(false);


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
            <v-card-text v-if="props.account.account_type != 'admin'  ">  
                <v-row style="margin-bottom:-30px;">
                    <v-col cols="12" >
                        <v-text-field
                            prepend-icon="mdi-account"
                            label="Name*"
                            v-model="form.name"
                            variant="outlined"
                        ></v-text-field>
                    </v-col>
                </v-row>
                

                <v-row style="margin-bottom:-30px;">
                    <v-col cols="12" >
                        <v-text-field
                            prepend-icon="mdi-email"
                            label="Email*"
                            v-model="form.email"
                            variant="outlined"
                            type="email"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row style="margin-bottom:-30px;">
                    <v-col cols="9">
                        <v-text-field
                            v-if="props.action == 'Add'"
                            prepend-icon="mdi-lock"
                            label="Password*"
                            v-model="form.password"
                            variant="outlined"
                            :type="showPassword ? 'text' : 'password'"
                            required
                        ></v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <v-checkbox
                            v-if="props.action == 'Add'"
                            v-model="showPassword"
                            label="Show"
                        ></v-checkbox>
                    </v-col>
                </v-row>

                <v-row style="margin-bottom:-30px;">
                   <v-col cols="12">
                        <v-select
                            prepend-icon="mdi-map-marker"
                            label="Office*"
                            v-model="form.office"
                            variant="outlined"
                            :items="data.offices"
                            item-title="name"
                            item-value="id"
                            required
                        ></v-select>
                    </v-col>
                </v-row>

                 <v-row style="margin-bottom:-30px;">
                   <v-col cols="12">
                        <v-select
                            prepend-icon="mdi-account-circle"
                            label="Account_type*"
                            v-model="form.account_type"
                            variant="outlined"
                            :items="['user','admin','planning','special-user']"
                            item-title="name"
                            clearable
                            required
                        ></v-select>
                    </v-col>
                </v-row>

                <v-row style="margin-bottom:-30px;">
                    <v-col cols="12" >
                        <v-text-field
                            v-if="form.account_type == 'user' || form.account_type == 'special-user'"
                            prepend-icon="mdi-account"
                            label="Designation*"
                            v-model="form.designation"
                            variant="outlined"
                        ></v-text-field>
                    </v-col>
                </v-row>
                

                  <v-row style="margin-bottom:-30px;">
                   <v-col cols="12">
                        <v-select
                            v-if="form.account_type == 'user' || form.account_type == 'special-user'"
                            prepend-icon="mdi-map-marker"
                            label="Division*"
                            v-model="form.division"
                            variant="outlined"
                            :items="data.divisions"
                            item-title="division_name"
                            item-value="id"
                            clearable
                            required
                        ></v-select>
                        
                    </v-col>
                </v-row>

                <v-row style="margin-bottom:-30px;">
                    <v-col cols="12">
                        <v-select
                            prepend-icon="mdi-map-marker"
                            label="Section*"
                            v-if="form.account_type == 'user' || form.account_type == 'special-user' || form.division  "
                            v-model="form.section"
                            variant="outlined"
                            :items="sections"
                            item-title="section_name"
                            item-value="id"
                            clearable
                            required
                        ></v-select>
                    </v-col>
                </v-row>


            </v-card-text>
            <v-card-text v-else>
                This account is an Administrator cannot be edited
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
