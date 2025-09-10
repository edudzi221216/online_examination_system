<?php 
include 'includes/check_user.php';

if (isset($_GET['eid'])) {
    include '../database/config.php';
    $exam_id = mysqli_real_escape_string($conn, $_GET['eid']);
    $question_id = $_GET["id"];

    $sql = "SELECT q.*, e.exam_name
                FROM tbl_questions q 
                JOIN tbl_examinations e ON q.exam_id = e.exam_id
                WHERE q.question_id = '$question_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $question = $result->fetch_assoc();
    } else {
        header("location:./");	
    }

}else{
header("location:./");	
}
?>
<!DOCTYPE html>
<html>
   
<head>
        
        <title>Update Question <?= $question_id ?></title>
        
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
        <link href="../assets/images/icon.png" rel="icon">
        <link href="../assets/css/modern.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/themes/green.css" class="theme-color" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link href="assets/css/button-styles.css" rel="stylesheet" type="text/css"/>
        <script src="../assets/plugins/3d-bold-navigation/js/modernizr.js"></script>
        <script src="../assets/plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>
        <script type="text/javascript" src="../assets/ckeditor/ckeditor.js"></script>

        
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
                        <li><a href="students.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon glyphicon-user"></span><p>Students</p></a></li>
                        <li class="active"><a href="classexamination.php"s class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-list-alt"></span><p>Examinations</p></a></li>
                        <li><a href="classresults.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-credit-card"></span><p>Exam Results</p></a></li>
                        <li><a href="notice.php" class="waves-effect waves-button"><span class="menu-icon glyphicon glyphicon-comment"></span><p>Notice</p></a></li>
                    </ul>
                </div>
            </div>
            <div class="page-inner">
                <div class="page-title">
                    <h3>Update Question (<?= $question_id ?>) For Exam - <?= $question["exam_name"]; ?></h3>
                </div>
                <div id="main-wrapper">
                    <div class="row">
                        <div class="col-md-12">
						<div class="row">
                            <div class="col-md-12">

                                <div class="panel panel-white">
                                    <div class="panel-body">
                                        <div role="tabpanel">
                                            <a href="view-questions.php?eid=<?php echo urlencode($question["exam_id"]); ?>" class="btn btn-default" style="margin-bottom: 1em">
                                                <i class="fa fa-arrow-left"></i> Back to Questions
                                            </a>

                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="<?= $question["question_type"] == "MC" ? "active" : "" ?>"><a href="#tab5" role="tab" data-toggle="tab" data-value="MC">Multiple Choice</a></li>
                                                <li role="presentation" class="<?= $question["question_type"] == "TF" ? "active" : "" ?>"><a href="#tab5" role="tab" data-toggle="tab" data-value="TF">Boolean (True/False)</a></li>
                                                <li role="presentation" class="<?= $question["question_type"] == "FB" ? "active" : "" ?>"><a href="#tab5" role="tab" data-toggle="tab" data-value="FB">Feedback</a></li>
                                            </ul>
                                    
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active fade in" id="tab5">
                                                <form action="pages/edit_question.php?type=<?= $question["question_type"] ?>" id="submit-form" method="POST">
                                                    <input type="hidden" name="question_id" value="<?= $question_id ?>">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Question</label>
                                                        <!--<input type="text" class="form-control" placeholder="Enter question" name="question" required autocomplete="off">-->
                                                        <textarea style="resize: both; overflow :auto;" rows="6" class="form-control" placeholder="Enter question" name="question" required autocomplete="off"><?= $question["question"] ?></textarea>
                                                        <script>
                                                            CKEDITOR.replace( 'question' );
                                                        </script>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><b style="font-size:20px;">Marks Alloted for this Question</b></label>
                                                        <input type="number" style="border: 2px solid #f25656;" class="form-control" placeholder="Enter marks for the above question" name="qmarks" required value="<?= $question["Qmarks"] ?>" autocomplete="off">
                                                    </div>
                                                    
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th width="100">Option No.</th>
                                                                <th>Option</th>
                                                                <th  width="100" >Answer</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row" >1</th>
                                                                <td>
                                                                <div class="form-group">
                                                                <label for="exampleInputEmail1">Option 1</label>
                                                                <!--<input type="text" class="form-control" placeholder="Enter option 1" name="opt1" required autocomplete="off">-->
                                                                <textarea style="resize: both; overflow :auto;" rows="2" class="form-control" placeholder="Enter option 1" name="opt1" autocomplete="off"><?= $question["option1"] ?></textarea> <!--Resize text box-->
                                                                </div>
                                                                </td>
                                                                <td><input type="radio" name="answer" value="option1" <?= $question["answer"] == "option1" || $question["answer"] == "True" ? "checked" : "" ?> required></td>
                                            
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">2</th>
                                                                <td>
                                                                <div class="form-group">
                                                                <label for="exampleInputEmail1">Option 2</label>
                                                                <!--<input type="text" class="form-control" placeholder="Enter option 2" name="opt2" required autocomplete="off">-->
                                                                <textarea style="resize: both; overflow :auto;" rows="2" class="form-control" placeholder="Enter option 2" name="opt2" autocomplete="off"><?= $question["option2"] ?></textarea> <!--Resize text box-->
                                                                </div>
                                                                </td>
                                                                <td><input type="radio" name="answer" value="option2" <?= $question["answer"] == "option2" || $question["answer"] == "False" ? "checked" : "" ?> required></td>
                                
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">3</th>
                                                                <td>
                                                                <div class="form-group">
                                                                <label for="exampleInputEmail1">Option 3</label>
                                                                <!--<input type="text" class="form-control" placeholder="Enter option 3" name="opt3" required autocomplete="off">-->
                                                                <textarea style="resize: both; overflow :auto;" rows="2" class="form-control" placeholder="Enter option 3" name="opt3" autocomplete="off"><?= $question["option3"] ?></textarea> <!--Resize text box-->
                                                                </div>
                                                                </td>
                                                                <td><input type="radio" name="answer" value="option3" <?= $question["answer"] == "option3" ? "checked" : "" ?> required></td>
                                                
                                                            </tr>
                                                            
                                                            <tr>
                                                                <th scope="row">4</th>
                                                                <td>
                                                                <div class="form-group">
                                                                <label for="exampleInputEmail1">Option 4</label>
                                                                <!--<input type="text" class="form-control" placeholder="Enter option 4" name="opt4" required autocomplete="off">-->
                                                                <textarea style="resize: both; overflow :auto;" rows="2" class="form-control" placeholder="Enter option 4" name="opt4" autocomplete="off"><?= $question["option4"] ?></textarea> <!--Resize text box-->
                                                                </div>
                                                                </td>
                                                                <td><input type="radio" name="answer" value="option4" <?= $question["answer"] == "option4" ? "checked" : "" ?> required></td>
                                                
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <input type="hidden" name="exam_id" value="<?= $question["exam_id"] ?>">
                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
                                            

                                            
                                                </form>
                                                       
                                                </div>
                                                <div role="tabpanel" class="tab-pane fade" id="tab6">
                                         
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

        <script>
            $(document).ready(function(){
                $("li[role=presentation]").click(function() {
                    let form = $("form");
                    let action = form.attr("action");
                    let typeValue = $(this).find("a").data("value"); // get data-value from li

                    // Use URL object for easier param handling
                    let url = new URL(action, window.location.origin);
                    url.searchParams.set("type", typeValue);

                    // Update the form action
                    form.attr("action", url.toString());
                });
            })
        </script>
        
    </body>

</html>