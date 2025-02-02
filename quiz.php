<?php
session_start();
require 'db.php';

if (!isset($_GET['quizId'])) {
    die("Quiz ID not provided.");
}

$quizId = $_GET['quizId'];

$stmt = $pdo->prepare("SELECT * FROM Quiz WHERE id = :quizId");
$stmt->execute(['quizId' => $quizId]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    die("Invalid quiz ID.");
}

$stmt = $pdo->prepare("SELECT * FROM QuizQuestion WHERE quizId = :quizId");
$stmt->execute(['quizId' => $quizId]);
$quizQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($quizQuestions as &$question) {
    $stmt = $pdo->prepare("SELECT * FROM QuizOption WHERE questionId = :questionId");
    $stmt->execute(['questionId' => $question['id']]);
    $question['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "head.php"; ?>
    <title><?php echo htmlspecialchars($quiz['name']); ?></title>
</head>
<body>
    <h2><?php echo htmlspecialchars($quiz['name']); ?></h2>
    <form action="submit.php" method="post">
        <input type="hidden" name="quizId" value="<?php echo $quizId; ?>">
        <?php foreach ($quizQuestions as $q): ?>
            <fieldset>
                <legend><?php echo htmlspecialchars($q['label']); ?></legend>
                <?php foreach ($q['options'] as $option): ?>
                    <label>
                        <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="<?php echo $option['id']; ?>" required>
                        <?php echo htmlspecialchars($option['label']); ?>
                    </label><br>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
