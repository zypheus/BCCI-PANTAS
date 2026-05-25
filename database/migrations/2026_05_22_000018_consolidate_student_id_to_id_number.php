<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['students', 'pending_students'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (Schema::hasColumn($table, 'student_id')) {
                DB::table($table)
                    ->whereNotNull('student_id')
                    ->where(function ($q) {
                        $q->whereNull('id_number')->orWhere('id_number', '');
                    })
                    ->orderBy('id')
                    ->chunkById(100, function ($rows) use ($table) {
                        foreach ($rows as $row) {
                            DB::table($table)->where('id', $row->id)->update([
                                'id_number' => $row->student_id,
                            ]);
                        }
                    });

                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $blueprint->dropColumn('student_id');
                });
            }

            if ($table === 'pending_students' && Schema::hasColumn($table, 'qrcode')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('qrcode');
                });
            }
        }

        $this->alignPendingStudentsColumns();
    }

    public function down(): void
    {
        if (Schema::hasTable('students') && ! Schema::hasColumn('students', 'student_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('student_id')->nullable()->after('role_id');
            });
            DB::table('students')->update(['student_id' => DB::raw('id_number')]);
        }

        if (Schema::hasTable('pending_students') && ! Schema::hasColumn('pending_students', 'student_id')) {
            Schema::table('pending_students', function (Blueprint $table) {
                $table->string('student_id')->nullable()->after('id');
                $table->string('qrcode')->nullable();
            });
            DB::table('pending_students')->update(['student_id' => DB::raw('id_number')]);
        }
    }

    private function alignPendingStudentsColumns(): void
    {
        if (! Schema::hasTable('pending_students')) {
            return;
        }

        Schema::table('pending_students', function (Blueprint $table) {
            $columns = [
                'id_number' => fn () => $table->string('id_number')->nullable()->after('id'),
                'middle_initial' => fn () => $table->string('middle_initial')->nullable()->after('lastname'),
                'birth_date' => fn () => $table->date('birth_date')->nullable(),
                'blood_type' => fn () => $table->string('blood_type', 10)->nullable(),
                'emergency_person' => fn () => $table->string('emergency_person')->nullable(),
                'emergency_relationship' => fn () => $table->string('emergency_relationship')->nullable(),
                'emergency_number' => fn () => $table->string('emergency_number')->nullable(),
                'emergency_address' => fn () => $table->text('emergency_address')->nullable(),
                'student_signature' => fn () => $table->string('student_signature')->nullable(),
                'address' => fn () => $table->text('address')->nullable(),
            ];

            foreach ($columns as $name => $add) {
                if (! Schema::hasColumn('pending_students', $name)) {
                    $add();
                }
            }
        });
    }
};
