<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-restaurant settings, one JSON document per section (sms, whatsapp, email, branding, printer, qr_logo).
        Schema::create('comms_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('section', 40);
            $table->json('data')->nullable();
            $table->timestamps();
            $table->unique(['restaurant_id', 'section']);
        });

        Schema::create('comms_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('channel', 20);          // sms | whatsapp | email | printer
            $table->string('event', 40);
            $table->string('driver', 20)->nullable();
            $table->string('recipient')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20);           // logged | sent | failed | skipped
            $table->text('response')->nullable();
            $table->string('reference')->nullable(); // e.g. order number
            $table->timestamps();
            $table->index(['restaurant_id', 'channel', 'created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            // customer_phone may already be added by another module's migration
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone', 40)->nullable();
            }
            if (!Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('dark_mode')->default(false);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('barcode', 64)->nullable();
            $table->index(['restaurant_id', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['restaurant_id', 'barcode']);
            $table->dropColumn('barcode');
        });
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('dark_mode'));
        Schema::table('reservations', fn (Blueprint $t) => $t->dropColumn('customer_email'));
        // orders.customer_phone / customer_email are shared with other modules and are left in place.
        Schema::dropIfExists('comms_notification_logs');
        Schema::dropIfExists('comms_settings');
    }
};
