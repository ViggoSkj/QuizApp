<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userId'])) {
    die("You must be logged in to view quiz results.");
}

if (!isset($_GET['quizId'])) {
    die("Invalid request.");
}

$userId = $_SESSION['userId'];
$quizId = $_GET['quizId'];

$stmt = $pdo->prepare("SELECT * FROM Quiz WHERE id = :quizId");
$stmt->execute(['quizId' => $quizId]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if ($quiz["userId"] != $userId) {
    die("You must own the quiz to view results");
}

if (!$quiz) {
    die("Invalid quiz ID.");
}

$stmt = $pdo->prepare("SELECT * FROM QuizQuestion WHERE quizId = :quizId");
$stmt->execute(['quizId' => $quizId]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($questions as &$q) {
    $stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN o.correct = 1 THEN 1 ELSE 0 END) AS correctCount,
        SUM(CASE WHEN o.correct = 0 THEN 1 ELSE 0 END) AS incorrectCount
    FROM QuizAnswer a
    JOIN QuizOption o ON a.optionId = o.id
    JOIN QuizQuestion q ON o.questionId = q.id
    WHERE q.id = :questionId;
    ");
    $stmt->execute(['questionId' => $q["id"]]);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($results);
    $q["correct"] = $results["correctCount"];
    $q["incorrect"] = $results["incorrectCount"];
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php"; ?>
    <title>Document</title>
</head>

<body>
    <?php foreach ($questions as $question) { ?>
        <p><?php echo $question["label"]; ?></p>
        <p><?php echo $question["correct"] * 100/($question["correct"] + $question["incorrect"]); ?>%</p>
    <?php } ?>
</body>

</html>