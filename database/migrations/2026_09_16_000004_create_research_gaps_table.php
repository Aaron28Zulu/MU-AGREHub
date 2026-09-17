<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\ResearchDomain;
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
        Schema::create('research_gaps', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->constrained('projects')->cascadeOnDelete();
            $table->foreignIdFor(ResearchDomain::class, 'domain_id')->nullable()->constrained('research_domains')->nullOnDelete();
            $table->text('extracted_gap_text');
            $table->boolean('is_saturated')->default(false)->index();
            $table->boolean('is_emerging')->default(false)->index();
            $table->timestamps();

            $table->index(['domain_id', 'is_saturated']);
            $table->index(['domain_id', 'is_emerging']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_gaps');
    }
};
