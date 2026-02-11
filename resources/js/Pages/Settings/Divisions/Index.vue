<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    divisions: {
        type: Array,
        default: () => [],
    },
});

const showModal = ref(false);
const isEdit = ref(false);
const form = ref({ id: null, division_name: '' });

const openAdd = () => {
    isEdit.value = false;
    form.value = { id: null, division_name: '' };
    showModal.value = true;
};

const openEdit = (division) => {
    isEdit.value = true;
    form.value = { id: division.id, division_name: division.division_name };
    showModal.value = true;
};

const saveDivision = () => {
    if (isEdit.value) {
        router.post('/settings/divisions/update', form.value);
    } else {
        router.post('/settings/divisions/store', form.value);
    }
    showModal.value = false;
};

const deleteDivision = (division) => {
    router.post('/settings/divisions/destroy', { id: division.id });
};
</script>

<template>
    <AppLayout title="Divisions">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Divisions</h2>
        </template>

        <div class="mx-8 mt-6">
            <div class="mb-4">
                <Link href="/settings">
                    <v-btn variant="outlined" prepend-icon="mdi-arrow-left">Back</v-btn>
                </Link>
            </div>
            <div class="mb-4 text-right">
                <v-btn color="primary" prepend-icon="mdi-plus" @click="openAdd">Add Division</v-btn>
            </div>
            <v-table density="compact">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Division Name</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(division, index) in props.divisions" :key="division.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ division.division_name }}</td>
                        <td class="text-right">
                            <v-btn size="small" class="mr-2" @click="openEdit(division)">Edit</v-btn>
                            <v-btn size="small" color="error" @click="deleteDivision(division)">Delete</v-btn>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </div>

        <v-dialog v-model="showModal" width="500" persistent>
            <v-card>
                <v-card-title class="bg-indigo">
                    <span class="text-h5">{{ isEdit ? 'Update' : 'Add' }} Division</span>
                </v-card-title>
                <v-card-text>
                    <v-text-field label="Division Name" v-model="form.division_name" variant="outlined" />
                </v-card-text>
                <v-card-actions class="justify-end">
                    <v-btn variant="text" @click="showModal = false">Cancel</v-btn>
                    <v-btn color="green-darken-1" @click="saveDivision">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AppLayout>
</template>
