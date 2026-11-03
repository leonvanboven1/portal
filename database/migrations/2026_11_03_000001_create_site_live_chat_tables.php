<?php

use App\Support\Permissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_chat_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('visitor_name', 80);
            $table->foreignId('visitor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 16)->default('open')->index();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('site_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('conversation_id')->constrained('site_chat_conversations')->cascadeOnDelete();
            $table->string('sender_type', 16);
            $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_name', 80);
            $table->text('body');
            $table->timestamps();

            $table->index(['conversation_id', 'id']);
        });

        Schema::create('site_chat_staff_presence', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_seen_at')->index();
            $table->timestamps();
        });

        Permissions::sync();
    }

    public function down(): void
    {
        Schema::dropIfExists('site_chat_staff_presence');
        Schema::dropIfExists('site_chat_messages');
        Schema::dropIfExists('site_chat_conversations');

        $permissionId = DB::table('permissions')->where('slug', 'manage-live-chat')->value('id');
        if ($permissionId) {
            DB::table('permission_role')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }
    }
};
