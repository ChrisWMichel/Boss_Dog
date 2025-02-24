<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->string('status');
            $table->string('type');
            $table->foreignIdFor(User::class, 'created_by')->nullable()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'updated_by')->nullable()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
