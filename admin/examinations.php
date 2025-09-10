<?php
// PHP includes and initial setup remain the same
include 'includes/check_user.php';
include '../database/config.php'; // Assuming this connects to your database

$class = $_GET['cn'] ?? ''; // Use null coalescing operator for safer access
$class = mysqli_real_escape_string($conn, $class); // Sanitize input
?>
<!DOCTYPE html>
<html>

<head>
    <title>Examinations</title>
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
    <link href="../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/images/icon.png" rel="icon">
    <link href="../assets/css/modern.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/themes/green.css" class="theme-color" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="../assets/css/button-styles.css" rel="stylesheet" type="text/css" />

    <script src="../assets/plugins/3d-bold-navigation/js/modernizr.js"></script>
    <script src="../assets/plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>

    <link href="../assets/plugins/summernote-master/summernote.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="../assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />

    <!-- Tailwind CSS for custom modal -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for the modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 90%;
            width: 400px;
            text-align: center;
        }

        .modal-header {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .modal-footer button {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
        }

        .modal-footer .btn-primary {
            background-color: #34D399;
            /* Green-500 */
            color: white;
            border: none;
        }

        .modal-footer .btn-primary:hover {
            background-color: #10B981;
            /* Green-600 */
        }

        .exam-status-display {
            font-weight: bold;
        }

        .countdown-timer {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body class="page-header-fixed">
    <div class="overlay"></div>

    <main class="page-content content-wrap">
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
                            <li class="dropdown">
                                <ul class="dropdown-menu dropdown-list" role="menu">
                                    <li role="presentation"><a href="logout.php"><i class="fa fa-sign-out m-r-xs"></i>Log out</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="logout.php" class="log-out waves-effect waves-button waves-classic">
                                    <span><i class="fa fa-sign-out m-r-xs"></i>Log out</span>
                                </a>
                            </li>
                            <li></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-sidebar sidebar">
            <div class="page-sidebar-inner slimscroll">
                </div>
                <ul class="menu accordion-menu">
                <li><a href="./" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-home"></span>
                        <p>Dashboard</p>
                    </a></li>
                <li><a href="acad.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-tasks"></span>
                        <p>Academic Year</p>
                    </a></li>
                <li><a href="classes.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-th-list"></span>
                        <p>Class</p>
                    </a></li>
                <li><a href="subject.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-file"></span>
                        <p>Subjects</p>
                    </a></li>
                <li><a href="accountant.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-user"></span>
                        <p>Accountant</p>
                    </a></li>
                <li><a href="teacher.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-user"></span>
                        <p>Teachers</p>
                    </a></li>
                <li><a href="students.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-user"></span>
                        <p>Students</p>
                    </a></li>
                <li class="active"><a href="classexamination.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-list-alt"></span>
                        <p>Examinations</p>
                    </a></li>
                <li><a href="classresults.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-credit-card"></span>
                        <p>Exam Results</p>
                    </a></li>
                <li><a href="notice.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-comment"></span>
                        <p>Notice</p>
                    </a></li>
                <li><a href="blockclassstd.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-eye-close"></span>
                        <p>Block Student</p>
                    </a></li>
                </ul>
            </div>
        </div>
        <div class="page-inner">
            <div class="page-title">
                <h3>Manage Examinations</h3>
            </div>

            <?php
            // Display success/error messages
            if (isset($_GET['success'])) {
                $success_message = '';
                switch ($_GET['success']) {
                    case 'added':
                        $success_message = 'Exam has been successfully added.';
                        break;
                    case 'updated':
                        $success_message = 'Exam has been successfully updated.';
                        break;
                    default:
                        $success_message = 'Operation completed successfully.';
                }
                echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Success!</strong> ' . $success_message . '
                      </div>';
            }

            if (isset($_GET['error'])) {
                $error_message = '';
                switch ($_GET['error']) {
                    case 'duplicate':
                        $error_message = 'Exam already exists for this subject and class.';
                        break;
                    case 'insert':
                        $error_message = 'Error occurred while adding the exam. Please try again.';
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
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-body">
                                        <div role="tabpanel">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#tab5" role="tab" data-toggle="tab">Examinations</a></li>
                                                <li role="presentation"><a href="#tab6" role="tab" data-toggle="tab">Add Exam</a></li>
                                            </ul>

                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active fade in" id="tab5">
                                                    <div class="table-responsive">
                                                        <?php
                                                        // Re-include config for this block as it might have been closed by previous includes
                                                        include '../database/config.php';
                                                        $sql = "SELECT e.*, 
                                                                CASE 
                                                                    WHEN e.created_by_type = 'admin' THEN 'Admin'
                                                                    WHEN e.created_by_type = 'teacher' THEN CONCAT('Teacher: ', t.first_name, ' ', t.last_name)
                                                                    ELSE 'Unknown'
                                                                END as creator_name
                                                                FROM tbl_examinations e 
                                                                LEFT JOIN tbl_teacher t ON e.created_by = t.teacher_id AND e.created_by_type = 'teacher'
                                                                WHERE e.class='$class'
                                                                ORDER BY e.date DESC, e.start_time DESC";
                                                        $result = $conn->query($sql);

                                                        if ($result->num_rows > 0) {
                                                            print '
                                                            <table id="example" class="display table" style="width: 100%; cellspacing: 0;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Exam name</th>
                                                                        <th>Class</th>
                                                                        <th>Subject</th>
                                                                        <th>Start Date</th>
                                                                        <th>End Date</th>
                                                                        <th>Start Time</th>
                                                                        <th>End Time</th>
                                                                        <th>Created By</th>
                                                                        <th>Start/Stop Exam</th>
                                                                        <th>Exam Status</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th>Exam name</th>
                                                                        <th>Class</th>
                                                                        <th>Subject</th>
                                                                        <th>Start Date</th>
                                                                        <th>End Date</th>
                                                                        <th>Start Time</th>
                                                                        <th>End Time</th>
                                                                        <th>Created By</th>
                                                                        <th>Start/Stop Exam</th>
                                                                        <th>Exam Status</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </tfoot>
                                                                <tbody>';

                                                            while ($row = $result->fetch_assoc()) {
                                                                // Combine date and time for easier JavaScript parsing
                                                                $full_start_time = $row['date'] . 'T' . $row['start_time'];
                                                                $full_end_time   = $row['date'] . 'T' . $row['end_time'];
                                                                
                                                                // Check if current user can edit this exam (only the creator can edit, regardless of role)
                                                                $can_edit = ($row['created_by'] === $_SESSION['myid'] && $row['created_by_type'] === $_SESSION['role']);
                                                                
                                                                print '
                                                                <tr id="exam-row-' . $row['exam_id'] . '"
                                                                    data-exam-id="' . $row['exam_id'] . '"
                                                                    data-start-time="' . $full_start_time . '"
                                                                    data-end-time="' . $full_end_time . '"
                                                                    data-current-status="' . $row['status'] . '">
                                                                    <td>' . htmlspecialchars($row['exam_name']) . '</td>
                                                                    <td>' . htmlspecialchars($row['class']) . '</td>
                                                                    <td>' . htmlspecialchars($row['subject']) . '</td>
                                                                    <td>' . htmlspecialchars($row['date']) . '</td>
                                                                    <td>' . htmlspecialchars($row['end_exam_date']) . '</td>
                                                                    <td>' . htmlspecialchars($row['start_time']) . '</td>
                                                                    <td>' . htmlspecialchars($row['end_time']) . '</td>
                                                                    <td>' . htmlspecialchars($row['creator_name']) . '</td>
                                                                    <td class="exam-control-buttons">
                                                                        <button class="btn btn-success btn-rounded btn-sm start-exam-btn" data-exam-id="' . $row['exam_id'] . '" disabled>
                                                                            <i class="fa fa-play"></i> Start
                                                                        </button>
                                                                        <button class="btn btn-danger btn-rounded btn-sm stop-exam-btn" data-exam-id="' . $row['exam_id'] . '" disabled>
                                                                            <i class="fa fa-stop"></i> Stop
                                                                        </button>
                                                                    </td>
                                                                    <td class="exam-status-display">
                                                                        <span class="status-text">' . htmlspecialchars($row['status']) . '</span>
                                                                        <div class="countdown-timer"></div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="btn-group" role="group">
                                                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                                Select <span class="caret"></span>
                                                                            </button>
                                                                            <ul class="dropdown-menu" role="menu">';
                                                                            
                                                                        if (true) {
                                                                            print '
                                                                                <li><a href="edit-exam.php?eid=' . $row['exam_id'] . '">Edit Exam</a></li>
                                                                                <li><a href="view-questions.php?eid=' . $row['exam_id'] . '">View Questions</a></li>
                                                                                <li><a href="add-questions.php?eid=' . $row['exam_id'] . '">Add Questions</a></li>
                                                                                <li><a href="pages/drop_ex.php?id=' . $row['exam_id'] . '">Delete</a></li>';
                                                                        } else {
                                                                            print '
                                                                                <li class="disabled"><a href="#" style="color: #999; cursor: not-allowed;">View Questions (Not Authorized)</a></li>
                                                                                <li class="disabled"><a href="#" style="color: #999; cursor: not-allowed;">Edit Exam (Not Authorized)</a></li>
                                                                                <li class="disabled"><a href="#" style="color: #999; cursor: not-allowed;">Add Questions (Not Authorized)</a></li>
                                                                                <li class="disabled"><a href="#" style="color: #999; cursor: not-allowed;">Delete (Not Authorized)</a></li>';
                                                                        }
                                                                        
                                                                        print '
                                                                            </ul>
                                                                        </div>
                                                                    </td>
                                                                </tr>';
                                                            }

                                                            print '
                                                                </tbody>
                                                            </table>';
                                                        } else {
                                                            print '
                                                                <div class="alert alert-info" role="alert">
                                                                    <em> No Examinations Found. </em>
                                                                </div>';
                                                        }
                                                        $conn->close(); // Close connection after fetching data
                                                        ?>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane fade" id="tab6">
                                                    <form action="pages/add_exam.php" method="POST">
                                                        <div class="form-group">
                                                            <label>Exam Name</label>
                                                            <input type="text" class="form-control" name="exam" placeholder="Enter exam name" required autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Exam Duration (Minutes)</label>
                                                            <input type="number" class="form-control" name="duration" placeholder="Enter exam duration" required autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Passmark</label>
                                                            <input type="number" class="form-control" name="passmark" placeholder="Enter passmark" required autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Full Marks</label>
                                                            <input type="number" class="form-control" name="fmarks" placeholder="Enter Full marks for this subject examination" required autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Start Exam Date</label>
                                                            <input type="text" class="form-control date-picker" name="date" placeholder="Select Start Exam Date" required autocomplete="off">
                                                        </div>
                                                         <div class="form-group">
                                                            <label>End Exam Date</label>
                                                            <input type="text" class="form-control date-picker" name="end_exam_date" placeholder="Select End Exam Date" required autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Start Time</label>
                                                            <input type="time" class="form-control" name="start_time" placeholder="Select Start Time" required autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>End Time</label>
                                                            <input type="time" class="form-control" name="end_time" placeholder="Select End Time" required autocomplete="off">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">Select Subject</label>
                                                            <select class="form-control" name="subject" required>
                                                                <option value="" selected disabled>-Select subject</option>
                                                                <?php
                                                                include '../database/config.php';
                                                                $sql = "SELECT name FROM tbl_subjects";
                                                                $result = $conn->query($sql);

                                                                if ($result->num_rows > 0) {

                                                                    while ($row = $result->fetch_assoc()) {
                                                                        if ($exsubject == $row['name']) {
                                                                            print '<option selected value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                                                        } else {
                                                                            print '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                                                        }
                                                                    }
                                                                } else {
                                                                }
                                                                $conn->close();
                                                                ?>

                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">Select Class</label>
                                                            <select class="form-control" name="class" required>
                                                                <option value="" selected disabled>-- Select Class --</option>
                                                                <?php
                                                                include '../database/config.php';
                                                                $sql = "SELECT name FROM tbl_classes";
                                                                $result = $conn->query($sql);

                                                                if ($result->num_rows > 0) {

                                                                    while ($row = $result->fetch_assoc()) {
                                                                        if ($excate == $row['name']) {
                                                                            print '<option selected value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                                                        } else {
                                                                            print '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                                                                        }
                                                                    }
                                                                } else {
                                                                }
                                                                $conn->close();
                                                                ?>

                                                            </select>
                                                        </div>

                                                        <div class="panel panel-info">
                                                            <div class="panel-heading">
                                                                <h4><i class="fa fa-calendar"></i> Result Publication Scheduling</h4>
                                                            </div>
                                                            <div class="panel-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Result Publication Start Date</label>
                                                                            <input type="text" class="form-control date-picker" name="result_publish_start_date" placeholder="Select Start Date">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Result Publication Start Time</label>
                                                                            <input type="time" class="form-control" name="result_publish_start_time" placeholder="Select Start Time" step="1">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Result Publication End Date</label>
                                                                            <input type="text" class="form-control date-picker" name="result_publish_end_date" placeholder="Select End Date">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Result Publication End Time</label>
                                                                            <input type="time" class="form-control" name="result_publish_end_time" placeholder="Select End Time" step="1">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="alert alert-info">
                                                                    <i class="fa fa-info-circle"></i>
                                                                    <strong>Note:</strong> Leave these fields empty to publish results manually.
                                                                    If dates and times are set, results will be automatically published and unpublished based on the schedule.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="examid" value="<?php echo $_GET['eid']; ?>">
                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
                                                    </form>
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

    <!-- Custom Modal Structure -->
    <div id="customModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header" id="modalTitle"></div>
            <div class="modal-body" id="modalMessage"></div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="hideModal()">OK</button>
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
    <script src="../assets/plugins/summernote-master/summernote.min.js"></script>
    <script src="../assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
    <script src="../assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
    <script src="../assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
    <script src="../assets/js/pages/form-elements.js"></script>

    <script>
        // --- Custom Modal Functions (replacing alert()) ---
        function showModal(title, message, type = 'info') {
            const modal = document.getElementById('customModal');
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;

            const modalContent = modal.querySelector('.modal-content');
            modalContent.classList.remove('border-green-500', 'border-red-500', 'border-blue-500');
            if (type === 'success') {
                modalContent.classList.add('border-green-500');
            } else if (type === 'error') {
                modalContent.classList.add('border-red-500');
            } else {
                modalContent.classList.add('border-blue-500');
            }
            modal.classList.remove('hidden');
        }

        function hideModal() {
            document.getElementById('customModal').classList.add('hidden');
        }

        // --- Form Validation (existing logic, updated to use custom modal) ---
        document.querySelector('form').addEventListener('submit', function(e) {
            var startTime = document.querySelector('input[name="start_time"]').value;
            var endTime = document.querySelector('input[name="end_time"]').value;

            if (startTime && endTime) {
                var start = new Date('2000-01-01 ' + startTime);
                var end = new Date('2000-01-01 ' + endTime);

                if (end <= start) {
                    e.preventDefault();
                    showModal('Validation Error', 'End time must be after start time.', 'error');
                    return false;
                }
            }

            // Show loading state
            document.getElementById('submitBtn').innerHTML = 'Submitting...';
            document.getElementById('submitBtn').disabled = true;
        });

        // --- Examination Automation Logic ---

        /**
         * Formats milliseconds into HH:MM:SS string.
         * @param {number} ms - Milliseconds.
         * @returns {string} Formatted time string.
         */
        function formatTime(ms) {
            const totalSeconds = Math.floor(ms / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            return [hours, minutes, seconds]
                .map(unit => unit < 10 ? '0' + unit : unit)
                .join(':');
        }

        /**
         * Updates the display and button states for all exam rows.
         */
        function updateExamDisplays() {
            const examRows = document.querySelectorAll('tr[data-exam-id]');
            const currentTime = new Date();

            examRows.forEach(row => {
                const examId = row.dataset.examId;
                const startTimeStr = row.dataset.startTime;
                const endTimeStr = row.dataset.endTime;
                let currentStatus = row.dataset.currentStatus; // Get status from data attribute

                const examStartTime = new Date(startTimeStr);
                const examEndTime = new Date(endTimeStr);

                const statusTextElement = row.querySelector('.exam-status-display .status-text');
                const countdownTimerElement = row.querySelector('.exam-status-display .countdown-timer');
                const startButton = row.querySelector('.start-exam-btn');
                const stopButton = row.querySelector('.stop-exam-btn');

                // Ensure elements exist before trying to update
                if (!statusTextElement || !countdownTimerElement || !startButton || !stopButton) {
                    console.warn(`Missing elements for exam ID: ${examId}`);
                    return;
                }

                let displayStatus = currentStatus; // Default to status from PHP

                // Logic for dynamic status and button control
                if (currentTime < examStartTime) {
                    // Exam is scheduled for the future
                    displayStatus = 'Scheduled';
                    startButton.disabled = true;
                    stopButton.disabled = true;

                    const timeLeft = examStartTime.getTime() - currentTime.getTime();
                    if (timeLeft > 0) {
                        countdownTimerElement.innerText = `Starts in: ${formatTime(timeLeft)}`;
                    } else {
                        countdownTimerElement.innerText = ''; // Should not happen if logic is correct
                    }
                } else if (currentTime >= examStartTime && currentTime < examEndTime) {
                    // Exam is currently active
                    displayStatus = 'Active';
                    startButton.disabled = false; // Enable start button
                    stopButton.disabled = false; // Enable stop button

                    const timeLeft = examEndTime.getTime() - currentTime.getTime();
                    if (timeLeft > 0) {
                        countdownTimerElement.innerText = `Ends in: ${formatTime(timeLeft)}`;
                    } else {
                        countdownTimerElement.innerText = '';
                    }
                } else {
                    // Exam has ended
                    displayStatus = 'Ended';
                    startButton.disabled = true;
                    stopButton.disabled = true;
                    countdownTimerElement.innerText = '';
                }

                // Update the displayed status
                statusTextElement.innerText = displayStatus;
            });
        }

        /**
         * Handles AJAX calls to update exam status on the server.
         * @param {string} examId - The ID of the exam.
         * @param {string} action - 'activate' or 'inactivate'.
         */
        async function handleExamAction(examId, action) {
            const url = action === 'activate' ? `pages/make_ex_ac.php?id=${examId}` : `pages/make_ex_in.php?id=${examId}`;

            try {
                // Disable buttons temporarily
                const row = document.getElementById(`exam-row-${examId}`);
                if (row) {
                    row.querySelectorAll('button').forEach(btn => btn.disabled = true);
                }

                const response = await fetch(url);
                const text = await response.text(); // Get response as text, assuming PHP returns simple string/redirects

                if (response.ok) {
                    // Assuming PHP handles the redirect or provides a success message
                    showModal('Success', `Exam ${action === 'activate' ? 'started' : 'stopped'} successfully!`, 'success');
                    // Re-fetch or update the data-current-status attribute after successful action
                    // For simplicity, we'll just re-run the display update, but a server response
                    // indicating the new status would be more robust.
                    // A full refresh or a specific API endpoint that returns JSON status would be better.
                    // For now, assume the action PHP script updates the DB and we refresh the UI.
                    // A better approach would be to have an API that returns the new status.
                    // For demonstration, we'll just re-run the display update.
                    updateExamDisplays(); // Re-evaluate all exams after an action
                } else {
                    showModal('Error', `Failed to ${action} exam: ${text}`, 'error');
                }
            } catch (error) {
                console.error('Error performing exam action:', error);
                showModal('Network Error', `Could not connect to server to ${action} exam.`, 'error');
            } finally {
                // Re-enable buttons based on the current time after the operation
                updateExamDisplays();
            }
        }

        // Attach event listeners to Start/Stop buttons
        document.addEventListener('DOMContentLoaded', () => {
            // Initial update on page load
            updateExamDisplays();

            // Update every second for countdowns and real-time status changes
            setInterval(updateExamDisplays, 1000);

            // Attach click listeners to all start/stop buttons
            document.querySelectorAll('.start-exam-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const examId = button.dataset.examId;
                    handleExamAction(examId, 'activate');
                });
            });

            document.querySelectorAll('.stop-exam-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const examId = button.dataset.examId;
                    handleExamAction(examId, 'inactivate');
                });
            });
        });
    </script>
</body>

</html>