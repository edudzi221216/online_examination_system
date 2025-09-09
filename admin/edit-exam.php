<?php 
include 'includes/check_user.php'; 
if (isset($_GET['eid'])) {
include '../database/config.php';
$exam_id = mysqli_real_escape_string($conn, $_GET['eid']);  
$sql = "SELECT e.*, 
        CASE 
            WHEN e.created_by_type = 'teacher' THEN CONCAT(t.first_name, ' ', t.last_name)
            WHEN e.created_by_type = 'admin' THEN 'Administrator'
            ELSE 'Unknown'
        END as creator_name,
        e.created_by_type
        FROM tbl_examinations e 
        LEFT JOIN tbl_teacher t ON e.created_by = t.teacher_id AND e.created_by_type = 'teacher'
        WHERE e.exam_id = '$exam_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
     $excate = $row['class'];
	 $exsubject = $row['subject'];
	 $exname = $row['exam_name'];
	 $exdate = $row['date'];
	 $exend_exam_date = $row['end_exam_date'];
	 $exstart_time = $row['start_time'];
	 $exend_time = $row['end_time'];
	 $exduration = $row['duration'];
	 $expassmark = $row['passmark'];
     $f_marks=$row['full_marks'];
	 $exreex = $row['re_exam'];
	 $result_publish_start_date = $row['result_publish_start_date'];
	 $result_publish_start_time = $row['result_publish_start_time'];
	 $result_publish_end_date = $row['result_publish_end_date'];
	 $result_publish_end_time = $row['result_publish_end_time'];
	 $result_publish_status = $row['result_publish_status'];
     $created_by_type = $row['created_by_type'];
     $creator_name = $row['creator_name'];
     
     // Check if admin can edit this exam
     $can_edit = ($created_by_type === 'admin');
    }
} else {
    header("location:./");
}
$conn->close();	
}else{
	header("location:./");
}
?>
<!DOCTYPE html>
<html>
   
<head>
        
        <title>Edit Exam</title>
        
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">

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
        <link href="../assets/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css"/>	
        <link href="../assets/plugins/datatables/css/jquery.datatables_themeroller.css" rel="stylesheet" type="text/css"/>	
        <link href="../assets/plugins/x-editable/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css">
        <link href="../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css"/>
		<link href="../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/images/icon.png" rel="icon">
        <link href="../assets/css/modern.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/themes/green.css" class="theme-color" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link href="assets/css/button-styles.css" rel="stylesheet" type="text/css"/>
        <script src="../assets/plugins/3d-bold-navigation/js/modernizr.js"></script>
        <script src="../assets/plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>
		

        <link href="../assets/plugins/summernote-master/summernote.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"/>
        
		

        
    </head>
    <body class="page-header-fixed">
        <div class="overlay"></div>
        
        <main class="page-content content-wrap">
            <div class="navbar">
                <div class="navbar-inner">
                    
                    
                    <div class="topmenu-outer">
                        <div class="top-menu">
                            <ul class="nav navbar-nav navbar-right">
                                

                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle waves-effect waves-button waves-classic" data-toggle="dropdown">
                                        <span class="user-name"><?php echo "$myfname"; ?><i class="fa fa-angle-down"></i></span>
										
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
                                <li>

                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-sidebar sidebar">
                <div class="page-sidebar-inner slimscroll">
                    
                    <ul class="menu accordion-menu">
                        <li><a href="./" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-home"></span><p>Dashboard</p></a></li>
                        <li><a href="acad.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-tasks"></span><p>Academic Year</p></a></li>
                        <li><a href="classes.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-th-list"></span><p>Class</p></a></li>
                        <li><a href="subject.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-file"></span><p>Subjects</p></a></li>
                        <li><a href="accountant.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-user"></span><p>Accountant</p></a></li>
                        <li><a href="teacher.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-file"></span><p>Teachers</p></a></li>
                        <li><a href="students.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-user"></span><p>Students</p></a></li>
                        <li><a href="examinations.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-list-alt"></span><p>Examinations</p></a></li>
                        <li><a href="classresults.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-credit-card"></span><p>Exam Results</p></a></li>
                        <li><a href="notice.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-comment"></span><p>Notice</p></a></li>
                        <li><a href="blockclassstd.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-eye-close"></span><p>Block Student</p></a></li>

                    </ul>
                </div>
            </div>
            <div class="page-inner">
                <div class="page-title">
                    <h3>Edit Exam - <?php echo "$exname"; ?></h3>
                </div>
                
                <?php
                // Display error messages
                if (isset($_GET['error'])) {
                    $error_message = '';
                    switch ($_GET['error']) {
                        case 'duplicate':
                            $error_message = 'Exam already exists for this subject and class.';
                            break;
                        case 'time_format':
                            $error_message = 'Invalid time format. Please use HH:MM:SS format.';
                            break;
                        case 'time_logic':
                            $error_message = 'End time must be after start time.';
                            break;
                        case 'update':
                            $error_message = 'Error occurred while updating the exam. Please try again.';
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
                                        <?php if (!$can_edit): ?>
                                        <div class="alert alert-warning">
                                            <i class="fa fa-exclamation-triangle"></i>
                                            <strong>Read-Only Mode:</strong> This exam was created by <strong><?php echo $creator_name; ?></strong> (Teacher). 
                                            Administrators can view but cannot edit exams created by teachers.
                                        </div>
                                        <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i>
                                            <strong>Edit Mode:</strong> This exam was created by <strong><?php echo $creator_name; ?></strong>.
                                        </div>
                                        <?php endif; ?>
                                        
                                        <form method="post" action="pages/update_exam.php">
										<div class="form-group">
                                            <label for="exampleInputEmail1">Exam Name</label>
                                            <input type="text" class="form-control" value="<?php echo"$exname"; ?>" placeholder="Enter exam name" name="exam" required autocomplete="off" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                        </div>
										<div class="form-group">
                                            <label for="exampleInputEmail1">Duration (Minutes)</label>
                                            <input type="number" class="form-control" value="<?php echo"$exduration"; ?>" placeholder="Enter duration in minutes" name="duration" required autocomplete="off" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                        </div>
										<div class="form-group">
                                            <label for="exampleInputEmail1">Passmark </label>
                                            <input type="number" class="form-control" value="<?php echo"$expassmark"; ?>" placeholder="Enter passmark" name="passmark" required autocomplete="off" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                        </div>
										<div class="form-group">
                                            <label for="exampleInputEmail1">Full Marks</label>
                                            <input type="number" class="form-control" value="<?php echo"$f_marks"; ?>" placeholder="Enter Full marks for this subject examination" name="fmarks" required autocomplete="off" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                        </div>
									<div class="form-group">
                                    <label >Start Exam Date</label>
                                    <input type="text" class="form-control date-picker" value="<?php echo"$exdate"; ?>" name="date" required autocomplete="off" placeholder="Select Start Exam Date" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                    </div>
									<div class="form-group">
                                    <label >End Exam Date</label>
                                    <input type="text" class="form-control date-picker" value="<?php echo"$exend_exam_date"; ?>" name="end_exam_date" required autocomplete="off" placeholder="Select End Exam Date" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                    </div>
									<div class="form-group">
                                    <label >Start Time</label>
                                    <input type="time" class="form-control" value="<?php echo"$exstart_time"; ?>" name="start_time" required autocomplete="off" placeholder="Select Start Time" step="1" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                    </div>
									<div class="form-group">
                                    <label >End Time</label>
                                    <input type="time" class="form-control" value="<?php echo"$exend_time"; ?>" name="end_time" required autocomplete="off" placeholder="Select End Time" step="1" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                    </div>
										<div class="form-group">
                                            <label for="exampleInputEmail1">Select Subject</label>
                                            <select class="form-control" name="subject" required <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
											<option value="" selected disabled>-Select subject</option>
											<?php
											include '../database/config.php';
											$sql = "SELECT name FROM tbl_subjects";
                                            $result = $conn->query($sql);

                                            if ($result->num_rows > 0) {
    
                                            while($row = $result->fetch_assoc()) {
											if ($exsubject == $row['name']) {
											print '<option selected value="'.$row['name'].'">'.$row['name'].'</option>';	
											}else{
											print '<option value="'.$row['name'].'">'.$row['name'].'</option>';	
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
                                            <select class="form-control" name="class" required <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
											<option value="" selected disabled>-- Select Class --</option>
											<?php
											include '../database/config.php';
											$sql = "SELECT name FROM tbl_classes";
                                            $result = $conn->query($sql);

                                            if ($result->num_rows > 0) {
    
                                            while($row = $result->fetch_assoc()) {
                                          	if ($excate == $row['name']) {
											print '<option selected value="'.$row['name'].'">'.$row['name'].'</option>';	
											}else{
											print '<option value="'.$row['name'].'">'.$row['name'].'</option>';	
											}
                                            }
                                           } else {
                          
                                            }
                                             $conn->close();
											 ?>
											
											</select>
                                        </div>

                                        <!-- Result Publication Scheduling -->
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <h4><i class="fa fa-calendar"></i> Result Publication Scheduling</h4>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="resultPublishStartDate">Result Publication Start Date</label>
                                                            <input type="text" class="form-control date-picker" id="resultPublishStartDate" name="result_publish_start_date" value="<?php echo $result_publish_start_date; ?>" autocomplete="off" placeholder="Select Start Date" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="resultPublishStartTime">Result Publication Start Time</label>
                                                            <input type="time" class="form-control" id="resultPublishStartTime" name="result_publish_start_time" value="<?php echo $result_publish_start_time; ?>" autocomplete="off" placeholder="Select Start Time" step="1" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="resultPublishEndDate">Result Publication End Date</label>
                                                            <input type="text" class="form-control date-picker" id="resultPublishEndDate" name="result_publish_end_date" value="<?php echo $result_publish_end_date; ?>" autocomplete="off" placeholder="Select End Date" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="resultPublishEndTime">Result Publication End Time</label>
                                                            <input type="time" class="form-control" id="resultPublishEndTime" name="result_publish_end_time" value="<?php echo $result_publish_end_time; ?>" autocomplete="off" placeholder="Select End Time" step="1" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
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
									
									
									
									 <input type="hidden" name="examid" value="<?php echo "$exam_id"; ?>">


                                        <button type="submit" class="btn btn-primary" id="submitBtn" <?php echo (!$can_edit) ? 'disabled' : ''; ?>>
                                            <?php echo ($can_edit) ? 'Update Exam' : 'View Only - Cannot Edit'; ?>
                                        </button>
                                       </form>
                                       
                                       <script>
                                       // Form validation
                                       document.querySelector('form').addEventListener('submit', function(e) {
                                           <?php if (!$can_edit): ?>
                                           e.preventDefault();
                                           alert('This exam cannot be edited as it was created by a teacher.');
                                           return false;
                                           <?php endif; ?>
                                           
                                           var startTime = document.querySelector('input[name="start_time"]').value;
                                           var endTime = document.querySelector('input[name="end_time"]').value;
                                           
                                           if (startTime && endTime) {
                                               var start = new Date('2000-01-01 ' + startTime);
                                               var end = new Date('2000-01-01 ' + endTime);
                                               
                                               if (end <= start) {
                                                   e.preventDefault();
                                                   alert('End time must be after start time.');
                                                   return false;
                                               }
                                           }
                                           
                                           // Show loading state
                                           document.getElementById('submitBtn').innerHTML = 'Updating...';
                                           document.getElementById('submitBtn').disabled = true;
                                       });
                                       </script>
                                    </div>
                                </div>  
  
                            </div>
                        </div>


                        </div>
                    </div>
                </div>
                
            </div>
            <!--<div class="oes">Designed and Developed by Koushik Sadhu &amp; Nishikant Mandal.</div>-->
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
		

    </body>

</html>