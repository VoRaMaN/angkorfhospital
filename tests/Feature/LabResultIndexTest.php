<?php

use App\Models\CbcReport;
use App\Models\FetReport;
use App\Models\HormoneReport;
use App\Models\MedicalOrder;
use App\Models\OpuReport;
use App\Models\Patient;
use App\Models\SaReport;
use App\Models\Staff;
use App\Models\User;
use App\Models\Visit;

function createLabResultMedicalOrder(): MedicalOrder
{
    $patient = Patient::factory()->create();
    $staff = Staff::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
    ]);

    return MedicalOrder::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'order_details' => 'Lab result test order',
        'status' => 'completed',
        'priority' => 'routine',
        'ordered_at' => now(),
    ]);
}

test('lab results index lists completed reports across types', function () {
    $user = User::factory()->create();

    $hormoneOrder = createLabResultMedicalOrder();
    HormoneReport::create([
        'medical_order_id' => $hormoneOrder->id,
        'patient_id' => $hormoneOrder->patient_id,
        'reported_date' => '2026-01-10',
    ]);

    $saOrder = createLabResultMedicalOrder();
    SaReport::create([
        'medical_order_id' => $saOrder->id,
        'patient_id' => $saOrder->patient_id,
        'reported_date' => '2026-01-11',
    ]);

    $this->actingAs($user)
        ->get('/lab-results')
        ->assertInertia(fn ($page) => $page
            ->component('LabResults/Index')
            ->where('results.total', 2)
        );
});

test('lab results index filters by report type', function () {
    $user = User::factory()->create();

    $hormoneOrder = createLabResultMedicalOrder();
    HormoneReport::create([
        'medical_order_id' => $hormoneOrder->id,
        'patient_id' => $hormoneOrder->patient_id,
    ]);

    $cbcOrder = createLabResultMedicalOrder();
    CbcReport::create([
        'medical_order_id' => $cbcOrder->id,
        'patient_id' => $cbcOrder->patient_id,
    ]);

    $this->actingAs($user)
        ->get('/lab-results?type=cbc')
        ->assertInertia(fn ($page) => $page
            ->where('results.total', 1)
            ->where('results.data.0.type', 'cbc')
        );
});

test('lab results index searches by patient name', function () {
    $user = User::factory()->create();

    $order = createLabResultMedicalOrder();
    $patient = Patient::find($order->patient_id);
    $patient->update(['name' => 'Distinctivename']);

    SaReport::create([
        'medical_order_id' => $order->id,
        'patient_id' => $order->patient_id,
    ]);

    $otherOrder = createLabResultMedicalOrder();
    SaReport::create([
        'medical_order_id' => $otherOrder->id,
        'patient_id' => $otherOrder->patient_id,
    ]);

    $this->actingAs($user)
        ->get('/lab-results?search=Distinctivename')
        ->assertInertia(fn ($page) => $page->where('results.total', 1));
});

test('lab results index handles FET (no patient relation) and OPU (bi-patient) correctly', function () {
    $user = User::factory()->create();

    $fetOrder = createLabResultMedicalOrder();
    FetReport::create([
        'medical_order_id' => $fetOrder->id,
        'female_patient_name' => 'Fet Patient',
        'female_hn' => '26/000099',
    ]);

    $opuOrder = createLabResultMedicalOrder();
    $femalePatient = Patient::find($opuOrder->patient_id);
    OpuReport::create([
        'medical_order_id' => $opuOrder->id,
        'female_patient_id' => $femalePatient->id,
    ]);

    $response = $this->actingAs($user)->get('/lab-results');

    $response->assertInertia(fn ($page) => $page->where('results.total', 2));

    $response->assertInertia(function ($page) use ($femalePatient) {
        $rows = collect($page->toArray()['props']['results']['data']);
        $fet = $rows->firstWhere('type', 'fet');
        $opu = $rows->firstWhere('type', 'opu');

        expect($fet['patient_name'])->toBe('Fet Patient');
        expect($fet['patient_hn'])->toBe('26/000099');
        expect($opu['patient_name'])->toBe($femalePatient->full_name);
    });
});

test('lab results index respects date range filter on created_at', function () {
    $user = User::factory()->create();

    $order = createLabResultMedicalOrder();
    $report = HormoneReport::create([
        'medical_order_id' => $order->id,
        'patient_id' => $order->patient_id,
    ]);
    $report->created_at = now()->subDays(10);
    $report->save();

    $this->actingAs($user)
        ->get('/lab-results?start_date='.now()->subDay()->format('Y-m-d'))
        ->assertInertia(fn ($page) => $page->where('results.total', 0));

    $this->actingAs($user)
        ->get('/lab-results?start_date='.now()->subDays(20)->format('Y-m-d'))
        ->assertInertia(fn ($page) => $page->where('results.total', 1));
});
