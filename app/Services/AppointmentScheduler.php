<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentScheduler
{
    /**
     * Check if a time slot is available for a staff member
     */
    public function isTimeSlotAvailable(Staff $staff, Carbon $startTime, int $durationMinutes, ?Appointment $excludeAppointment = null): bool
    {
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        $conflictingAppointments = Appointment::where('staff_id', $staff->id)
            ->where('appointment_date_time', '<', $endTime)
            ->whereRaw('DATE_ADD(appointment_date_time, INTERVAL duration_minutes MINUTE) > ?', [$startTime])
            ->when($excludeAppointment, fn ($q) => $q->where('id', '!=', $excludeAppointment->id))
            ->whereIn('status', ['scheduled', 'confirmed', 'arrived', 'in_progress'])
            ->exists();

        return ! $conflictingAppointments;
    }

    /**
     * Find available time slots for a staff member on a given date
     */
    public function findAvailableSlots(Staff $staff, Carbon $date, int $durationMinutes = 30, array $workingHours = ['09:00', '17:00']): Collection
    {
        $availableSlots = collect();
        $startOfDay = $date->copy()->setTimeFromTimeString($workingHours[0]);
        $endOfDay = $date->copy()->setTimeFromTimeString($workingHours[1]);

        // Get existing appointments for the day
        $existingAppointments = Appointment::where('staff_id', $staff->id)
            ->whereDate('appointment_date_time', $date)
            ->whereIn('status', ['scheduled', 'confirmed', 'arrived', 'in_progress'])
            ->orderBy('appointment_date_time')
            ->get();

        $currentTime = $startOfDay->copy();

        foreach ($existingAppointments as $appointment) {
            // Check slots before this appointment
            while ($currentTime->copy()->addMinutes($durationMinutes) <= $appointment->appointment_date_time) {
                if ($currentTime->copy()->addMinutes($durationMinutes) <= $endOfDay) {
                    $availableSlots->push($currentTime->copy());
                }
                $currentTime->addMinutes($durationMinutes);
            }

            // Move current time to end of this appointment
            $appointmentEnd = $appointment->appointment_date_time->copy()->addMinutes($appointment->duration_minutes ?? 30);
            $currentTime = max($currentTime, $appointmentEnd);
        }

        // Check remaining slots after last appointment
        while ($currentTime->copy()->addMinutes($durationMinutes) <= $endOfDay) {
            $availableSlots->push($currentTime->copy());
            $currentTime->addMinutes($durationMinutes);
        }

        return $availableSlots;
    }

    /**
     * Suggest alternative appointment times when a conflict occurs
     */
    public function suggestAlternativeTimes(Staff $staff, Carbon $preferredTime, int $durationMinutes = 30, int $suggestionsCount = 3): Collection
    {
        $suggestions = collect();
        $date = $preferredTime->copy()->startOfDay();

        // Try same day, different times
        $sameDaySlots = $this->findAvailableSlots($staff, $date, $durationMinutes);
        $suggestions = $suggestions->merge(
            $sameDaySlots->filter(function ($slot) use ($preferredTime) {
                return abs($slot->diffInMinutes($preferredTime)) > 60; // At least 1 hour difference
            })->take($suggestionsCount)
        );

        // If not enough suggestions, try next few days
        $daysToCheck = 7;
        for ($i = 1; $i <= $daysToCheck && $suggestions->count() < $suggestionsCount; $i++) {
            $checkDate = $date->copy()->addDays($i);
            $daySlots = $this->findAvailableSlots($staff, $checkDate, $durationMinutes);
            $suggestions = $suggestions->merge($daySlots->take($suggestionsCount - $suggestions->count()));
        }

        return $suggestions->take($suggestionsCount);
    }

    /**
     * Get staff availability for a specific date range
     */
    public function getStaffAvailability(Collection $staff, Carbon $startDate, Carbon $endDate, int $durationMinutes = 30): Collection
    {
        $availability = collect();

        $staff->each(function ($staffMember) use ($startDate, $endDate, $durationMinutes, &$availability) {
            $staffAvailability = collect();

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $slots = $this->findAvailableSlots($staffMember, $date, $durationMinutes);
                $staffAvailability->put($date->toDateString(), $slots);
            }

            $availability->put($staffMember->id, [
                'staff' => $staffMember,
                'availability' => $staffAvailability,
            ]);
        });

        return $availability;
    }

    /**
     * Validate appointment data and check for conflicts
     */
    public function validateAppointment(array $data): array
    {
        $errors = [];

        $startTime = Carbon::parse($data['appointment_date_time']);
        $duration = $data['duration_minutes'] ?? 30;
        $staffId = $data['staff_id'];

        $staff = Staff::find($staffId);
        if (! $staff) {
            $errors[] = 'Selected staff member not found.';

            return $errors;
        }

        if (! $this->isTimeSlotAvailable($staff, $startTime, $duration)) {
            $errors[] = 'The selected time slot conflicts with an existing appointment.';

            // Suggest alternatives
            $alternatives = $this->suggestAlternativeTimes($staff, $startTime, $duration);
            if ($alternatives->isNotEmpty()) {
                $errors[] = 'Suggested alternative times: '.$alternatives->map(fn ($time) => $time->format('M j, Y g:i A'))->join(', ');
            }
        }

        return $errors;
    }
}
