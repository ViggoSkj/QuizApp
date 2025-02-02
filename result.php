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

$stmt = $pdo->prepare("SELECT multipleTries FROM Quiz WHERE id = :quizId");
$stmt->execute(['quizId' => $quizId]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    die("Invalid quiz ID.");
}

$correctCount = 0;
$totalQuestions = 0;
$results = [];

$stmt = $pdo->prepare("SELECT q.id AS questionId, q.label AS question, o.label AS answer, o.correct FROM QuizAnswer a 
    JOIN QuizOption o ON a.questionId = o.id 
    JOIN QuizQuestion q ON o.questionId = q.id 
    WHERE a.userId = :userId AND q.quizId = :quizId");
$stmt->execute(['userId' => $userId, 'quizId' => $quizId]);
$answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($answers as $answer) {
    $totalQuestions++;
    if ($answer['correct']) {
        $correctCount++;
    }
    $results[] = $answer;
}

$score = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "head.php"; ?>
    <title>Quiz Results</title>
</head>
<body>
    <?php if (isset($_GET["alreadyAnswered"])) echo "already answerd"; ?>
    <h2>Quiz Results</h2>
    <p>You got <?php echo $correctCount; ?> out of <?php echo $totalQuestions; ?> correct.</p>
    <p>Your score: <?php echo $score; ?>%</p>
    
    <h3>Question Breakdown:</h3>
    <ul>
        <?php foreach ($results as $result): ?>
            <li>
                <strong><?php echo htmlspecialchars($result['question']); ?></strong><br>
                Your Answer: <?php echo htmlspecialchars($result['answer']); ?> - 
                <?php echo $result['correct'] ? 'Correct' : 'Incorrect'; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <a href="index.php">Back to Home</a>
</body>
</html>