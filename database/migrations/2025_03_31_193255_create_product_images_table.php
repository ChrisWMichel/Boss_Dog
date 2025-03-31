<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('path');
            $table->string('url');
            $table->string('mime');
            $table->string('size');
            $table->integer('position');
            $table->timestamps();
        });
        // TODO:Remove this code before production
        DB::table('products')->chunkById(100, function ($products) {
            foreach ($products as $product) {
                DB::table('product_images')->insert([
                    'product_id' => $product->id,
                    'path' => $product->image,
                    'url' => $product->image,
                    'mime' => 'image/jpeg',
                    'size' => $product->image_size,
                    'position' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('image');
                $table->dropColumn('image_size');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // TODO:Remove this code before production
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
            $table->integer('image_size')->nullable()->after('slug');
        });

        DB::table('products')->chunkById(100, function (Collection $products) {
            foreach($products as $product) {
                $image = DB::table('product_images')
                ->select(['product_id', 'path', 'size'])
                ->where('product_id', $product->id)
                ->first();

                if ($image) {
                    DB::table('products')->where('id', $product->id)->update([
                        'image' => $image->path,
                        'image_size' => $image->size,
                    ]);
                }
            }
        });

        Schema::dropIfExists('product_images');
    }
};
