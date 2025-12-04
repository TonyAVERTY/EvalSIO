<?php
$dsn = "mysql:host=localhost;dbname=TonyBDD;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "Ltony4425!";
$pdo = null;
$formResponse = ["success" => false, "message" => ""];
$showServerMessage = false;

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            prenom VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    );
} catch (PDOException $e) {
    $pdo = null;
}

$isAjax =
    strtolower($_SERVER["HTTP_X_REQUESTED_WITH"] ?? "") === "xmlhttprequest";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if (!$pdo) {
        $formResponse["message"] =
            "Connexion à la base de données indisponible.";
    } elseif ($firstName === "" || $email === "") {
        $formResponse["message"] = "Merci de renseigner tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formResponse["message"] = "Adresse mail invalide.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (prenom, email) VALUES (:prenom, :email)",
            );
            $stmt->execute([
                ":prenom" => $firstName,
                ":email" => $email,
            ]);
            $formResponse = [
                "success" => true,
                "message" => "Votre demande est bien envoyée !",
            ];
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? "") === "23000") {
                $formResponse["message"] =
                    "Cette adresse mail est déjà enregistrée.";
            } else {
                $formResponse["message"] =
                    "Erreur lors de la sauvegarde. Merci de reessayer.";
            }
        }
    }

    if ($isAjax) {
        header("Content-Type: application/json");
        echo json_encode($formResponse);
        exit();
    }

    $showServerMessage = $formResponse["message"] !== "";
}

$footerName = "notre equipe";
if ($pdo) {
    $stmt = $pdo->query("SELECT prenom FROM users ORDER BY id ASC LIMIT 1");
    $firstUserName = $stmt->fetchColumn();
    if ($firstUserName) {
        $footerName = $firstUserName;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stud2Jobs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="site-nav" aria-label="Navigation principale">
            <ul>
                <li><a href="#section1">Accueil</a></li>
                <li><a href="#section2">Contactez-moi</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="section1">
            <img src="https://placehold.co/300x200" alt="Illustration section 1">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vel mauris fermentum, tristique augue eget, luctus enim.</p>
            <p>Praesent venenatis, ligula id cursus ultricies, leo ipsum vulputate mi, non pulvinar quam arcu non libero.</p>
        </section>

        <section id="section2">
            <form id="contact-form" action="<?php echo htmlspecialchars(
                $_SERVER["PHP_SELF"] ?? "",
                ENT_QUOTES,
                "UTF-8",
            ); ?>" method="post">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Adresse mail</label>
                <input type="email" id="email" name="email" required>

                <button type="submit">Valider</button>
            </form>
            <p id="form-message" aria-live="polite" <?php echo $showServerMessage
                ? ""
                : "hidden"; ?>>
                <?php
                $messageText = $showServerMessage
                    ? $formResponse["message"]
                    : "Votre demande est bien envoyée !";
                echo htmlspecialchars($messageText, ENT_QUOTES, "UTF-8");
                ?>
            </p>
        </section>
    </main>

    <footer>
        <p>Page web faite par <?php echo htmlspecialchars(
            $footerName,
            ENT_QUOTES,
            "UTF-8",
        ); ?> venant de la base de données PHP.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("contact-form");
            const message = document.getElementById("form-message");
            if (!form || !message) {
                return;
            }

            const defaultMessage = message.textContent.trim() || "Votre demande est bien envoyée !";

            form.addEventListener("submit", function (event) {
                event.preventDefault();
                message.hidden = true;

                const formData = new FormData(form);
                fetch(form.getAttribute("action") || window.location.href, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error("Network response was not ok");
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        const text = typeof data.message === "string" && data.message.trim() !== ""
                            ? data.message
                            : defaultMessage;
                        message.textContent = text;
                        message.hidden = false;
                        if (data.success) {
                            form.reset();
                        }
                    })
                    .catch(function () {
                        message.textContent = "Une erreur est survenue. Merci de reessayer.";
                        message.hidden = false;
                    });
            });
        });
    </script>
</body>
</html>
