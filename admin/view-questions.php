<?php
include 'includes/check_user.php';

if (!isset($_GET['eid'])) {
    header("Location: ./");
    exit();
}

include '../database/config.php';

// sanitize incoming exam id
$exam_id = mysqli_real_escape_string($conn, $_GET['eid']);

// fetch exam with creator name (teacher or admin)
$sql = "
    SELECT e.*,
        CASE
            WHEN e.created_by_type = 'teacher' THEN CONCAT(t.first_name, ' ', t.last_name)
            WHEN e.created_by_type = 'admin' THEN 'Administrator'
            ELSE 'Unknown'
        END AS creator_name,
        e.created_by_type
    FROM tbl_examinations e
    LEFT JOIN tbl_teacher t ON e.created_by = t.teacher_id AND e.created_by_type = 'teacher'
    WHERE e.exam_id = '{$exam_id}'
    LIMIT 1
";
$examResult = $conn->query($sql);

if (!$examResult || $examResult->num_rows === 0) {
    header("Location: ./");
    exit();
}

$exam = $examResult->fetch_assoc();
$exam_name = $exam['exam_name'];
$f_marks = $exam['full_marks'];
$created_by_type = $exam['created_by_type'];
$creator_name = $exam['creator_name'];
$created_by = $exam['created_by'];

// Check if current user can view questions for this exam
$can_view_questions = false;
$can_edit = false;

if ($_SESSION['role'] === 'admin') {
    // Admin can view questions for all exams, but only edit questions for exams they created
    $can_view_questions = true;
    $can_edit = ($created_by_type === 'admin' && $created_by === $_SESSION['myid']);
} elseif ($_SESSION['role'] === 'teacher') {
    // Teachers can only view and edit questions for exams they created
    $can_view_questions = ($created_by_type === 'teacher' && $created_by === $_SESSION['myid']);
    $can_edit = ($created_by_type === 'teacher' && $created_by === $_SESSION['myid']);
}

// If user cannot view questions, redirect them
if (!$can_view_questions) {
    $_SESSION['error'] = "You don't have permission to view questions for this exam. Only the creator can view questions.";
    header("Location: examinations.php");
    exit();
}

// fetch questions for this exam
$qSql = "SELECT * FROM tbl_questions WHERE exam_id = '{$exam_id}' ORDER BY question_id ASC";
$qResult = $conn->query($qSql);

$questions = [];
if ($qResult && $qResult->num_rows > 0) {
    while ($qRow = $qResult->fetch_assoc()) {
        $questions[] = $qRow;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>View Exam - <?php echo htmlspecialchars($exam_name); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
    <link href="../assets/plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>
    <link href="../assets/plugins/uniform/css/uniform.default.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/line-icons/simple-line-icons.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/offcanvasmenueffects/css/menu_cornerbox.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/3d-bold-navigation/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/plugins/slidepushmenus/css/component.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/images/icon.png" rel="icon">
    <link href="../assets/css/modern.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/themes/green.css" class="theme-color" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="assets/css/button-styles.css" rel="stylesheet" type="text/css"/>
    <script src="../assets/plugins/3d-bold-navigation/js/modernizr.js"></script>
    <script src="../assets/plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>
    <style>
        /* small niceties */
        .question-pane { margin-bottom: 20px; }
        .question-actions { margin-top: 8px; }
        .question-badge { margin-left: 8px; }
    </style>
</head>
<body class="page-header-fixed page-horizontal-bar">
    <div class="overlay"></div>
    <main class="page-content content-wrap container">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="topmenu-outer">
                    <div class="top-menu">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle waves-effect waves-button waves-classic" data-toggle="dropdown">
                                    <span class="user-name"><?php echo htmlspecialchars($myfname ?? 'User'); ?><i class="fa fa-angle-down"></i></span>
                                </a>
                                <ul class="dropdown-menu dropdown-list" role="menu">
                                    <li role="presentation"><a href="profile.php"><i class="fa fa-user"></i>Profile</a></li>
                                </ul>
                            </li>
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

        <div class="horizontal-bar sidebar">
            <div class="page-sidebar-inner slimscroll">
                <ul class="menu accordion-menu">
                    <li><a href="./" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-home"></span><p>Dashboard</p></a></li>
                    <li><a href="classexamination.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-list-alt"></span><p>Examinations</p></a></li>
                    <li><a href="students.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-user"></span><p>Students</p></a></li>
                    <!-- other menu items -->
                </ul>
            </div>
        </div>

        <div class="page-inner">
            <div class="page-title">
                <h3>Question Paper of <?php echo htmlspecialchars($exam_name); ?></h3>
                <p class="text-muted">Created by: <?php echo htmlspecialchars($creator_name ?? 'Unknown'); ?> (<?php echo htmlspecialchars($created_by_type ?? ''); ?>)</p>
            </div>

            <div id="main-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <?php if (!$can_edit): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> 
                                <strong>Read-Only Access:</strong> You can only view questions for this exam. 
                                Only the creator can edit or add questions.
                            </div>
                        <?php endif; ?>
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Exam Information</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Exam Name:</strong> <?php echo htmlspecialchars($exam_name); ?></p>
                                        <p><strong>Full Marks:</strong> <?php echo htmlspecialchars($f_marks); ?></p>
                                        <p><strong>Created By:</strong> <?php echo htmlspecialchars($creator_name ?? 'Unknown'); ?> (<?php echo htmlspecialchars($created_by_type ?? ''); ?>)</p>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($can_edit): ?>
                                            <a href="add-questions.php?eid=<?php echo urlencode($exam_id); ?>" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Add New Question
                                            </a>
                                            <a href="bulk_upload_questions.php?exam_id=<?php echo urlencode($exam_id); ?>" class="btn btn-success">
                                                <i class="fa fa-upload"></i> Bulk Upload Questions
                                            </a>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> 
                                                To add or edit questions, contact the exam creator: <?php echo htmlspecialchars($creator_name ?? 'Unknown'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-body">
                                <div class="tabs-below" role="tabpanel">
                                    <?php if (empty($questions)): ?>
                                        <div class="alert alert-info">No questions found for this exam.</div>
                                    <?php else: ?>
                                    <!-- tab panes -->
                                    <div class="tab-content">
                                        <?php
                                            $qno = 1;
                                            foreach ($questions as $q) {
                                                // determine type (support both fields)
                                                $type = !empty($q['question_type']) ? $q['question_type'] : (!empty($q['type']) ? $q['type'] : '');
                                                $qmarks = htmlspecialchars($q['Qmarks'] ?? '');
                                                $qs = htmlspecialchars($q['question'] ?? '');
                                                $op1 = htmlspecialchars($q['option1'] ?? '');
                                                $op2 = htmlspecialchars($q['option2'] ?? '');
                                                $op3 = htmlspecialchars($q['option3'] ?? '');
                                                $op4 = htmlspecialchars($q['option4'] ?? '');
                                                $answer = htmlspecialchars($q['answer'] ?? '');
                                                $qid = $q['question_id'];

                                                // badge
                                                $type_badge = '<span class="label label-default question-badge">'.htmlspecialchars($type).'</span>';
                                                if ($type === 'MC') $type_badge = '<span class="label label-primary question-badge">Multiple Choice</span>';
                                                if ($type === 'FB') $type_badge = '<span class="label label-success question-badge">Fill-in-the-Blank</span>';
                                                if ($type === 'TF') $type_badge = '<span class="label label-warning question-badge">True/False</span>';

                                                // confirm string (escaped for JS)
                                                $confirmMsg = addslashes("Are you sure you want to delete question no. {$qno} ?");
                                        ?>
                                        <div role="tabpanel" class="tab-pane <?php echo ($qno === 1) ? 'active' : ''; ?>" id="tab<?php echo $qno; ?>">
                                            <div class="question-pane">
                                                <p style="font-size:17px;"><b>Question <?php echo $qno; ?>.</b> <?php echo $qs; ?> <?php echo $type_badge; ?></p>
                                                <p style="text-align:right;"><b>Marks: <?php echo $qmarks; ?></b></p>

                                                <?php if ($type === 'FB'): ?>
                                                    <p><input type="text" name="<?php echo $qno; ?>" class="form-control" placeholder="Enter your answer"></p>
                                                <?php elseif ($type === 'TF'): ?>
                                                    <p style="font-size:15px;"><b>Answer: <?php echo $answer; ?></b></p>
                                                <?php else: /* MC or other */ ?>
                                                    <p style="font-size:15px;">Option1: <?php echo $op1; ?></p>
                                                    <p style="font-size:15px;">Option2: <?php echo $op2; ?></p>
                                                    <p style="font-size:15px;">Option3: <?php echo $op3; ?></p>
                                                    <p style="font-size:15px;">Option4: <?php echo $op4; ?></p>
                                                    <p style="font-size:15px;"><b>Answer: <?php echo $answer; ?></b></p>
                                                <?php endif; ?>

                                                <div class="question-actions">
                                                    <?php if ($can_edit): ?>
                                                        <!-- Edit link -->
                                                        <a
                                                            class="btn btn-primary btn-sm"
                                                            href="edit-question.php?id=<?php echo $qid; ?>&eid=<?php echo urlencode($exam_id); ?>"
                                                        >
                                                            <i class="fa fa-edit"></i> Edit Question
                                                        </a>
                                                        
                                                        <!-- Delete link (confirmation) -->
                                                        <a
                                                            class="btn btn-danger btn-sm"
                                                            href="pages/drop_question.php?id=<?php echo $qid; ?>&eid=<?php echo urlencode($exam_id); ?>"
                                                            onclick="return confirm('<?php echo $confirmMsg; ?>');"
                                                        >
                                                            <i class="fa fa-trash-o"></i> Delete Question
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="fa fa-lock"></i> Read-only access
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                                $qno++;
                                            } // end foreach
                                        ?>
                                    </div>

                                    <!-- tab nav -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <?php
                                            $total = count($questions);
                                            for ($i = 1; $i <= $total; $i++) {
                                                $active = ($i === 1) ? 'class="active"' : '';
                                                echo '<li role="presentation" '.$active.'><a href="#tab'.$i.'" role="tab" data-toggle="tab">Q'.$i.'</a></li>';
                                            }
                                        ?>
                                    </ul>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </main>

    <div class="cd-overlay"></div>

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
    <script src="../assets/js/modern.min.js"></script>

    <script>
    // ensure the first tab pane shows on load (Bootstrap 3)
    $(function(){
        $('.nav-tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        // activate first tab if none active (defensive)
        if (!$('.nav-tabs li.active').length) {
            $('.nav-tabs li:first-child a').tab('show');
        }
    });
    </script>
</body>
</html>
