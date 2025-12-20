<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sub_categories')) {
            Schema::create('sub_categories', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->string('description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }


        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->foreignId('sub_category_id')->after('category_id')->nullable()->constrained()->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropForeign(['sub_category_id']);
                $table->dropColumn('sub_category_id');
            });
        }

        Schema::dropIfExists('sub_categories');

        Schema::enableForeignKeyConstraints();
    }
};
