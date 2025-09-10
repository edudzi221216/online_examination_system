<?php
class Schedule {
    const MINUTE  = "MINUTE";
    const HOURLY  = "HOURLY";
    const DAILY   = "DAILY";
    const WEEKLY  = "WEEKLY";
    const MONTHLY = "MONTHLY";
    const ONCE    = "ONCE";
    const ONSTART = "ONSTART";
    const ONLOGON = "ONLOGON";
    const ONIDLE  = "ONIDLE";
}

/**
 * Create a scheduled task
 * Works both in CLI and Browser
 */
function createScheduledTask($taskName, $phpFile, $schedule = Schedule::DAILY, $time = '12:00', $modifier = null)
{
    $phpPath = PHP_BINARY;
    $phpFile = realpath($phpFile);

    if (!$phpFile) {
        echo "❌ PHP file not found: $phpFile\n";
        return;
    }

    $taskRun = "\"$phpPath\" \"$phpFile\"";

    $command = 'schtasks /Create /TN "' . $taskName . '" /TR "' . $taskRun . '" /SC ' . $schedule;

    // Auto-handle modifier for MINUTE or HOURLY schedules
    if (in_array($schedule, [Schedule::MINUTE, Schedule::HOURLY])) {
        $command .= ' /MO ' . (int)($modifier ?? 1);
    }

    // Fixed start time only for certain schedules
    if (in_array($schedule, [Schedule::DAILY, Schedule::WEEKLY, Schedule::MONTHLY, Schedule::ONCE]) && $time) {
        $command .= ' /ST ' . $time;
    }

    $command .= ' /F';

    $output = shell_exec($command . ' 2>&1');

    if (stripos($output, "SUCCESS") !== false) {
        echo "✅ Scheduled task '$taskName' created successfully.\n";
    } else {
        echo "❌ Failed to create scheduled task '$taskName'.\n";
    }

    echo "Command run:\n$command\n\n";
    echo "Output:\n$output\n";
}

// scheduled tasks
$tasks = [
    [
        'name' => 'ExamScheduler',
        'file' => __DIR__ . '/auto_exam_manager.php',
        'schedule' => Schedule::MINUTE,
        'modifier' => 1 // every 1 minute
    ],
    [
        'name' => 'EnhancedExamScheduler',
        'file' => __DIR__ . '/enhanced_exam_scheduler.php',
        'schedule' => Schedule::MINUTE,
        'modifier' => 5 // every 5 minutes
    ],
    [
        'name' => 'UpdateExamStatus',
        'file' => __DIR__ . '/update_exam_status.php',
        'schedule' => Schedule::MINUTE,
        'modifier' => 5 // every 5 minutes
    ]
];

// create the tasks
foreach ($tasks as $task) {
    createScheduledTask(
        $task['name'],
        $task['file'],
        $task['schedule'],
        $task['time'] ?? null,
        $task['modifier'] ?? null
    );
}
