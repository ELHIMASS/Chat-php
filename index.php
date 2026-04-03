<?php
// Configuration de la connexion à la base de données
$dsn = 'mysql:host='.getenv("MARIADB_HOST").';dbname='.getenv("MARIADB_DATABASE").'';
$username = getenv("MARIADB_USER");
$password = getenv("MARIADB_PASSWORD");

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Échec de la connexion : ' . $e->getMessage();
}

// Envoi d'un message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username']) && !empty($_POST['message'])) {
    $username = htmlspecialchars($_POST['username']);
    $message = htmlspecialchars($_POST['message']);

    $stmt = $pdo->prepare('INSERT INTO messages (username, message) VALUES (:username, :message)');
    $stmt->execute(['username' => $username, 'message' => $message]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Chat</h1>
        <form action="index.php" method="post" class="mb-4">
            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="message">Message :</label>
                <textarea id="message" name="message" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
        <div id="messages">
            <?php
            $stmt = $pdo->query('SELECT username, message, created_at FROM messages ORDER BY created_at DESC');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="alert alert-secondary" role="alert">';
                echo '<strong>' . htmlspecialchars($row['username']) . '</strong> : ';
                echo htmlspecialchars($row['message']);
                echo '<br><small>' . $row['created_at'] . '</small>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>