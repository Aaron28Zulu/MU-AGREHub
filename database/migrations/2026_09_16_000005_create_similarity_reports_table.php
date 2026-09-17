<?php

declare(strict_types=1);

use App\Models\Project;
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
        Schema::create('similarity_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->constrained('projects')->cascadeOnDelete();
            $table->decimal('similarity_score', 5, 2); // Supports 0.00 to 100.00%
            $table->json('matched_sources_json');
            $table->timestamps();

            $table->index(['project_id', 'similarity_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('similarity_reports');
    }
};
