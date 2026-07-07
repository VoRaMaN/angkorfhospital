<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('opu_reports', function (Blueprint $table) {
            $table->json('embryo_developments')->nullable()->after('day5_embryos');
        });

        // Backfill from the legacy fixed day3_*/day5_* columns.
        DB::table('opu_reports')->orderBy('id')->each(function ($row) {
            $sections = [];

            foreach ([3 => 'day3', 5 => 'day5'] as $day => $prefix) {
                $embryos = json_decode($row->{$prefix.'_embryos'} ?? 'null', true);
                $hasEmbryos = is_array($embryos) && array_filter($embryos, fn ($v) => $v !== null && $v !== '');

                if ($row->{$prefix.'_datetime'} || $row->{$prefix.'_checked_by'} || $hasEmbryos) {
                    $sections[] = [
                        'day' => $day,
                        'datetime' => $row->{$prefix.'_datetime'},
                        'checked_by' => $row->{$prefix.'_checked_by'},
                        'embryos' => is_array($embryos) ? $embryos : array_fill(0, 20, null),
                    ];
                }
            }

            if ($sections) {
                DB::table('opu_reports')->where('id', $row->id)
                    ->update(['embryo_developments' => json_encode($sections)]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opu_reports', function (Blueprint $table) {
            $table->dropColumn('embryo_developments');
        });
    }
};
