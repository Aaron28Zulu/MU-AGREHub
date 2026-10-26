<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait ManagesResearchFolder
{
    /**
     * Add or remove a catalogue record from the signed-in user's folder.
     */
    public function toggleSave(int $projectId): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        $project = Project::query()->publicCatalog()->findOrFail($projectId);

        $user->savedProjects()->toggle($project->id);
    }

    /**
     * IDs of the records the current user has saved.
     *
     * @return list<int>
     */
    protected function savedProjectIds(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        return $user->savedProjects()->pluck('projects.id')->map(fn ($id): int => (int) $id)->all();
    }
}
