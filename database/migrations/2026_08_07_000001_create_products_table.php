<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('workspace_id')->constrained('demo_workspaces')->cascadeOnDelete();
            $table->string('sku');
            $table->string('name');
            $table->string('category')->index();
            $table->string('status')->default('draft')->index();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->text('description')->nullable();
            $table->string('image_tone')->default('amber');
            $table->timestamps();

            $table->unique(['workspace_id', 'sku']);
            $table->index(['workspace_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
