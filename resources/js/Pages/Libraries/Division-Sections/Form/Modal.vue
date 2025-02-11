<script setup lang="ts">
import { reactive, watch, ref, onMounted } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
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

    action_clicked: {
        type: String,
        default: null,
    },

    selected_division: {
        type: Object,
        default: null,
    },

});

watch(
    () => props.account,
    (value) => {
        if(value){
            form.id = value.id;
            form.name = value.name;
            form.email = value.email;
            form.selected_office = value.office;
        }
    }
     
);

const form = reactive({
    division_id: null,
    section_name:null,
});


const show_form_modal = ref(false);
watch(
    () => props.value,
    (value) => {
        show_form_modal.value = value;
    }
);


const action = ref('');
watch(
    () => props.action_clicked,
    (value) => {
        action.value = value;
    }
);

watch(
    () => form.selected_division,
    (value) => {
        form.selected_section = null;
    }
);

const closeDialog = (value) => {
    emit("input", value);
};

const saveDivision = () => {
    if(action.value == 'add_new_division'){
        router.post('/divisions/add', form );
    }
    else if(action.value == 'add_new_section'){
        form.division_id = props.selected_division.id;
        router.post('/divisions/section/add', form );
    }
   
    emit("input", false);
};





</script>

<template>
    <v-dialog v-model="show_form_modal" width="600" scrollable persistent>
        <v-card>
            <v-card-title class="bg-indigo mb-5">
                <span class="text-h5" v-if="props.action_clicked == 'add_new_division'">Add New Division</span>
                 <span class="text-h5" v-if="props.action_clicked == 'add_new_section'">Add New Section</span>
            </v-card-title>
            <v-card-text>

                
            <v-row style="margin-bottom:-30px;" v-if="props.action_clicked=='add_new_division'">
                <v-col cols="12" >
                    <v-text-field
                        prepend-icon="mdi-account"
                        label="Division Name"
                        v-model="form.division_name"
                        variant="outlined"
                    ></v-text-field>
                </v-col>
            </v-row>


            <v-row style="margin-bottom:-30px;" v-if="props.action_clicked=='add_new_section'">
                <v-col cols="12" >
                      <v-text-field
                        prepend-icon="mdi-account"
                        label="Division"
                        v-model="props.selected_division.division_name"
                        variant="outlined"
                    ></v-text-field>
                </v-col>
                 <v-col cols="12" >
                      <v-text-field
                        prepend-icon="mdi-account"
                        label="Section Name"
                        v-model="form.section_name"
                        variant="outlined"
                    ></v-text-field>
                </v-col>
            </v-row>



            </v-card-text>
            <v-spacer></v-spacer>
            <v-card-action >
                <v-divider></v-divider>
                <div style="text-align: center">
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
                        @click="saveDivision()"
                    >
                        Save
                        <v-icon end icon="mdi-check"></v-icon>
                    </v-btn>
                </div>
            </v-card-action>
        </v-card>
    </v-dialog>
</template>
