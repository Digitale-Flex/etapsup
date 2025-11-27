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
        if (! Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_type_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('category_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('city_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->string('title');
                $table->string('slug')->index();
                $table->text('description');
                $table->decimal('price');
                $table->unsignedTinyInteger('room')->nullable();
                $table->unsignedTinyInteger('bathroom')->nullable();
                $table->unsignedTinyInteger('dining_room')->nullable();
                $table->unsignedTinyInteger('kitchen')->nullable();
                $table->unsignedTinyInteger('living_room')->nullable();
                $table->boolean('balcony')->default(false);
                $table->boolean('outdoor_space')->default(false);
                $table->text('address')->nullable();
                $table->text('regulation')->nullable();
                $table->string('airbnb')->nullable();
                $table->boolean('is_published')->default(false);
                $table->boolean('discount')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['city_id', 'price']);
                $table->index(['property_type_id', 'price']);
                $table->index(['category_id', 'price']);
                $table->index(['room', 'bathroom', 'price']);
                $table->index(['balcony', 'outdoor_space', 'price']);
            });
        }

        if (! Schema::hasTable('equipment_property')) {
            Schema::create('equipment_property', function (Blueprint $table) {
                $table->foreignId('property_id')->constrained()->onDelete('cascade');
                $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('layout_property')) {
            Schema::create('layout_property', function (Blueprint $table) {
                $table->foreignId('property_id')->constrained()->onDelete('cascade');
                $table->foreignId('layout_id')->constrained('layouts')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('property_regulation')) {
            Schema::create('property_regulation', function (Blueprint $table) {
                $table->foreignId('property_id')->constrained()->onDelete('cascade');
                $table->foreignId('regulation_id')->constrained('regulations')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()
                    ->constrained('comments')
                    ->onDelete('cascade');
                $table->text('content');
                $table->unsignedTinyInteger('score')->nullable();
                $table->morphs('commentable');
                $table->string('status')->default('approved');
                $table->timestamps();

                $table->index(['commentable_type', 'commentable_id', 'parent_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('properties');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('equipment_property');
        Schema::dropIfExists('layout_property');
        Schema::dropIfExists('property_regulation');
        Schema::dropIfExists('comments');
        Schema::disableForeignKeyConstraints();
    }
};
