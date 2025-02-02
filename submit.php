<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userId'])) {
    die("You must be logged in to submit a quiz.");
}

if (!isset($_POST['quizId'], $_POST['answers']) || !is_array($_POST['answers'])) {
    die("Invalid submission.");
}

$userId = $_SESSION['userId'];
$quizId = $_POST['quizId'];
$answers = $_POST['answers'];

$stmt = $pdo->prepare("SELECT multipleTries FROM Quiz WHERE id = :quizId");
$stmt->execute(['quizId' => $quizId]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    die("Invalid quiz ID.");
}

if (!$quiz['multipleTries']) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM QuizAnswer WHERE userId = :userId AND questionId IN (SELECT id FROM QuizOption WHERE questionId IN (SELECT id FROM QuizQuestion WHERE quizId = :quizId))");
    $stmt->execute(['userId' => $userId, 'quizId' => $quizId]);
    $previousAttempts = $stmt->fetchColumn();

    if ($previousAttempts > 0) {
        header("Location: result.php?quizId=".$quizId."&alreadyAnswered=1");
        die();
    }
}

$correctCount = 0;
$totalQuestions = count($answers);

foreach ($answers as $questionId => $selectedOptionId) {
    $stmt = $pdo->prepare("SELECT correct FROM QuizOption WHERE id = :optionId AND questionId = :questionId");
    $stmt->execute(['optionId' => $selectedOptionId, 'questionId' => $questionId]);
    $option = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$option) {
        continue; 
    }
    
    if ($option['correct']) {
        $correctCount++;
    }
    
    $stmt = $pdo->prepare("INSERT INTO QuizAnswer (questionId, userId) VALUES (:questionId, :userId)");
    $stmt->execute(['questionId' => $selectedOptionId, 'userId' => $userId]);
}

$score = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

header("Location: result.php?quizId=".$quizId);