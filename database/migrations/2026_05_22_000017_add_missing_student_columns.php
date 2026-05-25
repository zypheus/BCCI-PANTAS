<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('students', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('students', 'student_id')) {
                $table->string('student_id')->nullable()->unique()->after('role_id');
            }
            if (! Schema::hasColumn('students', 'id_number')) {
                $table->string('id_number')->nullable()->unique()->after('student_id');
            }
            if (! Schema::hasColumn('students', 'middle_initial')) {
                $table->string('middle_initial')->nullable()->after('firstname');
            }
            if (! Schema::hasColumn('students', 'birth_date')) {
                $table->date('birth_date')->nullable();
            }
            if (! Schema::hasColumn('students', 'blood_type')) {
                $table->string('blood_type', 10)->nullable();
            }
            if (! Schema::hasColumn('students', 'mobile_number')) {
                $table->string('mobile_number')->nullable();
            }
            if (! Schema::hasColumn('students', 'emergency_person')) {
                $table->string('emergency_person')->nullable();
            }
            if (! Schema::hasColumn('students', 'emergency_relationship')) {
                $table->string('emergency_relationship')->nullable();
            }
            if (! Schema::hasColumn('students', 'emergency_number')) {
                $table->string('emergency_number')->nullable();
            }
            if (! Schema::hasColumn('students', 'emergency_address')) {
                $table->text('emergency_address')->nullable();
            }
            if (! Schema::hasColumn('students', 'student_signature')) {
                $table->string('student_signature')->nullable();
            }
            if (! Schema::hasColumn('students', 'address')) {
                $table->text('address')->nullable();
            }
        });

        Schema::table('pending_students', function (Blueprint $table) {
            if (! Schema::hasColumn('pending_students', 'middle_initial')) {
                $table->string('middle_initial')->nullable()->after('lastname');
            }
            if (! Schema::hasColumn('pending_students', 'address')) {
                $table->text('address')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = [
                'address', 'student_signature', 'emergency_address', 'emergency_number',
                'emergency_relationship', 'emergency_person', 'mobile_number', 'blood_type',
                'birth_date', 'middle_initial', 'id_number', 'student_id',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('pending_students', function (Blueprint $table) {
            foreach (['address', 'middle_initial'] as $col) {
                if (Schema::hasColumn('pending_students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
