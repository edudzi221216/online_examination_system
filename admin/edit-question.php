<?php
include 'includes/check_user.php';

if (!isset($_GET['id']) || !isset($_GET['eid'])) {
    header("Location: ./");
    exit();
}

include '../database/config.php';

// sanitize incoming parameters
$question_id = mysqli_real_escape_string($conn, $_GET['id']);
$exam_id = mysqli_real_escape_string($conn, $_GET['eid']);

// fetch question details
$q_sql = "SELECT * FROM tbl_questions WHERE question_id = '{$question_id}' AND exam_id = '{$exam_id}'";
$q_result = $conn->query($q_sql);

if (!$q_result || $q_result->num_rows === 0) {
    $_SESSION['error'] = "Question not found.";
    header("Location: view-questions.php?eid=" . urlencode($exam_id));
    exit();
}

$question = $q_result->fetch_assoc();

// fetch exam details to check permissions
$exam_sql = "
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
$exam_result = $conn->query($exam_sql);

if (!$exam_result || $exam_result->num_rows === 0) {
    header("Location: ./");
    exit();
}

$exam = $exam_result->fetch_assoc();
$exam_name = $exam['exam_name'];
$created_by_type = $exam['created_by_type'];
$created_by = $exam['created_by'];

// Check if current user can edit this question
$can_edit = false;

if ($_SESSION['role'] === 'admin') {
    // Admin can only edit questions for exams they created
    $can_edit = ($created_by_type === 'admin');
} elseif ($_SESSION['role'] === 'teacher') {
    // Teachers can only edit questions for exams they created
    $can_edit = ($created_by_type === 'teacher' && $created_by === $_SESSION['myid']);
}

// If user cannot edit, redirect them
if (!$can_edit) {
    $_SESSION['error'] = "You don't have permission to edit this question. Only the exam creator can modify it.";
    header("Location: view-questions.php?eid=" . urlencode($exam_id));
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_question = mysqli_real_escape_string($conn, $_POST['question']);
    $new_qmarks = mysqli_real_escape_string($conn, $_POST['qmarks']);
    $new_answer = mysqli_real_escape_string($conn, $_POST['answer']);
    $question_type = $question['type'];
    
    // Update question
    $update_sql = "UPDATE tbl_questions SET question = ?, Qmarks = ?, answer = ? WHERE question_id = ? AND exam_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssss", $new_question, $new_qmarks, $new_answer, $question_id, $exam_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Question updated successfully.";
        header("Location: view-questions.php?eid=" . urlencode($exam_id));
        exit();
    } else {
        $_SESSION['error'] = "Failed to update question.";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Edit Question - <?php echo htmlspecialchars($exam_name); ?></title>
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
                </ul>
            </div>
        </div>

        <div class="page-inner">
            <div class="page-title">
                <h3>Edit Question - <?php echo htmlspecialchars($exam_name); ?></h3>
                <p class="text-muted">Question ID: <?php echo htmlspecialchars($question_id); ?></p>
            </div>

            <div id="main-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h4><i class="fa fa-edit"></i> Edit Question</h4>
                            </div>
                            <div class="panel-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <label for="question">Question:</label>
                                        <textarea class="form-control" id="question" name="question" rows="4" required><?php echo htmlspecialchars($question['question']); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="qmarks">Marks:</label>
                                        <input type="number" class="form-control" id="qmarks" name="qmarks" value="<?php echo htmlspecialchars($question['Qmarks']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="answer">Answer:</label>
                                        <input type="text" class="form-control" id="answer" name="answer" value="<?php echo htmlspecialchars($question['answer']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Question Type:</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($question['type']); ?>" readonly>
                                        <small class="text-muted">Question type cannot be changed</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <a href="view-questions.php?eid=<?php echo urlencode($exam_id); ?>" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to Questions
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Update Question
                                        </button>
                                    </div>
                                </form>
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
</body>
</html>
