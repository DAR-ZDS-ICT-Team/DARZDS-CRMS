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
    selected_section: {
        type: Object,
        default: null,
    },
});

const form = reactive({
    division_id: null,
    division_name: null,
    section_id: null,
    section_name: null,
    service_name: null,
});

// Watch for changes in selected division
watch(
    () => props.selected_division,
    (value) => {
        if (value) {
            form.division_id = value.id;
            form.division_name = value.division_name;
        }
    }
);

// Watch for changes in selected section
watch(
    () => props.selected_section,
    (value) => {
        if (value) {
            form.section_id = value.id;
            form.section_name = value.section_name;
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

const action = ref('');
watch(
    () => props.action_clicked,
    (value) => {
        action.value = value;
    }
);

const closeDialog = (value) => {
    emit("input", value);
};

const save = () => {
    if (action.value === 'add_new_division') {
        router.post('/divisions/add', form);
    }
    else if (action.value === 'add_new_section') {
        form.division_id = props.selected_division.id;
        router.post('/divisions/section/add', form);
    }
    else if (action.value === 'add_new_service') {
        if (props.selected_section) {
            // Add service to a section
            form.division_id = props.selected_division.id;
            form.section_id = props.selected_section.id;
            router.post('/divisions/section/service/add', form);
        } else {
            // Add service directly to division
            form.division_id = props.selected_division.id;
            router.post('/divisions/service/add', form);
        }
    }
    
    emit("input", false);
};
</script>

<template>
    <v-dialog v-model="show_form_modal" width="600" scrollable persistent>
        <v-card>
            <v-card-title class="bg-indigo mb-5">
                <span class="text-h5" v-if="action_clicked === 'add_new_division'">Add New Division</span>
                <span class="text-h5" v-if="action_clicked === 'add_new_section'">Add New Section</span>
                <span class="text-h5" v-if="action_clicked === 'add_new_service'">Add New Service</span>
            </v-card-title>
            <v-card-text>
                <!-- Add Division Form -->
                <v-row style="margin-bottom:-30px;" v-if="action_clicked === 'add_new_division'">
                    <v-col cols="12">
                        <v-text-field
                            prepend-icon="mdi-domain"
                            label="Division Name"
                            v-model="form.division_name"
                            variant="outlined"
                        ></v-text-field>
                    </v-col>
                </v-row>

                <!-- Add Section Form -->
                <v-row style="margin-bottom:-30px;" v-if="action_clicked === 'add_new_section'">
                    <v-col cols="12">
                        <v-text-field
                            prepend-icon="mdi-domain"
                            label="Division"
                            v-model="props.selected_division.division_name"
                            variant="outlined"
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" v-if="props.selected_section">
                        <v-text-field
                            prepend-icon="mdi-office-building"
                            label="Section"
                            v-model="props.selected_section.section_name"
                            variant="outlined"
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                            prepend-icon="mdi-cogs"
                            label="Service Name"
                            v-model="form.service_name"
                            variant="outlined"
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                            prepend-icon="mdi-office-building"
                            label="Section Name"
                            v-model="form.section_name"
                            variant="outlined"
                        ></v-text-field>
                    </v-col>
                </v-row>

    <!-- Add Service Form -->
    <v-row style="margin-bottom:-30px;" v-if="action_clicked === 'add_new_service'">
        <v-col cols="12">
            <v-text-field
                prepend-icon="mdi-domain"
                label="Division"
                v-model="props.selected_division.division_name"
                variant="outlined"
                disabled
            ></v-text-field>
        </v-col>
        <v-col cols="12" v-if="props.selected_section">
            <v-text-field
                prepend-icon="mdi-office-building"
                label="Section"
                v-model="props.selected_section.section_name"
                variant="outlined"
                disabled
            ></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-text-field
                prepend-icon="mdi-cogs"
                label="Service Name"
                v-model="form.service_name"
                variant="outlined"
                required
            ></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-textarea
                prepend-icon="mdi-information-outline"
                label="Service Description (Optional)"
                v-model="form.service_description"
                variant="outlined"
                rows="3"
                placeholder="Enter a brief description of this service"
            ></v-textarea>
        </v-col>
        <v-col cols="12">
            <v-select
                prepend-icon="mdi-account-group"
                label="Available To"
                v-model="form.service_availability"
                :items="['Internal Only', 'External Only', 'Both Internal and External']"
                variant="outlined"
            ></v-select>
        </v-col>
        <v-col cols="12">
            <v-checkbox
                label="Active"
                v-model="form.service_active"
                hint="Is this service currently active and available?"
                persistent-hint
            ></v-checkbox>
        </v-col>
    </v-row>

    </v-card-text>
                <v-card-actions>
                    <v-divider></v-divider>
                    <div style="text-align: center; width: 100%;">
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
                            @click="save()"
                        >
                            Save
                            <v-icon end icon="mdi-check"></v-icon>
                        </v-btn>
                    </div>
                </v-card-actions>
            </v-card>
        </v-dialog>
</template>