<?php
include 'includes/check_user.php';
include 'includes/fetch_records.php';
// Dashboard of a student page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Examinations</title>

    <!-- Fonts & Icons -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css">
    <link rel="icon" href="../assets/images/icon.png">
    
    <!-- CSS Plugins -->
    <link rel="stylesheet" href="../assets/plugins/pace-master/themes/blue/pace-theme-flash.css">
    <link rel="stylesheet" href="../assets/plugins/uniform/css/uniform.default.min.css">
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/font-awesome.css">
    <link rel="stylesheet" href="../assets/plugins/line-icons/simple-line-icons.css">
    <link rel="stylesheet" href="../assets/plugins/offcanvasmenueffects/css/menu_cornerbox.css">
    <link rel="stylesheet" href="../assets/plugins/switchery/switchery.min.css">
    <link rel="stylesheet" href="../assets/plugins/3d-bold-navigation/css/style.css">
    <link rel="stylesheet" href="../assets/plugins/slidepushmenus/css/component.css">
    <link rel="stylesheet" href="../assets/plugins/datatables/css/jquery.datatables.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables/css/jquery.datatables_themeroller.css">
    <link rel="stylesheet" href="../assets/plugins/x-editable/bootstrap3-editable/css/bootstrap-editable.css">
    <link rel="stylesheet" href="../assets/plugins/bootstrap-datepicker/css/datepicker3.css">
    <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../assets/css/modern.min.css">
    <link rel="stylesheet" href="../assets/css/themes/green.css" class="theme-color">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/button-styles.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <!-- JS Head Scripts -->
    <script src="../assets/plugins/3d-bold-navigation/js/modernizr.js"></script>
    <script src="../assets/plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>
</head>

<body class="page-header-fixed">
    <div class="overlay"></div>

    <main class="page-content content-wrap">
        <!-- Navbar -->
        <div class="navbar">
            <div class="navbar-inner">
                <div class="sidebar-pusher">
                    <a href="javascript:void(0);" class="waves-effect waves-button waves-classic push-sidebar">
                        <i class="fa fa-bars"></i>
                    </a>
                </div>
                <div class="topmenu-outer">
                    <div class="top-menu">
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="logout.php" class="log-out waves-effect waves-button waves-classic">
                                    <span><i class="fa fa-sign-out m-r-xs"></i>Log out</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="page-sidebar sidebar">
            <div class="page-sidebar-inner slimscroll">
                <ul class="menu accordion-menu">
                    <li><a href="Dashboard.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-home"></span><p>Dashboard</p></a></li>
                    <li><a href="profile.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-lock"></span><p>My Profile</p></a></li>
                    <li class="active"><a href="./" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-list-alt"></span><p>My Examinations</p></a></li>
                    <li><a href="results.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-credit-card"></span><p>Exam Results</p></a></li>
                </ul>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-inner">
            <div class="page-title">
                <h3>My Examinations</h3>
            </div>

            <?php
            // Display error messages
            if (isset($_GET['error'])) {
                $error_message = '';
                switch ($_GET['error']) {
                    case 'exam_not_available':
                        $error_message = isset($_GET['message']) ? urldecode($_GET['message']) : 'This exam is not available at this time.';
                        break;
                    default:
                        $error_message = 'An error occurred. Please try again.';
                }
                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Error!</strong> ' . $error_message . '
                      </div>';
            }
            ?>

            <div id="main-wrapper">
                <div class="row">
                    <!-- Notices -->
                    <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">
                        <?php
                        include '../database/config.php';
                        $sql = "SELECT * FROM tbl_notice";
                        $result = $conn->query($sql);
                        ?>
                        <marquee direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<label><b>' . htmlspecialchars($row['notice']) . '</b></label> ';
                                }
                            }
                            ?>
                        </marquee>
                        </div>
                    </div>

                    <!-- Enhanced Exam Display -->
                    <div class="col-md-12">
                                    <?php
                        // Include the enhanced exam status system
                        include '../includes/student_exam_status.php';

                                    // Get categorized exams for the student's class
                        $categorized_exams = categorizeExamsForStudent($myclass, $conn);
                                    $active_exams = $categorized_exams['active'];
                                    $pending_exams = $categorized_exams['pending'];
                                    $past_exams = $categorized_exams['past'];

                        // Display Active Exams
                                    if (count($active_exams) > 0) {
                            echo '<div class="panel panel-success">
                                                <div class="panel-heading">
                                        <h4><i class="fa fa-play-circle"></i> Active Exams - Start Now!</h4>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Subject</th>
                                                                    <th>Exam Name</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                                    <th>Start Time</th>
                                                                    <th>End Time</th>
                                                                    <th>Duration</th>
                                                        <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                            
                            foreach ($active_exams as $exam) {
                                            echo '<tr>
                                        <td>' . htmlspecialchars($exam['subject']) . '</td>
                                        <td>' . htmlspecialchars($exam['exam_name']) . '</td>
                                        <td>' . formatDate($exam['date']) . '</td>
                                        <td>' . formatDate($exam['end_exam_date']) . '</td>
                                        <td>' . formatTime($exam['start_time']) . '</td>
                                        <td>' . formatTime($exam['end_time']) . '</td>
                                        <td>' . $exam['duration'] . ' minutes</td>
                                        <td>
                                            <a class="btn btn-success btn-rounded btn-sm" href="take-assessment.php?id=' . $exam['exam_id'] . '">
                                                <i class="fa fa-play"></i> Start Exam
                                            </a>
                                        </td>
                                                  </tr>';
                                        }
                            
                                        echo '</tbody></table></div></div></div>';
                        }

                        // Display Pending Exams with Countdown
                        if (count($pending_exams) > 0) {
                            echo generateExamTable($pending_exams, 'pending', true);
                        }

                        // Display Past Exams
                        if (count($past_exams) > 0) {
                            echo generateExamTable($past_exams, 'past', true);
                        }

                        // Add countdown JavaScript if there are pending exams
                        if (count($pending_exams) > 0) {
                            echo generateCountdownJavaScript($pending_exams);
                                    }

                                    $conn->close();
                                    ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="cd-overlay"></div>

    <!-- JS Scripts -->
    <script src="../assets/plugins/jquery/jquery-2.1.4.min.js"></script>
    <script src="../assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="../assets/plugins/pace-master/pace.min.js"></script>
    <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/plugins/switchery/switchery.min.js"></script>
    <script src="../assets/plugins/uniform/jquery.uniform.min.js"></script>
    <script src="../assets/plugins/offcanvasmenueffects/js/classie.js"></script>
    <script src="../assets/plugins/offcanvasmenueffects/js/main.js"></script>
    <script src="../assets/plugins/waves/waves.min.js"></script>
    <script src="../assets/plugins/3d-bold-navigation/js/main.js"></script>
    <script src="../assets/plugins/jquery-mockjax-master/jquery.mockjax.js"></script>
    <script src="../assets/plugins/moment/moment.js"></script>
    <script src="../assets/plugins/datatables/js/jquery.datatables.min.js"></script>
    <script src="../assets/plugins/x-editable/bootstrap3-editable/js/bootstrap-editable.js"></script>
    <script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="../assets/js/modern.min.js"></script>
    <script src="../assets/js/pages/table-data.js"></script>
    <script src="../assets/plugins/select2/js/select2.min.js"></script>
    <?php include '../includes/javascript_scheduler.php'; ?>
</body>
</html>
