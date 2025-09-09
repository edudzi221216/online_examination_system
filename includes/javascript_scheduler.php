<?php
/**
 * JavaScript-based Exam Scheduler
 * This file provides JavaScript functions to replace cron job functionality
 * for automatic exam availability and result publication management
 */

// Include the enhanced exam scheduler functions
include_once 'enhanced_exam_scheduler.php';
?>

<script>
/**
 * JavaScript-based Exam Scheduler
 * Replaces cron job functionality for automatic exam management
 */

// Global variables
let schedulerInterval;
let lastCheckTime = new Date();
const CHECK_INTERVAL = 60000; // Check every 1 minute (60000ms)
const AJAX_URL = '<?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"; ?>://<?php echo $_SERVER['HTTP_HOST']; ?>/ems/includes/scheduler_ajax.php';

/**
 * Initialize the JavaScript scheduler
 */
function initJavaScriptScheduler() {
    console.log('JavaScript Exam Scheduler initialized');
    
    // Start the scheduler
    startScheduler();
    
    // Set up periodic checks
    setInterval(function() {
        checkAndUpdateExamStatus();
    }, CHECK_INTERVAL);
    
    // Also check when page becomes visible (user returns to tab)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkAndUpdateExamStatus();
        }
    });
}

/**
 * Start the scheduler
 */
function startScheduler() {
    console.log('Starting JavaScript Exam Scheduler...');
    
    // Initial check
    checkAndUpdateExamStatus();
    
    // Set up periodic checks
    schedulerInterval = setInterval(function() {
        checkAndUpdateExamStatus();
    }, CHECK_INTERVAL);
}

/**
 * Stop the scheduler
 */
function stopScheduler() {
    if (schedulerInterval) {
        clearInterval(schedulerInterval);
        console.log('JavaScript Exam Scheduler stopped');
    }
}

/**
 * Check and update exam status via AJAX
 */
function checkAndUpdateExamStatus() {
    const currentTime = new Date();
    
    // Only check if enough time has passed since last check
    if (currentTime - lastCheckTime < CHECK_INTERVAL) {
        return;
    }
    
    lastCheckTime = currentTime;
    
    // Make AJAX request to update exam status
    $.ajax({
        url: AJAX_URL,
        type: 'POST',
        data: {
            action: 'update_exam_status',
            timestamp: currentTime.getTime()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Exam status updated successfully');
                
                // Update UI if there are changes
                if (response.changes && response.changes.length > 0) {
                    updateExamStatusUI(response.changes);
                }
                
                // Show notifications for important changes
                if (response.notifications && response.notifications.length > 0) {
                    showNotifications(response.notifications);
                }
            } else {
                console.error('Failed to update exam status:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
        }
    });
}

/**
 * Update exam status in the UI
 */
function updateExamStatusUI(changes) {
    changes.forEach(function(change) {
        const examRow = document.querySelector(`[data-exam-id="${change.exam_id}"]`);
        if (examRow) {
            // Update status display
            const statusCell = examRow.querySelector('.exam-status-display');
            if (statusCell) {
                statusCell.textContent = change.new_status;
                statusCell.className = 'exam-status-display ' + getStatusClass(change.new_status);
            }
            
            // Update countdown timer if exists
            const countdownCell = examRow.querySelector('.countdown-timer');
            if (countdownCell) {
                updateCountdownTimer(countdownCell, change);
            }
        }
    });
}

/**
 * Get CSS class for status
 */
function getStatusClass(status) {
    switch(status.toLowerCase()) {
        case 'active':
            return 'text-success';
        case 'inactive':
            return 'text-danger';
        case 'scheduled':
            return 'text-warning';
        default:
            return 'text-muted';
    }
}

/**
 * Update countdown timer
 */
function updateCountdownTimer(element, change) {
    if (change.start_time && change.end_time) {
        const now = new Date();
        const startTime = new Date(change.start_time);
        const endTime = new Date(change.end_time);
        
        if (now < startTime) {
            // Exam hasn't started yet
            const timeLeft = startTime - now;
            element.textContent = 'Starts in: ' + formatTimeLeft(timeLeft);
        } else if (now >= startTime && now <= endTime) {
            // Exam is active
            const timeLeft = endTime - now;
            element.textContent = 'Ends in: ' + formatTimeLeft(timeLeft);
        } else {
            // Exam has ended
            element.textContent = 'Exam ended';
        }
    }
}

/**
 * Format time left for display
 */
function formatTimeLeft(milliseconds) {
    const hours = Math.floor(milliseconds / (1000 * 60 * 60));
    const minutes = Math.floor((milliseconds % (1000 * 60 * 60)) / (1000 * 60));
    
    if (hours > 0) {
        return hours + 'h ' + minutes + 'm';
    } else {
        return minutes + 'm';
    }
}

/**
 * Show notifications
 */
function showNotifications(notifications) {
    notifications.forEach(function(notification) {
        // Create notification element
        const notificationDiv = document.createElement('div');
        notificationDiv.className = 'alert alert-' + notification.type + ' alert-dismissible';
        notificationDiv.innerHTML = `
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>${notification.title}</strong> ${notification.message}
        `;
        
        // Add to page
        const container = document.querySelector('.page-inner') || document.body;
        container.insertBefore(notificationDiv, container.firstChild);
        
        // Auto-remove after 10 seconds
        setTimeout(function() {
            if (notificationDiv.parentNode) {
                notificationDiv.parentNode.removeChild(notificationDiv);
            }
        }, 10000);
    });
}

/**
 * Manual trigger for exam status update
 */
function manualUpdateExamStatus() {
    console.log('Manual exam status update triggered');
    checkAndUpdateExamStatus();
}

/**
 * Get exam status for a specific exam
 */
function getExamStatus(examId) {
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: AJAX_URL,
            type: 'POST',
            data: {
                action: 'get_exam_status',
                exam_id: examId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    resolve(response.data);
                } else {
                    reject(response.message);
                }
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
    });
}

/**
 * Initialize scheduler when document is ready
 */
$(document).ready(function() {
    // Only initialize scheduler on pages that need it (examinations pages)
    const currentPage = window.location.pathname;
    if (currentPage.includes('examinations.php') || currentPage.includes('classexamination.php')) {
        // Initialize the JavaScript scheduler
        initJavaScriptScheduler();
        
        // Add manual update button if it doesn't exist
        if (!document.getElementById('manualUpdateBtn')) {
            const updateBtn = document.createElement('button');
            updateBtn.id = 'manualUpdateBtn';
            updateBtn.className = 'btn btn-sm btn-info';
            updateBtn.innerHTML = '<i class="fa fa-refresh"></i> Update Status';
            updateBtn.onclick = manualUpdateExamStatus;
            
            // Add to page header if possible
            const pageTitle = document.querySelector('.page-title');
            if (pageTitle) {
                pageTitle.appendChild(updateBtn);
            }
        }
    }
});

// Export functions for global access
window.ExamScheduler = {
    init: initJavaScriptScheduler,
    start: startScheduler,
    stop: stopScheduler,
    manualUpdate: manualUpdateExamStatus,
    getStatus: getExamStatus
};
</script> 