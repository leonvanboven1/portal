<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_contents', function (Blueprint $table) {
            $table->string('hero_image_path')->nullable();
            $table->string('unique_image_path')->nullable();
            $table->string('steps_image_path')->nullable();
        });

        Schema::create('site_rule_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('site_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_rule_category_id')->constrained('site_rule_categories')->cascadeOnDelete();
            $table->string('title', 160);
            $table->text('body');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $existingRules = DB::table('site_contents')->where('id', 1)->value('rules_text');
        if (is_string($existingRules) && trim($existingRules) !== '') {
            $now = now();
            $categoryId = DB::table('site_rule_categories')->insertGetId([
                'name' => 'Algemene regels',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $rules = array_filter(array_map('trim', preg_split('/\R/', $existingRules)), fn (string $rule) => $rule !== '');

            foreach (array_values($rules) as $index => $rule) {
                DB::table('site_rules')->insert([
                    'site_rule_category_id' => $categoryId,
                    'title' => 'Regel '.($index + 1),
                    'body' => $rule,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_rules');
        Schema::dropIfExists('site_rule_categories');

        Schema::table('site_contents', function (Blueprint $table) {
            $table->dropColumn(['hero_image_path', 'unique_image_path', 'steps_image_path']);
        });
    }
};
