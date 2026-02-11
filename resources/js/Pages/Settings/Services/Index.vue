<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    services: {
        type: Array,
        default: () => [],
    },
    divisions: {
        type: Array,
        default: () => [],
    },
    sections: {
        type: Array,
        default: () => [],
    },
});

const showModal = ref(false);
const isEdit = ref(false);
const form = ref({
    id: null,
    service_name: '',
    division_id: null,
    section_id: null,
    service_type: null,
    service_url: null,
    is_disabled: false,
});

const openAdd = () => {
    isEdit.value = false;
    form.value = {
        id: null,
        service_name: '',
        division_id: null,
        section_id: null,
        service_type: null,
        service_url: null,
        is_disabled: false,
    };
    showModal.value = true;
};

const openEdit = (service) => {
    isEdit.value = true;
    form.value = {
        id: service.id,
        service_name: service.service_name,
        division_id: service.division_id,
        section_id: service.section_id,
        service_type: service.service_type,
        service_url: service.service_url,
        is_disabled: !!service.is_disabled,
    };
    showModal.value = true;
};

const saveService = () => {
    if (isEdit.value) {
        router.post('/settings/services/update', form.value);
    } else {
        router.post('/settings/services/store', form.value);
    }
    showModal.value = false;
};

const deleteService = (service) => {
    router.post('/settings/services/destroy', { id: service.id });
};
</script>

<template>
    <AppLayout title="Services">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Services</h2>
        </template>

        <div class="mx-8 mt-6">
            <div class="mb-4">
                <Link href="/settings">
                    <v-btn variant="outlined" prepend-icon="mdi-arrow-left">Back</v-btn>
                </Link>
            </div>
            <div class="mb-4 text-right">
                <v-btn color="primary" prepend-icon="mdi-plus" @click="openAdd">Add Service</v-btn>
            </div>
            <v-table density="compact">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Service</th>
                        <th>Division</th>
                        <th>Section</th>
                        <th>Type</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(service, index) in props.services" :key="service.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ service.service_name }}</td>
                        <td>{{ service.division?.division_name }}</td>
                        <td>{{ service.section?.section_name || 'Direct Service' }}</td>
                        <td>{{ service.service_type }}</td>
                        <td class="text-right">
                            <v-btn size="small" class="mr-2" @click="openEdit(service)">Edit</v-btn>
                            <v-btn size="small" color="error" @click="deleteService(service)">Delete</v-btn>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </div>

        <v-dialog v-model="showModal" width="550" persistent>
            <v-card>
                <v-card-title class="bg-indigo">
                    <span class="text-h5">{{ isEdit ? 'Update' : 'Add' }} Service</span>
                </v-card-title>
                <v-card-text>
                    <v-text-field label="Service Name" v-model="form.service_name" variant="outlined" />
                    <v-select
                        label="Division"
                        :items="props.divisions"
                        item-title="division_name"
                        item-value="id"
                        v-model="form.division_id"
                        variant="outlined"
                    />
                    <v-select
                        label="Section"
                        :items="props.sections"
                        item-title="section_name"
                        item-value="id"
                        v-model="form.section_id"
                        variant="outlined"
                        clearable
                    />
                    <v-select
                        label="Service Type"
                        :items="['Internal', 'External']"
                        v-model="form.service_type"
                        variant="outlined"
                    />
                    <v-text-field label="Service URL (optional)" v-model="form.service_url" variant="outlined" />
                    <v-switch label="Disabled" v-model="form.is_disabled" color="red" />
                </v-card-text>
                <v-card-actions class="justify-end">
                    <v-btn variant="text" @click="showModal = false">Cancel</v-btn>
                    <v-btn color="green-darken-1" @click="saveService">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AppLayout>
</template>
