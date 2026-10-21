<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('contact_person')->nullable();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->text('address')->nullable();
            $t->string('payment_terms')->nullable();
            $t->decimal('opening_balance', 12, 2)->default(0);
            $t->boolean('is_active')->default(true);
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $t->string('po_number');
            $t->date('order_date');
            $t->string('status')->default('ordered'); // ordered, received, cancelled
            $t->decimal('total_amount', 12, 2)->default(0);
            $t->decimal('paid_amount', 12, 2)->default(0);
            $t->timestamp('received_at')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->unique(['restaurant_id', 'po_number']);
        });

        Schema::create('purchase_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $t->string('description');
            $t->decimal('quantity', 12, 3);
            $t->decimal('unit_cost', 12, 2);
            $t->decimal('line_total', 12, 2);
            $t->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->timestamps();
            $t->unique(['restaurant_id', 'name']);
        });

        Schema::create('expenses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $t->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $t->date('expense_date');
            $t->decimal('amount', 12, 2);
            $t->string('reference')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->index(['restaurant_id', 'expense_date']);
        });

        Schema::create('tax_rates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->decimal('rate', 6, 3);
            $t->string('type')->default('exclusive'); // inclusive, exclusive
            $t->boolean('is_active')->default(true);
            $t->boolean('is_default')->default(false);
            $t->timestamps();
        });

        Schema::create('invoices', function (Blueprint $t) {
            $t->id();
            $t->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('sequence');
            $t->string('invoice_number');
            $t->date('invoice_date');
            $t->decimal('subtotal', 12, 2);
            $t->decimal('tax_rate', 6, 3)->default(0);
            $t->string('tax_name')->nullable();
            $t->string('tax_type')->nullable();
            $t->decimal('tax_amount', 12, 2)->default(0);
            $t->decimal('total', 12, 2);
            $t->string('customer_name')->nullable();
            $t->timestamps();
            $t->unique(['restaurant_id', 'order_id']);
            $t->unique(['restaurant_id', 'sequence']);
            $t->index(['restaurant_id', 'invoice_date']);
        });

        Schema::create('accounts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $t->string('code');
            $t->string('name');
            $t->string('type'); // income, expense, asset, liability
            $t->timestamps();
            $t->unique(['restaurant_id', 'code']);
        });

        Schema::create('journal_entries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $t->date('entry_date');
            $t->string('memo');
            $t->timestamps();
            $t->index(['restaurant_id', 'entry_date']);
        });

        Schema::create('journal_lines', function (Blueprint $t) {
            $t->id();
            $t->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $t->foreignId('account_id')->constrained()->cascadeOnDelete();
            $t->decimal('debit', 12, 2)->default(0);
            $t->decimal('credit', 12, 2)->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['journal_lines', 'journal_entries', 'accounts', 'invoices', 'tax_rates', 'expenses', 'expense_categories', 'purchase_order_items', 'purchase_orders', 'suppliers'] as $tbl) {
            Schema::dropIfExists($tbl);
        }
    }
};
