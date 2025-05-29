<?php

namespace App\Console\Commands;

use App\Events\TaskDueDateApproachingEvent;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTasksDueDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-tasks-due-date-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check due date of all tasks';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle(): void
    {
        $tasks = DB::table('tasks')->get();
        $actualDate = new DateTime();

        foreach ($tasks as $task) {
            $dueDate = new DateTime($task->due_date);

            $interval = $actualDate->diff($dueDate);
            $daysDifference = $interval->days;

            if ($daysDifference <= 1 && $dueDate > $actualDate) {
              event(new TaskDueDateApproachingEvent($task));
            }
        }
    }
}
