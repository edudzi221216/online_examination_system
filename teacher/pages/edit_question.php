<!--Examination > Action > Add Question -->
<?php
include '../../database/config.php';
include '../../includes/uniques.php';

$type = $_GET["type"] ?? "mc";
$examid = $_POST['exam_id'];
$question_id = $_POST["question_id"];
$question = $_POST['question'];
$qmarks = $_POST['qmarks'];
$answer = $_POST['answer'];

$opt1 = $opt2 = $opt3 = $opt4 = '-';

if(strtolower($type) == "mc" || strtolower($type) == "tf"){
    $opt1 = $_POST['opt1'];
    $opt2 = $_POST['opt2'];
    
    if(strtolower($type) == "mc"){
        $opt3 = $_POST['opt3'];
        $opt4 = $_POST['opt4'];
    }
}

if(!in_array(strtolower($type), ['mc', 'tf', 'fb'])){
    echo "<script>
    alert('Invalid question type.');
    window.location.href='".$_SERVER['HTTP_REFERER']."';
    </script>";	
    
    exit;
}

$stmt = $conn->prepare("UPDATE tbl_questions SET question = ?, Qmarks = ?, option1 = ?, option2 = ?, option3 = ?, option4 = ?, answer = ? WHERE question_id = ?");
$stmt->bind_param("ssssssss", $question, $qmarks, $opt1, $opt2, $opt3, $opt4, $answer, $question_id);

if ($stmt->execute()) {
    echo "<script>
    alert('Question updated successfully.');
    window.location.href='../view-questions.php?eid=$examid';
    </script>";
} else {
    echo "<script>
    alert('Could Not Update Question.');
    window.location.href='".$_SERVER['HTTP_REFERER']."';
    </script>";
}

$stmt->close();


?>