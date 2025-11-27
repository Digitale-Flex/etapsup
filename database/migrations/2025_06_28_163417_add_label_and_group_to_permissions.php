<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->foreignId('permission_group_id')
                ->after('id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('label')->after('name')->nullable();
        });

         if (!Schema::hasColumn('roles', 'label')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('display_name')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['label']);
            $table->dropForeign(['permission_group_id']);
            $table->dropColumn('permission_group_id');
        });

        /*  Schema::table('roles', function (Blueprint $table) {
              $table->dropColumn('label');
          }); */
    }
};
