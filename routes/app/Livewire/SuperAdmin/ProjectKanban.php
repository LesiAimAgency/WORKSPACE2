<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Project;
use App\Models\Task;
use Livewire\Component;

class ProjectKanban extends Component
{
    public Project $project;

    // Listen to task updates to refresh the board if needed
    protected $listeners = ['taskUpdated' => '$refresh'];

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function updateTaskStatus($taskId, $newStatus)
    {
        $task = Task::find($taskId);
        if ($task && in_array($newStatus, ['pending', 'in_progress', 'review', 'completed'])) {
            $task->update(['status' => $newStatus]);

            // Generate notification or activity log here if needed

            $this->dispatch('taskUpdated');
        }
    }

    public function render()
    {
        // Get tasks for this project grouped by status
        $tasks = $this->project->tasks()->with('assignedUser')->get();

        $columns = [
            'pending' => [
                'title' => 'Cần làm (Pending)',
                'color' => 'gray',
                'tasks' => $tasks->where('status', 'pending')->values(),
            ],
            'in_progress' => [
                'title' => 'Đang làm (In Progress)',
                'color' => 'blue',
                'tasks' => $tasks->where('status', 'in_progress')->values(),
            ],
            'review' => [
                'title' => 'Chờ duyệt (Review)',
                'color' => 'amber',
                'tasks' => $tasks->where('status', 'review')->values(),
            ],
            'completed' => [
                'title' => 'Hoàn thành (Done)',
                'color' => 'emerald',
                'tasks' => $tasks->where('status', 'completed')->values(),
            ],
        ];

        return view('livewire.superadmin.project-kanban', [
            'columns' => $columns,
        ]);
    }
}
