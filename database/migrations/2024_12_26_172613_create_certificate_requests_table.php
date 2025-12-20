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
        if (! Schema::hasTable('genres')) {
            Schema::create('genres', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('icon')->nullable();
                $table->boolean('is_published')->default(false);
                $table->integer('order_column')->nullable();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('rental_deposits')) {
            Schema::create('rental_deposits', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('certificate_requests')) {
            Schema::create('certificate_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('city_id')
                    ->nullable()
                    ->index()
                    ->constrained()
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('genre_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('rental_deposit_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('country_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('location_id')
                    ->nullable()
                    ->index()
                    ->constrained()
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
                $table->string('nationality');
                $table->string('passport_number');
                $table->string('budget');
                $table->date('rental_start');
                $table->string('duration');
                $table->text('further_information')->nullable();
                $table->string('address')->nullable();
                $table->boolean('pay_later');
                $table->string('state')->default('payment_pending');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('charges')) {
            Schema::create('charges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('certificate_request_id')
                    ->index()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->string('stripe_id')->index();
                $table->integer('amount');
                $table->string('currency');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('payment_proofs')) {
            Schema::create('payment_proofs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('certificate_request_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->text('note')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'country_id')) {
                $table->foreignId('country_id')->after('partner_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
        Schema::dropIfExists('rental_deposits');
        Schema::dropIfExists('certificate_requests');
        Schema::dropIfExists('charges');
        Schema::dropIfExists('payment_proofs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country_id', 'partner_id']);
            $table->dropColumn('country_id', 'partner_id');
        });
    }
};
