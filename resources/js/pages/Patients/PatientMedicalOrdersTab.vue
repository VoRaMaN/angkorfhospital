<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { show } from '@/routes/medical-orders';
import { Link } from '@inertiajs/vue3';
import { Eye } from 'lucide-vue-next';

interface Props {
    patient: {
        id: number;
        medical_orders?: Array<{
            id: number;
            type: string;
            status: string;
            priority: string;
            created_at: string;
        }>;
    };
}

const props = defineProps<Props>();
</script>

<template>
    <div class="space-y-6">
        <!-- Medical Orders List -->
        <div class="rounded-lg border bg-card p-6">
            <h3 class="mb-4 text-lg font-medium">Medical Orders</h3>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Priority</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="order in props.patient.medical_orders"
                            :key="order.id"
                        >
                            <TableCell>{{ order.id }}</TableCell>
                            <TableCell>{{ order.type }}</TableCell>
                            <TableCell>
                                <Badge variant="secondary">{{
                                    order.status
                                }}</Badge>
                            </TableCell>
                            <TableCell>
                                <Badge variant="outline">{{
                                    order.priority
                                }}</Badge>
                            </TableCell>
                            <TableCell>{{
                                new Date(order.created_at).toLocaleDateString()
                            }}</TableCell>
                            <TableCell>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="show(order.id).url">
                                        <Eye class="size-4" />
                                        View
                                    </Link>
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-if="
                                !props.patient.medical_orders ||
                                props.patient.medical_orders.length === 0
                            "
                        >
                            <TableCell
                                colspan="6"
                                class="text-center text-muted-foreground"
                            >
                                No medical orders found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </div>
</template>
