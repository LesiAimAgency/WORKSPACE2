<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PerformanceService
{
    /**
     * Get the performance ranking for a specific time period.
     *
     * @param  mixed  $period  '7', '14', '30', 'all'
     * @return Collection
     */
    public function getRanking($period = '30')
    {
        $query = User::query()
            ->select('users.id', 'users.name', 'users.avatar', 'users.department')
            ->leftJoin('tasks', function ($join) use ($period) {
                $join->on('users.id', '=', 'tasks.assigned_to')
                    ->where('tasks.approval_status', '=', 'approved');

                if (in_array($period, ['7', '14', '30', 7, 14, 30])) {
                    $join->where('tasks.completed_at', '>=', Carbon::now()->subDays((int) $period));
                }
            })
            ->selectRaw('COUNT(tasks.id) as approved_tasks_count')
            ->groupBy('users.id', 'users.name', 'users.avatar', 'users.department')
            ->orderByDesc('approved_tasks_count');

        return $query->get();
    }

    /**
     * Get Gold ranking (if gold feature is enabled)
     */
    public function getGoldRanking()
    {
        return User::query()
            ->select('id', 'name', 'avatar', 'department', 'gold')
            ->orderByDesc('gold')
            ->limit(10)
            ->get();
    }

    /**
     * Get personal performance report for a specific user and time period.
     *
     * @param  int  $userId
     * @param  string  $period  'month', 'week', 'year', 'all'
     * @return array
     */
    public function getPersonalReport($userId, $period = 'month')
    {
        $query = Task::where('assigned_to', $userId);

        if ($period === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        } elseif ($period === 'week') {
            $query->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        $tasks = $query->get();

        $totalTasks = $tasks->count();
        $completedTasks = 0;
        $inProgressTasks = 0;
        $onTimeTasks = 0;
        $lateTasks = 0;
        $totalProcessingDays = 0;
        $totalGoldEarned = 0;

        $today = Carbon::today();

        foreach ($tasks as $task) {
            // Đang làm / Hoàn thành
            if ($task->completed_at) {
                $completedTasks++;

                // Tính thời gian xử lý
                $start = $task->start_date ? Carbon::parse($task->start_date) : Carbon::parse($task->created_at);
                $end = Carbon::parse($task->completed_at);
                $days = $start->diffInDays($end);
                $totalProcessingDays += max(0, $days); // Không lấy số âm
            } else {
                $inProgressTasks++;
            }

            // Đúng hạn / Trễ hạn
            if ($task->deadline) {
                $deadline = Carbon::parse($task->deadline)->endOfDay();
                if ($task->completed_at) {
                    if (Carbon::parse($task->completed_at)->lte($deadline)) {
                        $onTimeTasks++;
                    } else {
                        $lateTasks++;
                    }
                } else {
                    if ($today->lte($deadline)) {
                        $onTimeTasks++; // Vẫn đang trong hạn
                    } else {
                        $lateTasks++; // Đã quá hạn
                    }
                }
            } else {
                // Không có deadline -> mặc định đúng hạn
                $onTimeTasks++;
            }

            // Gold
            if ($task->gold_awarded) {
                $totalGoldEarned += $task->gold;
            }
        }

        $averageProcessingTime = $completedTasks > 0 ? round($totalProcessingDays / $completedTasks, 1) : 0;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'on_time_tasks' => $onTimeTasks,
            'late_tasks' => $lateTasks,
            'average_processing_time' => $averageProcessingTime, // days
            'total_gold_earned' => $totalGoldEarned,
            'tasks' => $tasks, // Return tasks to display in the view if needed
        ];
    }
}
