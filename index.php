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
            <p>Premier paragraphe pour présenter brièvement le contenu de la section 1.</p>
            <p>Deuxième paragraphe pour ajouter davantage de précisions sur cette section.</p>
        </section>

        <section id="section2">
            <h2>Contactez-moi</h2>
            <form action="#" method="post">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Adresse mail</label>
                <input type="email" id="email" name="email" required>

                <button type="submit" style="background-color:blue;">Valider</button>
            </form>
        </section>
    </main>
</body>
</html>
