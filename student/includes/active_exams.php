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
            $categorized_exams = categorizeExamsForStudent($myclass, $conn, $_SESSION["myid"]);
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
            }elseif(isset($dashboard)){
                echo <<<HTML
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="alert alert-info" role="alert">
                                <strong>No active exams available at the moment.</strong> Please check back later.
                            </div>
                        </td>
                </tbody>
                HTML;
            }

            // Display Pending Exams with Countdown
            if (count($pending_exams) > 0 && !isset($dashboard)) {
                echo generateExamTable($pending_exams, 'pending', true);
            }

            // Display Past Exams
            if (count($past_exams) > 0 && !isset($dashboard)) {
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