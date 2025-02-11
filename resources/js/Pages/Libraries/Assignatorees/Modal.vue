<script setup lang="ts">
import { reactive, watch, ref, onMounted } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
const emit = defineEmits(["reloadAssignatorees", "input"]);
const props = defineProps({
    assignatoree: {
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
    () => props.assignatoree,
    (value) => {
        if(value){
            form.id = value.id;
            form.name = value.name;
            form.designation = value.designation;
        }
    }
     
);

const form = reactive({
    id: null,
    name:null,
    designation:null,
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




const saveAssignatoree = async () => {
   
    if(action_clicked.value == 'Add'){
        router.post('/assignatorees/add', form );
 
    }
    else if(action_clicked.value == 'Update'){
        router.post('/assignatorees/update', form );
    }

   
    emit("input", false);
    emit("reloadAssignatorees");

    form.id= '';
    form.name='';
    form.designation='';
};



const closeDialog = (value) => {
    emit("input", value);
    emit("reloadAssignatorees");

    form.id= '';
    form.name='';
    form.designation='';
};



</script>

<template>
    <v-dialog v-model="show_form_modal" width="600" scrollable persistent>
        <v-card>
            <v-card-title class="bg-indigo">
                <span class="text-h5">{{ props.action }} Assignatoree</span>
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
                            label="Designation*"
                            v-model="form.designation"
                            variant="outlined"
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
                        @click="saveAssignatoree()"
                    >
                        Save
                        <v-icon end icon="mdi-check"></v-icon>
                    </v-btn>
                </div>
            </v-card-action>
        </v-card>
    </v-dialog>
</template>
