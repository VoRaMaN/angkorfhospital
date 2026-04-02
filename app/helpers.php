<?php

if (! function_exists('formatDate')) {
    /**
     * Format date to DD/MM/YY format
     */
    function formatDate($date): string
    {
        if (! $date) {
            return '';
        }

        return \Carbon\Carbon::parse($date)->format('d/m/y');
    }
}

if (! function_exists('formatDateTime')) {
    /**
     * Format datetime to DD/MM/YY HH:mm format
     */
    function formatDateTime($date): string
    {
        if (! $date) {
            return '';
        }

        return \Carbon\Carbon::parse($date)->format('d/m/y H:i');
    }
}
