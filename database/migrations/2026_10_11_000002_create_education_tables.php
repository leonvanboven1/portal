<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('education_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedSmallInteger('duration_hours')->default(24);
            $table->string('payout_bank_account_uuid')->nullable();
            $table->boolean('qualifies_for_company')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('education_course_prerequisites', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('education_courses')->cascadeOnDelete();
            $table->foreignId('prerequisite_id')->constrained('education_courses')->cascadeOnDelete();
            $table->primary(['course_id', 'prerequisite_id']);
        });

        Schema::create('education_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('education_course_id')->constrained('education_courses')->restrictOnDelete();
            $table->string('payer_bank_account_uuid')->nullable();
            $table->string('payout_bank_account_uuid')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->dateTime('started_at');
            $table->dateTime('completes_at');
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamps();
            $table->unique(['user_id', 'education_course_id']);
            $table->index(['user_id', 'status']);
            $table->index(['status', 'completes_at']);
        });

        \App\Support\Features::sync();
        \App\Support\Permissions::sync();

        $permissionId = DB::table('permissions')->where('slug', 'use-education')->value('id');
        $defaultRoleIds = DB::table('roles')->where('is_default', true)->pluck('id');
        foreach ($defaultRoleIds as $roleId) {
            DB::table('permission_role')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id' => $roleId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('education_enrollments');
        Schema::dropIfExists('education_course_prerequisites');
        Schema::dropIfExists('education_courses');
    }
};