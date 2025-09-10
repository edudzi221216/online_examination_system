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

            <?php include_once "includes/active_exams.php"; ?>
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
