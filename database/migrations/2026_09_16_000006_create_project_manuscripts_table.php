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
        Schema::create('project_manuscripts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->constrained('projects')->cascadeOnDelete();
            $table->string('version')->default('v1.0');
            $table->string('milestone_stage'); // e.g. Proposal, Progress Report, Final Draft
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable(); // Size in bytes
            $table->text('submission_notes')->nullable();
            $table->string('status')->default('submitted'); // submitted, under_review, revisions_requested, approved
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_manuscripts');
    }
};
