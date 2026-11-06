<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $t) {
            $t->id();
            $t->uuid('slug')->unique();
            $t->string('recipient');
            $t->string('unit')->nullable();
            $t->string('courier');
            $t->string('tracking')->unique();
            $t->string('photo_path');
            $t->string('pickup_hash')->nullable()->unique();
            $t->string('status')->default('received');
            $t->foreignId('created_by')->constrained('users');
            $t->string('collected_by')->nullable();
            $t->timestamp('collected_at')->nullable();
            $t->timestamps();
        });
        Schema::create('package_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained();
            $t->foreignId('actor_id')->constrained('users');
            $t->string('action');
            $t->string('description');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_events');
        Schema::dropIfExists('packages');
    }
};
