<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    edit as labPanelEdit,
    index as labPanelIndex,
} from '@/routes/lab-panels';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';

interface Props {
    labPanel: {
        id: number;
        name: string;
        description: string;
        price: number;
        is_active: boolean;
        created_at: string;
        updated_at: string;
        inventory_items: Array<{
            id: number;
            item_name: string;
            unit: string;
            quantity: number;
            price: number;
            pivot: {
                quantity_required: number;
                notes: string;
            };
        }>;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lab Panels',
        href: labPanelIndex().url,
    },
    {
        title: 'Details',
        href: '#',
    },
];
</script>

<template>
    <Head title="Lab Panel Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center gap-4">
                <Button variant="outline" as-child>
                    <a :href="labPanelIndex().url">
                        <ArrowLeft class="size-4" />
                        Back
                    </a>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Lab Panel Details</h1>
                    <p class="text-muted-foreground">
                        View panel information and required supplies
                    </p>
                </div>
                <div class="ml-auto">
                    <Button variant="outline" as-child>
                        <Link :href="labPanelEdit(props.labPanel.id).url">
                            <Edit class="size-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-6xl space-y-6">
                <!-- Package Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Package Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <dt
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Panel Name
                                </dt>
                                <dd class="text-sm">
                                    {{ props.labPanel.name }}
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Price
                                </dt>
                                <dd class="text-sm">
                                    ${{ props.labPanel.price }}
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Status
                                </dt>
                                <dd class="text-sm">
                                    <Badge
                                        :variant="
                                            props.labPanel.is_active
                                                ? 'default'
                                                : 'secondary'
                                        "
                                    >
                                        {{
                                            props.labPanel.is_active
                                                ? 'Active'
                                                : 'Inactive'
                                        }}
                                    </Badge>
                                </dd>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <dt
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Description
                                </dt>
                                <dd class="text-sm">
                                    {{
                                        props.labPanel.description ||
                                        'No description provided'
                                    }}
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Created
                                </dt>
                                <dd class="text-sm">
                                    {{
                                        new Date(
                                            props.labPanel.created_at,
                                        ).toLocaleString()
                                    }}
                                </dd>
                            </div>

                            <div class="space-y-2">
                                <dt
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Last Updated
                                </dt>
                                <dd class="text-sm">
                                    {{
                                        new Date(
                                            props.labPanel.updated_at,
                                        ).toLocaleString()
                                    }}
                                </dd>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Required Inventory Items -->
                <Card>
                    <CardHeader>
                        <CardTitle
                            >Required Inventory Items ({{
                                props.labPanel.inventory_items.length
                            }})</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="props.labPanel.inventory_items.length === 0"
                            class="py-8 text-center text-muted-foreground"
                        >
                            No inventory items assigned to this panel.
                        </div>

                        <div v-else>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Item Name</TableHead>
                                        <TableHead
                                            >Available Quantity</TableHead
                                        >
                                        <TableHead>Quantity Required</TableHead>
                                        <TableHead>Price</TableHead>
                                        <TableHead>Notes</TableHead>
                                        <TableHead>Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="item in props.labPanel
                                            .inventory_items"
                                        :key="item.id"
                                    >
                                        <TableCell>{{
                                            item.item_name
                                        }}</TableCell>
                                        <TableCell
                                            >{{ item.quantity }}
                                            {{ item.unit }}</TableCell
                                        >
                                        <TableCell>{{
                                            item.pivot.quantity_required
                                        }}</TableCell>
                                        <TableCell>${{ item.price }}</TableCell>
                                        <TableCell>{{
                                            item.pivot.notes || 'N/A'
                                        }}</TableCell>
                                        <TableCell>
                                            <Badge
                                                :variant="
                                                    item.quantity >=
                                                    item.pivot.quantity_required
                                                        ? 'default'
                                                        : 'destructive'
                                                "
                                            >
                                                {{
                                                    item.quantity >=
                                                    item.pivot.quantity_required
                                                        ? 'Available'
                                                        : 'Insufficient'
                                                }}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
