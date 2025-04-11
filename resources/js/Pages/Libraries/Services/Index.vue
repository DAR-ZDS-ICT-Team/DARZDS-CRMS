<script setup>
import VueMultiselect from "vue-multiselect";
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref, computed, onMounted } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    services: Object,
    divisions: Object,
    sections: Object,
    user: Object,
});

onMounted(() => {
    // Debug: Log the data structure to see how divisions and sections are represented
    console.log('Services:', props.services);
    console.log('Divisions:', props.divisions);
    console.log('Sections:', props.sections);
});

const form = reactive({
  division_id: null,
  division_name: null,
  section_id: null,
  section_name: null,
  service_id: null,
  service_name: null,
  client_type: '',
  search: '',
  filter_division: null,
  filter_section: null
});

// For pagination
const currentPage = ref(1);
const itemsPerPage = ref(10);

// For service filtering
const filteredServices = computed(() => {
  if (!props.services || !props.services.data) {
    return [];
  }
  
  let result = props.services.data;
  
  // Filter by search term
  if (form.search) {
    const searchTerm = form.search.toLowerCase();
    result = result.filter(service => 
      service && service.service_name && 
      service.service_name.toLowerCase().includes(searchTerm)
    );
  }
  
  // Filter by division
  if (form.filter_division) {
    result = result.filter(service => 
      service.division_id == form.filter_division
    );
  }
  
  // Filter by section
  if (form.filter_section) {
    result = result.filter(service => 
      service.section_id == form.filter_section
    );
  }
  
  return result;
});

// Computed property for paginated services
const paginatedServices = computed(() => {
  if (!filteredServices.value || filteredServices.value.length === 0) {
    return [];
  }
  
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredServices.value.slice(start, end);
});

// For total pages calculation
const totalPages = computed(() => {
  if (!filteredServices.value) return 1;
  return Math.max(1, Math.ceil(filteredServices.value.length / itemsPerPage.value));
});

// Get division name - multiple ways to handle different data structures
const getDivisionName = (service) => {
  // Using the divisions store if necessary (for lookup)
  if (service && service.division_id && props.divisions && props.divisions.data) {
    const division = props.divisions.data.find(d => d.id === service.division_id);
    if (division) return division.division_name;
  }
  
  // Direct access if available
  if (service && service.division && service.division.division_name) {
    return service.division.division_name;
  }
  
  // Fallback to raw attribute if it exists
  if (service && service.division_name) {
    return service.division_name;
  }
  
  return 'N/A';
};

// Get section name - multiple ways to handle different data structures
const getSectionName = (service) => {
  // If no section_id, it's a direct service
  if (!service || !service.section_id) {
    return 'Direct Service';
  }
  
  // Using the sections store if necessary (for lookup)
  if (service.section_id && props.sections && props.sections.data) {
    const section = props.sections.data.find(s => s.id === service.section_id);
    if (section) return section.section_name;
  }
  
  // Direct access if available
  if (service.section && service.section.section_name) {
    return service.section.section_name;
  }
  
  // Fallback to raw attribute if it exists
  if (service.section_name) {
    return service.section_name;
  }
  
  return 'N/A';
};

const rating = (
        division_id, 
        division_name, 
        section_id = null, 
        section_name = null, 
        service_id, 
        service_name, 
        sub_service_id = null, 
        sub_service_name = null
    ) => {
        const params = {
            division_id,
            division_name,
            client_type: form?.client_type || '',
        };

        // Add section info if it exists and is valid
        if (section_id != null) {
            params.section_id = section_id;
            params.section_name = section_name || '';
        }

        // Add service info if it exists and is valid
        if (service_id != null) {
            params.service_id = service_id;
            params.service_name = service_name || '';
            
            // Track if this is a direct service (no section)
            params.direct_service = (section_id == null);
        }

        // Add sub_service info if it exists and is valid
        if (sub_service_id != null) {
            params.sub_service_id = sub_service_id;
            params.sub_service_name = sub_service_name || '';
        }

        // Update form with selected values
        form.division_name = division_name;
        form.division_id = division_id;
        form.section_id = section_id;
        form.section_name = section_name;
        form.service_id = service_id;
        form.service_name = service_name;
        form.sub_service_id = sub_service_id;
        form.sub_service_name = sub_service_name;
        
        // Navigate to CSI page with parameters
        router.get('/csi', params, { preserveState: true });
    };


const goToServiceCreate = () => {
    router.get('/divisions/service/add');
};

const goToServiceEdit = (service) => {
    router.get(`/services/${service.id}/edit`);
};

const goViewPage = (division_id, division_name, section_id = null, section_name = null, service_id = null, service_name = null) => {
    form.division_id = division_id;
    form.division_name = division_name;
    form.section_id = section_id;
    form.section_name = section_name;
    form.service_id = service_id;
    form.service_name = service_name;
    router.get('/csi/view', { data: form, preserveState: true });
};

const deleteService = (service) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/services/${service.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        'Deleted!',
                        'Service has been deleted.',
                        'success'
                    )
                }
            });
        }
    });
};
</script>

<template>
    <AppLayout title="Services">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                 Services Management
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <v-card>
                        <!-- Controls Section -->
                        <v-row class="m-3">
                            <v-col cols="12" sm="4" md="3">
                                <v-text-field
                                    v-model="form.search"
                                    prepend-icon="mdi-magnify"
                                    label="Search Services"
                                    placeholder="Enter service name..."
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                            
                            <v-col cols="12" sm="3" md="3">
                                <v-select
                                    v-if="props.divisions && props.divisions.data"
                                    v-model="form.filter_division"
                                    :items="props.divisions.data"
                                    item-title="division_name"
                                    item-value="id"
                                    label="Filter by Division"
                                    clearable
                                    variant="outlined"
                                    density="compact"
                                ></v-select>
                            </v-col>
                            
                            <v-col cols="12" sm="3" md="3">
                                <v-select
                                    v-if="props.sections && props.sections.data"
                                    v-model="form.filter_section"
                                    :items="props.sections.data"
                                    item-title="section_name"
                                    item-value="id"
                                    label="Filter by Section"
                                    clearable
                                    variant="outlined"
                                    density="compact"
                                ></v-select>
                            </v-col>
                            
                            <v-col cols="12" sm="12" md="3" class="text-right">
                                <v-btn @click="goToServiceCreate()" prepend-icon="mdi-plus" color="primary" size="small" class="mt-2">
                                    Add New Service
                                </v-btn>
                            </v-col>
                        </v-row>
                       
                        <!-- Services Table -->
                        <v-table>
                            <thead>
                                <tr>
                                    <th class="text-left">ID</th>
                                    <th class="text-left">Service Name</th>
                                    <th class="text-left">Division</th>
                                    <th class="text-left">Section</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="service in paginatedServices" :key="service.id">
                                    <td>{{ service.id }}</td>
                                    <td class="font-medium">{{ service.service_name }}</td>
                                    <td>{{ getDivisionName(service) }}</td>
                                    <td>{{ getSectionName(service) }}</td>
                                    <td class="text-center">
                                        <div class="flex justify-center gap-2">
                                            <v-btn 
                                                prepend-icon="mdi-eye" 
                                                size="x-small" 
                                                color="info"
                                                @click="goViewPage(
                                                    service.division_id, 
                                                    getDivisionName(service), 
                                                    service.section_id, 
                                                    getSectionName(service), 
                                                    service.id, 
                                                    service.service_name
                                                )"
                                            >
                                                VIEW
                                            </v-btn>
                                            
                                            <v-btn 
                                                prepend-icon="mdi-star" 
                                                size="x-small" 
                                                color="warning"
                                                @click="rating(
                                                    service.division_id, 
                                                    getDivisionName(service), 
                                                    service.section_id, 
                                                    getSectionName(service), 
                                                    service.id, 
                                                    service.service_name,
                                                    service.sub_service_id || null,
                                                    service.sub_service_name || null
                                                )"
                                            >
                                                RATE
                                            </v-btn>
                                            
                                            <v-btn 
                                                v-if="user.account_type == 'admin'"
                                                prepend-icon="mdi-pencil" 
                                                size="x-small" 
                                                color="orange"
                                                @click="goToServiceEdit(service)"
                                            >
                                                EDIT
                                            </v-btn>
                                            
                                            <v-btn 
                                                v-if="user.account_type == 'admin'"
                                                prepend-icon="mdi-delete" 
                                                size="x-small" 
                                                color="error"
                                                @click="deleteService(service)"
                                            >
                                                DELETE
                                            </v-btn>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Empty state when no services found -->
                                <tr v-if="paginatedServices.length === 0">
                                    <td colspan="5" class="text-center py-8">
                                        <v-icon size="large" class="mb-2">mdi-database-off</v-icon>
                                        <p>No services found. Please adjust your filters or add a new service.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-center my-4">
                            <v-pagination
                                v-model="currentPage"
                                :length="totalPages"
                                :total-visible="5"
                                rounded
                            ></v-pagination>
                        </div>
                    </v-card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
.v-table {
    border-radius: 8px;
    overflow: hidden;
}

.v-table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.gap-2 {
    gap: 0.5rem;
}

.justify-center {
    justify-content: center;
}

.flex {
    display: flex;
}
</style>