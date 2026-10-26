<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Programmes run by the MUSAGRICA Office of Research and Innovation:
 * the Vision 2030 Research and Innovation Challenge, office events such as the
 * Agricultural Debate, and the Continuous Crop Production Initiative.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('innovation_challenges', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('edition');
            $table->string('theme')->nullable();
            $table->text('description');
            $table->text('criteria')->nullable();
            $table->string('status')->default('draft')->index();
            $table->date('opens_on')->nullable();
            $table->date('closes_on')->nullable();
            $table->date('presentation_on')->nullable();
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('challenge_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('innovation_challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('entry_type');
            $table->string('category');
            $table->text('problem_statement');
            $table->text('proposed_solution');
            $table->text('expected_impact');
            $table->string('team_members')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('submitted')->index();
            $table->string('award')->nullable();
            $table->text('office_feedback')->nullable();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['innovation_challenge_id', 'student_id', 'title']);
        });

        Schema::create('entry_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('challenge_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('relevance_score');
            $table->unsignedTinyInteger('innovation_score');
            $table->unsignedTinyInteger('feasibility_score');
            $table->unsignedTinyInteger('impact_score');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->unique(['challenge_entry_id', 'reviewer_id']);
        });

        Schema::create('association_events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('event_type')->index();
            $table->string('motion')->nullable();
            $table->text('description');
            $table->string('venue')->nullable();
            $table->dateTime('starts_at')->index();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('speaker_slots')->nullable();
            $table->text('outcomes')->nullable();
            $table->boolean('is_published')->default(true);
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('association_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('wants_to_speak')->default(false);
            $table->string('position')->nullable();
            $table->timestamps();

            $table->unique(['association_event_id', 'user_id']);
        });

        Schema::create('crop_plots', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('crop');
            $table->string('variety')->nullable();
            $table->string('location')->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->string('season');
            $table->string('status')->default('preparing')->index();
            $table->date('planted_on')->nullable();
            $table->date('expected_harvest_on')->nullable();
            $table->text('objectives')->nullable();
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('plot_observations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('crop_plot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->date('observed_on')->index();
            $table->string('activity');
            $table->string('metric')->nullable();
            $table->decimal('value', 12, 2)->nullable();
            $table->string('unit', 32)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_observations');
        Schema::dropIfExists('crop_plots');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('association_events');
        Schema::dropIfExists('entry_reviews');
        Schema::dropIfExists('challenge_entries');
        Schema::dropIfExists('innovation_challenges');
    }
};
