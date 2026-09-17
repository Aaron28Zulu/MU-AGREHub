<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
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
        Schema::create('supervisor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->constrained('projects')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('rubric_score', 5, 2); // Supports scores from 0.00 to 100.00
            $table->text('feedback')->nullable();
            $table->string('document_markup_path')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'supervisor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisor_evaluations');
    }
};
