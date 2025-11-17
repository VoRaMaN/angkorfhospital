<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { store, update } from '@/routes/medical-services';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Plus } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

interface Props {
    medicalServices: Array<{
        id: number;
        name: string;
        description: string | null;
        type: string;
        price: number;
        created_at: string;
        updated_at: string;
    }>;
}

interface Service {
    id: number;
    name: string;
    description: string | null;
    type: string;
    price: number;
    created_at: string;
    updated_at: string;
}

const props = withDefaults(defineProps<Props>(), {
    medicalServices: () => [],
});

const { hasPermission } = useAuth();

const showModal = ref(false);
const editingService = ref<Service | null>(null);
const showViewModal = ref(false);
const viewingService = ref<Service | null>(null);

const formData = reactive({
    name: '',
    description: '',
    type: 'procedure',
    price: 0,
});

const errors = reactive<Record<string, string>>({
    name: '',
    description: '',
    type: '',
    price: '',
});

const openCreateModal = () => {
    editingService.value = null;
    formData.name = '';
    formData.description = '';
    formData.type = 'procedure';
    formData.price = 0;
    Object.keys(errors).forEach((key) => (errors[key] = ''));
    showModal.value = true;
};

const openEditModal = (service: Service) => {
    editingService.value = service;
    formData.name = service.name;
    formData.description = service.description || '';
    formData.type = service.type;
    formData.price = service.price;
    Object.keys(errors).forEach((key) => (errors[key] = ''));
    showModal.value = true;
};

const openViewModal = (service: Service) => {
    viewingService.value = service;
    showViewModal.value = true;
};

const submitForm = async () => {
    try {
        const url = editingService.value
            ? update(editingService.value.id).url
            : store().url;
        const method = editingService.value ? 'patch' : 'post';
        const response = await axios[method](url, formData);
        if (editingService.value) {
            // update the service in the list
            const index = props.medicalServices.findIndex(
                (s) => s.id === editingService.value!.id,
            );
            if (index !== -1) {
                props.medicalServices[index] = response.data;
            }
        } else {
            // add new service
            props.medicalServices.push(response.data);
        }
        showModal.value = false;
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            Object.assign(errors, error.response.data.errors);
        }
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medical Services',
        href: '#',
    },
];

const getTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        procedure: 'bg-blue-100 text-blue-800',
        imaging: 'bg-green-100 text-green-800',
        consultation: 'bg-purple-100 text-purple-800',
        therapy: 'bg-orange-100 text-orange-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
};
</script>

<template>

    <Head title="Medical Services" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Medical Services</h1>
                    <p class="text-muted-foreground">
                        Manage medical services for procedures and imaging
                    </p>
                </div>
                <Button @click="openCreateModal" v-if="hasPermission('create_medical_services') || true">
                    <Plus class="size-4" />
                    Create Medical Service
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead class="text-right">Price</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="service in props.medicalServices" :key="service.id">
                            <TableCell class="font-medium">
                                {{ service.name }}
                            </TableCell>
                            <TableCell>
                                <Badge :class="getTypeColor(service.type)">
                                    {{ service.type }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                {{ service.description || '-' }}
                            </TableCell>
                            <TableCell class="text-right">
                                {{ formatPrice(service.price) }}
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" @click="openViewModal(service)">
                                        View
                                    </Button>
                                    <Button variant="outline" size="sm" @click="openEditModal(service)">
                                        Edit
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.medicalServices.length === 0">
                            <TableCell colspan="5" class="py-8 text-center">
                                No medical services found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <Dialog v-model:open="showModal">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{
                        editingService
                            ? 'Edit Medical Service'
                            : 'Create Medical Service'
                    }}</DialogTitle>
                    <DialogDescription>
                        {{
                            editingService
                                ? 'Update the medical service details.'
                                : 'Add a new medical service.'
                        }}
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div>
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="formData.name" />
                        <p v-if="errors.name" class="text-sm text-red-500">
                            {{ errors.name[0] }}
                        </p>
                    </div>
                    <div>
                        <Label for="description">Description</Label>
                        <Textarea id="description" v-model="formData.description" />
                        <p v-if="errors.description" class="text-sm text-red-500">
                            {{ errors.description[0] }}
                        </p>
                    </div>
                    <div>
                        <Label for="type">Type</Label>
                        <Select v-model="formData.type">
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="procedure">Procedure</SelectItem>
                                <SelectItem value="imaging">Imaging</SelectItem>
                                <SelectItem value="consultation">Consultation</SelectItem>
                                <SelectItem value="therapy">Therapy</SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="errors.type" class="text-sm text-red-500">
                            {{ errors.type[0] }}
                        </p>
                    </div>
                    <div>
                        <Label for="price">Price</Label>
                        <Input id="price" type="number" step="0.01" v-model.number="formData.price" />
                        <p v-if="errors.price" class="text-sm text-red-500">
                            {{ errors.price[0] }}
                        </p>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showModal = false">Cancel</Button>
                    <Button @click="submitForm">{{
                        editingService ? 'Update' : 'Create'
                    }}</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="showViewModal">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>View Medical Service</DialogTitle>
                    <DialogDescription>
                        Details of the medical service.
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4" v-if="viewingService">
                    <div>
                        <Label>Name</Label>
                        <p class="text-sm">{{ viewingService.name }}</p>
                    </div>
                    <div>
                        <Label>Description</Label>
                        <p class="text-sm">
                            {{ viewingService.description || '-' }}
                        </p>
                    </div>
                    <div>
                        <Label>Type</Label>
                        <Badge :class="getTypeColor(viewingService.type)">
                            {{ viewingService.type }}
                        </Badge>
                    </div>
                    <div>
                        <Label>Price</Label>
                        <p class="text-sm">
                            {{ formatPrice(viewingService.price) }}
                        </p>
                    </div>
                    <div>
                        <Label>Created At</Label>
                        <p class="text-sm">
                            {{
                                new Date(
                                    viewingService.created_at,
                                ).toLocaleString()
                            }}
                        </p>
                    </div>
                    <div>
                        <Label>Updated At</Label>
                        <p class="text-sm">
                            {{
                                new Date(
                                    viewingService.updated_at,
                                ).toLocaleString()
                            }}
                        </p>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showViewModal = false">Close</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
