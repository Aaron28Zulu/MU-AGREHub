<?php

declare(strict_types=1);

use App\Enums\ProjectStatus;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('abstract');
            $table->string('category')->index();
            $table->string('status')->default(ProjectStatus::DRAFT->value)->index();
            $table->unsignedSmallInteger('completion_year')->nullable()->index();
            $table->foreignIdFor(User::class, 'supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignIdFor(User::class, 'student_id')->constrained('users')->cascadeOnDelete();
            $table->string('full_document_path')->nullable();
            $table->string('raw_data_path')->nullable();
            $table->timestamps();

            // Composite indexes for fast catalog searches and filtering
            $table->index(['status', 'category']);
            $table->index(['status', 'completion_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
