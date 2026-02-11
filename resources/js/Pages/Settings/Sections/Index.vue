<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    sections: {
        type: Array,
        default: () => [],
    },
    divisions: {
        type: Array,
        default: () => [],
    },
});

const showModal = ref(false);
const isEdit = ref(false);
const form = ref({ id: null, section_name: '', division_id: null });

const openAdd = () => {
    isEdit.value = false;
    form.value = { id: null, section_name: '', division_id: null };
    showModal.value = true;
};

const openEdit = (section) => {
    isEdit.value = true;
    form.value = {
        id: section.id,
        section_name: section.section_name,
        division_id: section.division_id,
    };
    showModal.value = true;
};

const saveSection = () => {
    if (isEdit.value) {
        router.post('/settings/sections/update', form.value);
    } else {
        router.post('/settings/sections/store', form.value);
    }
    showModal.value = false;
};

const deleteSection = (section) => {
    router.post('/settings/sections/destroy', { id: section.id });
};
</script>

<template>
    <AppLayout title="Sections">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sections</h2>
        </template>

        <div class="mx-8 mt-6">
            <div class="mb-4">
                <Link href="/settings">
                    <v-btn variant="outlined" prepend-icon="mdi-arrow-left">Back</v-btn>
                </Link>
            </div>
            <div class="mb-4 text-right">
                <v-btn color="primary" prepend-icon="mdi-plus" @click="openAdd">Add Section</v-btn>
            </div>
            <v-table density="compact">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Section Name</th>
                        <th>Division</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(section, index) in props.sections" :key="section.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ section.section_name }}</td>
                        <td>{{ section.division?.division_name }}</td>
                        <td class="text-right">
                            <v-btn size="small" class="mr-2" @click="openEdit(section)">Edit</v-btn>
                            <v-btn size="small" color="error" @click="deleteSection(section)">Delete</v-btn>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </div>

        <v-dialog v-model="showModal" width="500" persistent>
            <v-card>
                <v-card-title class="bg-indigo">
                    <span class="text-h5">{{ isEdit ? 'Update' : 'Add' }} Section</span>
                </v-card-title>
                <v-card-text>
                    <v-select
                        label="Division"
                        :items="props.divisions"
                        item-title="division_name"
                        item-value="id"
                        v-model="form.division_id"
                        variant="outlined"
                    />
                    <v-text-field label="Section Name" v-model="form.section_name" variant="outlined" />
                </v-card-text>
                <v-card-actions class="justify-end">
                    <v-btn variant="text" @click="showModal = false">Cancel</v-btn>
                    <v-btn color="green-darken-1" @click="saveSection">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AppLayout>
</template>
