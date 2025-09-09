<?php
/**
 * Test page for JavaScript Exam Scheduler
 * This page can be used to test the scheduler functionality
 */

// Include database configuration
require_once 'database/config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>JavaScript Scheduler Test</title>
    <meta charset="UTF-8">
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <script src="assets/plugins/jquery/jquery-2.1.4.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>JavaScript Exam Scheduler Test</h1>
                <p>This page tests the JavaScript-based exam scheduler functionality.</p>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Scheduler Status</h3>
                    </div>
                    <div class="panel-body">
                        <div id="schedulerStatus">Initializing...</div>
                        <button class="btn btn-primary" onclick="manualUpdateExamStatus()">Manual Update</button>
                        <button class="btn btn-success" onclick="startScheduler()">Start Scheduler</button>
                        <button class="btn btn-danger" onclick="stopScheduler()">Stop Scheduler</button>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Exam Status</h3>
                    </div>
                    <div class="panel-body">
                        <div id="examStatus">Loading...</div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Log</h3>
                    </div>
                    <div class="panel-body">
                        <div id="log" style="height: 200px; overflow-y: scroll; background-color: #f5f5f5; padding: 10px; font-family: monospace;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Test functions
        function log(message) {
            const logDiv = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            logDiv.innerHTML += `[${timestamp}] ${message}<br>`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }
        
        function updateStatus(message) {
            document.getElementById('schedulerStatus').innerHTML = message;
        }
        
        function updateExamStatus() {
            // Get exam status from database
            $.ajax({
                url: 'includes/scheduler_ajax.php',
                type: 'POST',
                data: {
                    action: 'get_exam_status',
                    exam_id: 'TEST123'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        document.getElementById('examStatus').innerHTML = 
                            `<strong>Exam:</strong> ${response.data.exam_name}<br>
                             <strong>Status:</strong> ${response.data.status}<br>
                             <strong>Result Publication:</strong> ${response.data.result_publish_status}`;
                    } else {
                        document.getElementById('examStatus').innerHTML = 'No exam data available';
                    }
                },
                error: function() {
                    document.getElementById('examStatus').innerHTML = 'Error loading exam data';
                }
            });
        }
        
        // Override console.log to also log to our display
        const originalLog = console.log;
        console.log = function(...args) {
            originalLog.apply(console, args);
            log(args.join(' '));
        };
        
        // Initialize
        $(document).ready(function() {
            log('Page loaded');
            updateStatus('Ready');
            updateExamStatus();
            
            // Test the scheduler
            setTimeout(function() {
                log('Testing scheduler...');
                if (typeof initJavaScriptScheduler === 'function') {
                    initJavaScriptScheduler();
                    updateStatus('Scheduler initialized');
                } else {
                    updateStatus('Scheduler not available');
                    log('ERROR: Scheduler functions not loaded');
                }
            }, 1000);
        });
    </script>
    
    <!-- Include JavaScript Scheduler -->
    <?php include 'includes/javascript_scheduler.php'; ?>
</body>
</html> 