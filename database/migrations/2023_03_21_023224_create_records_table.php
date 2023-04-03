<?php

use App\Models\Taxonomy;
use App\Models\Template;
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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('main_image')->nullable();
            $table->json('seo')->nullable();
            $table->json('data')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreignIdFor(Taxonomy::class)->nullable();
            $table->foreignIdFor(Template::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};