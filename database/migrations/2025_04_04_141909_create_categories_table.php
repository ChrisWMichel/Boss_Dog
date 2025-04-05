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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('active')->default(true);
            $table->foreignId('parent_id')->nullable()->references('id')->on('categories')->cascadeOnDelete();
            $table->timestamp('deleted_at')->nullable();
            $table->foreignId('deleted_by')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->nullable()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'updated_by')->nullable()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
