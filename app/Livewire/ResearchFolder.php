<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\ManagesResearchFolder;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ResearchFolder extends Component
{
    use ManagesResearchFolder;

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $records = $user->savedProjects()
            ->publicCatalog()
            ->select(['projects.id', 'title', 'abstract', 'category', 'completion_year', 'student_id', 'supervisor_id'])
            ->with(['student:id,name', 'supervisor:id,name'])
            ->orderByPivot('created_at', 'desc')
            ->get();

        return view('livewire.research-folder', [
            'records' => $records,
        ])->title('My folder');
    }
}
