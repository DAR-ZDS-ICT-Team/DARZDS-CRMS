<script setup lang="ts">
import { reactive, watch, ref, onMounted } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
const emit = defineEmits(["reloadOffices", "input"]);
const props = defineProps({
    office: {
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

watch(
    () => props.office,
    (value) => {
        if(value){
            form.id = value.id;
            form.name = value.name;
            form.code = value.code;
            form.short_name = value.short_name;
            form.order = value.order;
        }
    }
     
);

const form = reactive({
    id: null,
    name:null,
    code:null,
    short_name:null,
    order:null,
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

watch(
    () => form.selected_division,
    (value) => {
        form.selected_section = null;
    }
);



const saveOffice = async () => {
   
    if(action_clicked.value == 'Add'){
        router.post('/offices/add', form );
 
    }
    else if(action_clicked.value == 'Update'){
        router.post('/offices/update', form );
    }
   
    emit("input", false);
    emit("reloadAccounts");

    form.id= '';
    form.name='';
    form.code='';
    form.short_name='';
    form.order='';
};



const closeDialog = (value) => {
    emit("input", value);
    emit("reloadOffice");

    form.id= '';
    form.name='';
    form.code='';
    form.short_name='';
    form.order='';
};



</script>

<template>
    <v-dialog v-model="show_form_modal" width="600" scrollable persistent>
        <v-card>
            <v-card-title class="bg-indigo">
                <span class="text-h5">{{ props.action }} Office</span>
            </v-card-title>
            <v-card-text>

                
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
                            prepend-icon="mdi-account"
                            label="Code*"
                            v-model="form.code"
                            variant="outlined"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>

                
                <v-row style="margin-bottom:-30px;">
                    <v-col cols="12" >
                        <v-text-field
                            prepend-icon="mdi-account"
                            label="Short Name*"
                            v-model="form.short_name"
                            variant="outlined"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>

                 <v-row style="margin-bottom:-30px;">
                    <v-col cols="12" >
                        <v-text-field
                            prepend-icon="mdi-account"
                            label="Order*"
                            v-model="form.order"
                            variant="outlined"
                            type="number"
                            required
                        ></v-text-field>
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
                        @click="saveOffice()"
                    >
                        Save
                        <v-icon end icon="mdi-check"></v-icon>
                    </v-btn>
                </div>
            </v-card-action>
        </v-card>
    </v-dialog>
</template>
