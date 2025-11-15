<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { useAuth } from '@/composables/useAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, index, show } from '@/routes/appointments';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    eachDayOfInterval,
    endOfMonth,
    format,
    isSameDay,
    isToday,
    startOfMonth,
} from 'date-fns';
import {
    CalendarIcon,
    ChevronLeft,
    ChevronRight,
    List,
    Plus,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    appointments: Array<{
        id: number;
        patient: { user: { name: string } };
        staff: { user: { name: string } };
        appointment_date_time: string;
        duration_minutes: number;
        appointment_type: string;
        status: string;
        reason_for_visit?: string;
    }>;
    currentDate?: string;
}

const props = defineProps<Props>();

const { hasPermission } = useAuth();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appointment Calendar',
        href: '#',
    },
];

const selectedDate = ref(new Date(props.currentDate || new Date()));
const calendarOpen = ref(false);
const selectedAppointment = ref<any>(null);
const appointmentDialogOpen = ref(false);

// Group appointments by date
const appointmentsByDate = computed(() => {
    const grouped: Record<string, typeof props.appointments> = {};

    props.appointments.forEach((appointment) => {
        const date = new Date(appointment.appointment_date_time).toDateString();
        if (!grouped[date]) {
            grouped[date] = [];
        }
        grouped[date].push(appointment);
    });

    return grouped;
});

// Get appointments for selected date
const selectedDateAppointments = computed(() => {
    const dateKey = selectedDate.value.toDateString();
    return appointmentsByDate.value[dateKey] || [];
});

// Get status color
const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        scheduled:
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        confirmed:
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        arrived:
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        in_progress:
            'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        completed:
            'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        no_show:
            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        rescheduled:
            'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

// Get type color
const getTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        consultation: 'bg-blue-50 border-blue-200',
        emergency: 'bg-red-50 border-red-200',
        follow_up: 'bg-green-50 border-green-200',
        procedure: 'bg-purple-50 border-purple-200',
        checkup: 'bg-yellow-50 border-yellow-200',
        telemedicine: 'bg-indigo-50 border-indigo-200',
        screening: 'bg-pink-50 border-pink-200',
        therapy: 'bg-teal-50 border-teal-200',
    };
    return colors[type] || 'bg-gray-50 border-gray-200';
};

const navigateMonth = (direction: 'prev' | 'next') => {
    const newDate = new Date(selectedDate.value);
    if (direction === 'prev') {
        newDate.setMonth(newDate.getMonth() - 1);
    } else {
        newDate.setMonth(newDate.getMonth() + 1);
    }
    selectedDate.value = newDate;
};

const selectDate = (date: Date | undefined) => {
    if (date) {
        selectedDate.value = date;
        calendarOpen.value = false;
    }
};

const viewAppointment = (appointment: any) => {
    selectedAppointment.value = appointment;
    appointmentDialogOpen.value = true;
};

const closeAppointmentDialog = () => {
    appointmentDialogOpen.value = false;
    selectedAppointment.value = null;
};
</script>

<template>
    <Head title="Appointment Calendar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Appointment Calendar</h1>
                    <p class="text-muted-foreground">
                        View and manage appointments in calendar format
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button as-child variant="outline">
                        <Link :href="index().url">
                            <List class="mr-2 size-4" />
                            List View
                        </Link>
                    </Button>
                    <Button
                        as-child
                        v-if="hasPermission('create_appointments')"
                    >
                        <Link :href="create().url">
                            <Plus class="size-4" />
                            Create Appointment
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Calendar Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button
                        variant="outline"
                        size="sm"
                        @click="navigateMonth('prev')"
                    >
                        <ChevronLeft class="size-4" />
                    </Button>
                    <Popover v-model:open="calendarOpen">
                        <PopoverTrigger as-child>
                            <Button
                                variant="outline"
                                class="w-48 justify-start"
                            >
                                <CalendarIcon class="mr-2 size-4" />
                                {{ format(selectedDate, 'MMMM yyyy') }}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-auto p-0">
                            <Calendar
                                v-model="selectedDate"
                                :month="selectedDate"
                                @update:model-value="selectDate"
                                initial-focus
                            />
                        </PopoverContent>
                    </Popover>
                    <Button
                        variant="outline"
                        size="sm"
                        @click="navigateMonth('next')"
                    >
                        <ChevronRight class="size-4" />
                    </Button>
                </div>
                <div class="text-sm text-muted-foreground">
                    {{ selectedDateAppointments.length }} appointment{{
                        selectedDateAppointments.length !== 1 ? 's' : ''
                    }}
                    on {{ format(selectedDate, 'MMM d, yyyy') }}
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Calendar Grid -->
                <div class="lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Calendar</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-7 gap-1">
                                <!-- Day headers -->
                                <div
                                    v-for="day in [
                                        'Sun',
                                        'Mon',
                                        'Tue',
                                        'Wed',
                                        'Thu',
                                        'Fri',
                                        'Sat',
                                    ]"
                                    :key="day"
                                    class="p-2 text-center text-sm font-medium text-muted-foreground"
                                >
                                    {{ day }}
                                </div>

                                <!-- Calendar days -->
                                <div
                                    v-for="day in eachDayOfInterval({
                                        start: startOfMonth(selectedDate),
                                        end: endOfMonth(selectedDate),
                                    })"
                                    :key="day.toISOString()"
                                    class="min-h-24 border border-border p-1"
                                    :class="{
                                        'bg-muted': isToday(day),
                                        'bg-primary/5': isSameDay(
                                            day,
                                            selectedDate,
                                        ),
                                    }"
                                >
                                    <div class="mb-1 text-sm font-medium">
                                        {{ format(day, 'd') }}
                                    </div>
                                    <div class="space-y-1">
                                        <div
                                            v-for="appointment in appointmentsByDate[
                                                day.toDateString()
                                            ] || []"
                                            :key="appointment.id"
                                            class="cursor-pointer rounded border p-1 text-xs hover:opacity-80"
                                            :class="
                                                getTypeColor(
                                                    appointment.appointment_type,
                                                )
                                            "
                                            @click="
                                                viewAppointment(appointment)
                                            "
                                        >
                                            <div class="truncate font-medium">
                                                {{
                                                    appointment.patient.user
                                                        .name
                                                }}
                                            </div>
                                            <div class="text-muted-foreground">
                                                {{
                                                    format(
                                                        new Date(
                                                            appointment.appointment_date_time,
                                                        ),
                                                        'HH:mm',
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Selected Date Appointments -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle>{{
                                format(selectedDate, 'MMM d, yyyy')
                            }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="selectedDateAppointments.length === 0"
                                class="py-8 text-center text-muted-foreground"
                            >
                                No appointments scheduled
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="appointment in selectedDateAppointments"
                                    :key="appointment.id"
                                    class="cursor-pointer rounded-lg border p-3 transition-shadow hover:shadow-md"
                                    :class="
                                        getTypeColor(
                                            appointment.appointment_type,
                                        )
                                    "
                                    @click="viewAppointment(appointment)"
                                >
                                    <div
                                        class="mb-2 flex items-start justify-between"
                                    >
                                        <div class="font-medium">
                                            {{ appointment.patient.user.name }}
                                        </div>
                                        <Badge
                                            :class="
                                                getStatusColor(
                                                    appointment.status,
                                                )
                                            "
                                            class="text-xs"
                                        >
                                            {{
                                                appointment.status.replace(
                                                    '_',
                                                    ' ',
                                                )
                                            }}
                                        </Badge>
                                    </div>
                                    <div
                                        class="space-y-1 text-sm text-muted-foreground"
                                    >
                                        <div>
                                            Time:
                                            {{
                                                format(
                                                    new Date(
                                                        appointment.appointment_date_time,
                                                    ),
                                                    'HH:mm',
                                                )
                                            }}
                                            ({{
                                                appointment.duration_minutes
                                            }}min)
                                        </div>
                                        <div>
                                            Staff:
                                            {{ appointment.staff.user.name }}
                                        </div>
                                        <div>
                                            Type:
                                            {{
                                                appointment.appointment_type.replace(
                                                    '_',
                                                    ' ',
                                                )
                                            }}
                                        </div>
                                        <div
                                            v-if="appointment.reason_for_visit"
                                            class="truncate"
                                        >
                                            Reason:
                                            {{ appointment.reason_for_visit }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Appointment Details Dialog -->
        <Dialog
            v-model:open="appointmentDialogOpen"
            @update:open="closeAppointmentDialog"
        >
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Appointment Details</DialogTitle>
                </DialogHeader>
                <div v-if="selectedAppointment" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">Patient</label>
                            <p class="text-sm text-muted-foreground">
                                {{ selectedAppointment.patient.user.name }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Staff</label>
                            <p class="text-sm text-muted-foreground">
                                {{ selectedAppointment.staff.user.name }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium"
                                >Date & Time</label
                            >
                            <p class="text-sm text-muted-foreground">
                                {{
                                    format(
                                        new Date(
                                            selectedAppointment.appointment_date_time,
                                        ),
                                        'PPP p',
                                    )
                                }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Duration</label>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    selectedAppointment.duration_minutes
                                }}
                                minutes
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Type</label>
                            <Badge
                                :class="
                                    getTypeColor(
                                        selectedAppointment.appointment_type,
                                    )
                                "
                            >
                                {{
                                    selectedAppointment.appointment_type.replace(
                                        '_',
                                        ' ',
                                    )
                                }}
                            </Badge>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Status</label>
                            <Badge
                                :class="
                                    getStatusColor(selectedAppointment.status)
                                "
                            >
                                {{
                                    selectedAppointment.status.replace('_', ' ')
                                }}
                            </Badge>
                        </div>
                    </div>
                    <div v-if="selectedAppointment.reason_for_visit">
                        <label class="text-sm font-medium"
                            >Reason for Visit</label
                        >
                        <p class="text-sm text-muted-foreground">
                            {{ selectedAppointment.reason_for_visit }}
                        </p>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="closeAppointmentDialog"
                        >Close</Button
                    >
                    <Button as-child>
                        <Link :href="show(selectedAppointment?.id).url"
                            >View Full Details</Link
                        >
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
