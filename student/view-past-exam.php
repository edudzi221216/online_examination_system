<?php
include 'includes/check_user.php';
include '../database/config.php';

$exam_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get exam details
$sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    header("Location: ./");
    exit();
}

$exam = $result->fetch_assoc();
$exam_name = $exam['exam_name'];
$subject = $exam['subject'];
$class = $exam['class'];

// Check if student has taken this exam
$check_sql = "SELECT * FROM tbl_assessment_records WHERE student_id = '$myid' AND exam_id = '$exam_id'";
$check_result = $conn->query($check_sql);

if (!$check_result || $check_result->num_rows === 0) {
    $_SESSION['error'] = "You haven't taken this exam yet.";
    header("Location: ./");
    exit();
}

$exam_record = $check_result->fetch_assoc();
$score = $exam_record['score'];
$status = $exam_record['status'];

// Check if exam results have been officially published
$exam_sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
$exam_result = $conn->query($exam_sql);
$exam_data = $exam_result->fetch_assoc();

$current_time = time();
$exam_end_time = strtotime($exam_data['end_exam_date'] . ' ' . $exam_data['end_time']);
$results_published = false;

// Check the rstatus field in assessment records first
if (isset($exam_record['rstatus']) && $exam_record['rstatus'] === 'Result Published') {
    $results_published = true;
} else {
    // Fallback: Results are considered published if:
    // 1. Exam has ended (current time > exam end time)
    // 2. Student has a score recorded
    if ($current_time > $exam_end_time && !empty($score)) {
        $results_published = true;
    }
}

if (!$results_published) {
    $_SESSION['error'] = "Exam results have not been officially published yet. You can view questions once the exam period ends and results are published.";
    header("Location: ./");
    exit();
}

// Get questions for this exam
$q_sql = "SELECT * FROM tbl_questions WHERE exam_id = '$exam_id' ORDER BY question_id ASC";
$q_result = $conn->query($q_sql);

$questions = [];
if ($q_result && $q_result->num_rows > 0) {
    while ($q_row = $q_result->fetch_assoc()) {
        $questions[] = $q_row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Past Exam - <?php echo htmlspecialchars($exam_name); ?></title>

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
                    <li><a href="./" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-list-alt"></span><p>My Examinations</p></a></li>
                    <li class="active"><a href="view-past-exam.php?id=<?php echo urlencode($exam_id); ?>" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-eye-open"></span><p>View Past Exam</p></a></li>
                    <li><a href="results.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-credit-card"></span><p>Exam Results</p></a></li>
                </ul>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-inner">
            <div class="page-title">
                <h3>Past Exam: <?php echo htmlspecialchars($exam_name); ?></h3>
                <p class="text-muted">Subject: <?php echo htmlspecialchars($subject); ?> | Class: <?php echo htmlspecialchars($class); ?></p>
            </div>

            <div id="main-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Exam Results Summary -->
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4><i class="fa fa-trophy"></i> Your Exam Results</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-success"><?php echo htmlspecialchars($score); ?></h3>
                                            <p><strong>Score</strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="<?php echo strtolower($status) === 'pass' ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo htmlspecialchars(strtoupper($status)); ?>
                                            </h3>
                                            <p><strong>Status</strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-info"><?php echo count($questions); ?></h3>
                                            <p><strong>Total Questions</strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <a href="./" class="btn btn-primary">
                                                <i class="fa fa-arrow-left"></i> Back to Exams
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Display -->
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h4><i class="fa fa-question-circle"></i> Exam Questions</h4>
                            </div>
                            <div class="panel-body">
                                <?php if (empty($questions)): ?>
                                    <div class="alert alert-info">No questions found for this exam.</div>
                                <?php else: ?>
                                    <div class="tabs-below" role="tabpanel">
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <?php
                                            $qno = 1;
                                            foreach ($questions as $q) {
                                                $type = !empty($q['question_type']) ? $q['question_type'] : (!empty($q['type']) ? $q['type'] : '');
                                                $qmarks = htmlspecialchars($q['Qmarks'] ?? '');
                                                $qs = htmlspecialchars($q['question'] ?? '');
                                                $op1 = htmlspecialchars($q['option1'] ?? '');
                                                $op2 = htmlspecialchars($q['option2'] ?? '');
                                                $op3 = htmlspecialchars($q['option3'] ?? '');
                                                $op4 = htmlspecialchars($q['option4'] ?? '');
                                                $answer = htmlspecialchars($q['answer'] ?? '');

                                                // Badge for question type
                                                $type_badge = '<span class="label label-default">'.htmlspecialchars($type).'</span>';
                                                if ($type === 'MC') $type_badge = '<span class="label label-primary">Multiple Choice</span>';
                                                if ($type === 'FB') $type_badge = '<span class="label label-success">Fill-in-the-Blank</span>';
                                                if ($type === 'TF') $type_badge = '<span class="label label-warning">True/False</span>';
                                            ?>
                                            <div role="tabpanel" class="tab-pane <?php echo ($qno === 1) ? 'active' : ''; ?>" id="tab<?php echo $qno; ?>">
                                                <div class="question-pane">
                                                    <p style="font-size:17px;">
                                                        <b>Question <?php echo $qno; ?>.</b> 
                                                        <?php echo $qs; ?> 
                                                        <?php echo $type_badge; ?>
                                                    </p>
                                                    <p style="text-align:right;"><b>Marks: <?php echo $qmarks; ?></b></p>

                                                    <?php if ($type === 'FB'): ?>
                                                        <p><strong>Answer:</strong> <?php echo $answer; ?></p>
                                                    <?php elseif ($type === 'TF'): ?>
                                                        <p><strong>Answer:</strong> <?php echo $answer; ?></p>
                                                    <?php else: /* MC or other */ ?>
                                                        <p style="font-size:15px;"><strong>Options:</strong></p>
                                                        <p style="font-size:15px;">A: <?php echo $op1; ?></p>
                                                        <p style="font-size:15px;">B: <?php echo $op2; ?></p>
                                                        <p style="font-size:15px;">C: <?php echo $op3; ?></p>
                                                        <p style="font-size:15px;">D: <?php echo $op4; ?></p>
                                                        <p style="font-size:15px;"><strong>Correct Answer:</strong> <?php echo $answer; ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php
                                                $qno++;
                                            }
                                            ?>
                                        </div>

                                        <!-- Tab navigation -->
                                        <ul class="nav nav-tabs" role="tablist">
                                            <?php
                                            $total = count($questions);
                                            for ($i = 1; $i <= $total; $i++) {
                                                $active = ($i === 1) ? 'class="active"' : '';
                                                echo '<li role="presentation" '.$active.'><a href="#tab'.$i.'" role="tab" data-toggle="tab">Q'.$i.'</a></li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
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
</body>
</html>
