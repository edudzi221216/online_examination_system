<?php
session_start();
include 'includes/check_user.php';
include '../includes/bulk_question_upload.php';
?>
<!DOCTYPE html>
<html>

<head>

    <title>Bulk Upload Questions</title>

    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta charset="UTF-8">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
    <link href="../assets/plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet" />
    <link href="../assets/plugins/uniform/css/uniform.default.min.css" rel="stylesheet" />
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/line-icons/simple-line-icons.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/offcanvasmenueffects/css/menu_cornerbox.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/3d-bold-navigation/css/style.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/slidepushmenus/css/component.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/datatables/css/jquery.datatables_themeroller.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/x-editable/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css">
    <link href="../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />
    <link href="../assets/images/icon.png" rel="icon">
    <link href="../assets/css/modern.min.css" rel="stylesheet" type="text/css" />

</head>

<body class="page-header-fixed">
    <div class="overlay"></div>

    <main class="page-content content-wrap">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">
                        <h2><span class="text-primary">Online Examination System</span></h2>
                    </a>
                </div>
                <div class="navbar-form navbar-left">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-user"></span> <?php echo $myfname; ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="profile.php"><i class="fa fa-user"></i> Profile</a></li>
                            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-sidebar sidebar">
            <div class="page-sidebar-inner slimscroll">
                <div class="sidebar-header">
                    <div class="sidebar-brand">
                        <a href="index.php">
                            <h2><span class="text-primary">OES</span></h2>
                        </a>
                    </div>
                    <div class="sidebar-search">
                        <div>
                            <input type="text" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>
                <ul class="menu accordion-menu">
                    <li><a href="index.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-home"></span>
                            <p>Dashboard</p>
                        </a></li>
                    <li><a href="examinations.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-list"></span>
                            <p>Examinations</p>
                        </a></li>
                    <li><a href="bulk_upload_questions.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-upload"></span>
                            <p>Bulk Upload Questions</p>
                        </a></li>
                    <li><a href="questions.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-question-sign"></span>
                            <p>Questions</p>
                        </a></li>
                    <li><a href="add-questions.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-plus"></span>
                            <p>Add Questions</p>
                        </a></li>
                    <li><a href="classresults.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-stats"></span>
                            <p>Class Results</p>
                        </a></li>
                </ul>
            </div>
        </div>
        <div class="page-inner">
            <div class="page-title">
                <h3>Bulk Upload Questions</h3>
            </div>
            <div id="main-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-body">
                                        <?php
                                        // Display session messages here
                                        if (isset($_SESSION['upload_message'])) {
                                            $message = $_SESSION['upload_message'];
                                            echo '<div class="alert alert-' . htmlspecialchars($message['type']) . ' alert-dismissible" role="alert">';
                                            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                            echo $message['text'];
                                            echo '</div>';
                                            unset($_SESSION['upload_message']); // Clear the message after displaying
                                        }
                                        ?>
                                        <div role="tabpanel">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#upload" role="tab" data-toggle="tab">Upload Questions</a></li>
                                                <li role="presentation"><a href="#template" role="tab" data-toggle="tab">Download Template</a></li>
                                                <li role="presentation"><a href="#history" role="tab" data-toggle="tab">Upload History</a></li>
                                            </ul>

                                            <div class="tab-content">
                                                <!-- Upload Questions Tab -->
                                                <div role="tabpanel" class="tab-pane active fade in" id="upload">
                                                    <form action="pages/process_bulk_upload.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                                                        <input type="hidden" name="uploadType" value="questions">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="examSelect">Select Exam</label>
                                                                    <select class="form-control" name="exam_id" id="examSelect" required>
                                                                        <option value="" selected disabled>-- Select Exam --</option>
                                                                        <?php
                                                                        include '../database/config.php';
                                                                        $sql = "SELECT exam_id, exam_name, subject, class 
                                                                            FROM tbl_examinations 
                                                                            WHERE created_by = '$mytid' 
                                                                            ORDER BY exam_name";
                                                                        $result = $conn->query($sql);
                                                                        if ($result->num_rows > 0) {
                                                                            while ($row = $result->fetch_assoc()) {
                                                                                print '<option value="' . $row['exam_id'] . '">' . $row['exam_name'] . ' (' . $row['subject'] . ' - ' . $row['class'] . ')</option>';
                                                                            }
                                                                        }
                                                                        $conn->close();
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="csvFile">Upload CSV File</label>
                                                                    <input type="file" class="form-control" name="csv_file" id="csvFile" accept=".csv" required>
                                                                    <small class="text-muted">Only CSV files are allowed. Maximum file size: 5MB</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="alert alert-info">
                                                            <h4><i class="fa fa-info-circle"></i> Instructions</h4>
                                                            <ul>
                                                                <li>Download the template first to understand the required format</li>
                                                                <li>CSV should have columns: Question Type, Question, Marks, Correct Answer, Option A, Option B, Option C, Option D</li>
                                                                <li>Question Types: MC (Multiple Choice), FB (Fill-in-the-Blank), TF (True/False)</li>
                                                                <li>For Multiple Choice: Use A, B, C, D or option1, option2, option3, option4 for correct answer</li>
                                                                <li>For True/False: Use TRUE, FALSE, T, or F for correct answer</li>
                                                                <li>For Fill-in-the-Blank: Put the correct answer in the Correct Answer column</li>
                                                            </ul>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary" id="uploadBtn">
                                                            <i class="fa fa-upload"></i> Upload Questions
                                                        </button>
                                                    </form>
                                                </div>

                                                <!-- Download Template Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="template">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="panel panel-info">
                                                                <div class="panel-heading">
                                                                    <h4><i class="fa fa-download"></i> Download CSV Template</h4>
                                                                </div>
                                                                <div class="panel-body">
                                                                    <p>Click the button below to download a sample CSV template with example questions.</p>
                                                                    <a href="pages/download_template.php" class="btn btn-success">
                                                                        <i class="fa fa-download"></i> Download Template
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <div class="panel panel-default">
                                                                <div class="panel-heading">
                                                                    <h4><i class="fa fa-table"></i> CSV Format</h4>
                                                                </div>
                                                                <div class="panel-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Column</th>
                                                                                    <th>Description</th>
                                                                                    <th>Required</th>
                                                                                    <th>Example</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>Question Type</td>
                                                                                    <td>MC, FB, or TF</td>
                                                                                    <td>Yes</td>
                                                                                    <td>MC</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Question</td>
                                                                                    <td>The question text</td>
                                                                                    <td>Yes</td>
                                                                                    <td>What is the capital of France?</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Marks</td>
                                                                                    <td>Points for this question</td>
                                                                                    <td>Yes</td>
                                                                                    <td>20</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Correct Answer</td>
                                                                                    <td>The correct answer</td>
                                                                                    <td>Yes</td>
                                                                                    <td>A or option1</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Option A</td>
                                                                                    <td>First option (MC only)</td>
                                                                                    <td>MC only</td>
                                                                                    <td>Paris</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Option B</td>
                                                                                    <td>Second option (MC only)</td>
                                                                                    <td>MC only</td>
                                                                                    <td>London</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Option C</td>
                                                                                    <td>Third option (MC only)</td>
                                                                                    <td>MC only</td>
                                                                                    <td>Berlin</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Option D</td>
                                                                                    <td>Fourth option (MC only)</td>
                                                                                    <td>MC only</td>
                                                                                    <td>Madrid</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Upload History Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="history">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped" id="historyTable">
                                                            <thead>
                                                                <tr>
                                                                    <th>Upload Date</th>
                                                                    <th>Exam</th>
                                                                    <th>File Name</th>
                                                                    <th>Total Questions</th>
                                                                    <th>Successful</th>
                                                                    <th>Failed</th>
                                                                    <th>Status</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                include '../database/config.php';
                                                                $uploads = getUploadHistory($myid, 'teacher', $conn);
                                                                foreach ($uploads as $upload) {
                                                                    $status_class = '';
                                                                    switch ($upload['status']) {
                                                                        case 'Completed':
                                                                            $status_class = 'success';
                                                                            break;
                                                                        case 'Processing':
                                                                            $status_class = 'warning';
                                                                            break;
                                                                        case 'Failed':
                                                                            $status_class = 'danger';
                                                                            break;
                                                                    }
                                                                    echo '<tr>';
                                                                    echo '<td>' . date('M j, Y H:i', strtotime($upload['upload_date'])) . '</td>';
                                                                    echo '<td>' . htmlspecialchars($upload['exam_name']) . '</td>';
                                                                    echo '<td>' . htmlspecialchars($upload['file_name']) . '</td>';
                                                                    echo '<td>' . $upload['total_questions'] . '</td>';
                                                                    echo '<td><span class="label label-success">' . $upload['successful_uploads'] . '</span></td>';
                                                                    echo '<td><span class="label label-danger">' . $upload['failed_uploads'] . '</span></td>';
                                                                    echo '<td><span class="label label-' . $status_class . '">' . $upload['status'] . '</span></td>';
                                                                    echo '<td>';
                                                                    if ($upload['error_log']) {
                                                                        echo '<button class="btn btn-xs btn-info" onclick="viewErrors(\'' . htmlspecialchars($upload['error_log']) . '\')">View Errors</button>';
                                                                    }
                                                                    echo '</td>';
                                                                    echo '</tr>';
                                                                }
                                                                $conn->close();
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="cd-overlay"></div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Upload Errors</h4>
                </div>
                <div class="modal-body">
                    <pre id="errorContent"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/plugins/jquery/jquery-2.1.4.min.js"></script>
    <script src="../assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="../assets/plugins/pace-master/pace.min.js"></script>
    <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/plugins/switchery/switchery.min.js"></script>
    <script src="../assets/plugins/uniform/js/jquery.uniform.standalone.js"></script>
    <script src="../assets/plugins/classie/classie.js"></script>
    <script src="../assets/plugins/waves/waves.min.js"></script>
    <script src="../assets/plugins/3d-bold-navigation/js/main.js"></script>
    <script src="../assets/plugins/jquery-mockjax-master/jquery.mockjax.js"></script>
    <script src="../assets/plugins/moment/moment.js"></script>
    <script src="../assets/plugins/datatables/js/jquery.datatables.min.js"></script>
    <script src="../assets/plugins/x-editable/bootstrap3-editable/js/bootstrap-editable.js"></script>
    <script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="../assets/js/modern.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#historyTable').DataTable({
                "order": [
                    [0, "desc"]
                ]
            });

            // File size validation
            $('#csvFile').change(function() {
                var file = this.files[0];
                var maxSize = 5 * 1024 * 1024; // 5MB

                if (file && file.size > maxSize) {
                    alert('File size must be less than 5MB');
                    this.value = '';
                }
            });

            // Form submission
            $('#uploadForm').submit(function(e) {
                var examId = $('#examSelect').val();
                var file = $('#csvFile')[0].files[0];

                if (!examId) {
                    alert('Please select an exam');
                    e.preventDefault();
                    return false;
                }

                if (!file) {
                    alert('Please select a CSV file');
                    e.preventDefault();
                    return false;
                }

                // Show loading state
                $('#uploadBtn').html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
                $('#uploadBtn').prop('disabled', true);
            });
        });

        function viewErrors(errorLog) {
            $('#errorContent').text(errorLog);
            $('#errorModal').modal('show');
        }
    </script>
    
    <!-- Include JavaScript Scheduler -->
    <?php include '../includes/javascript_scheduler.php'; ?>
</body>

</html>