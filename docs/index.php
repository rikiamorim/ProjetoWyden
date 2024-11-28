<?php

try {
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    exit;
}

$query = "SELECT * FROM animes";

if (isset($_GET['genre']) && count($_GET['genre']) > 0) {
    $genres = $_GET['genre'];
    $placeholders = implode(',', array_fill(0, count($genres), '?'));
    $query .= " WHERE genero IN ($placeholders)";
}

$stmt = $db->prepare($query);

if (isset($_GET['genre']) && count($_GET['genre']) > 0) {
    $stmt->execute($genres);
} else {
    $stmt->execute();
}

$animes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universidade dos Animes</title>
    <link rel="icon" href="UA logo.jpg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div id="header-container"></div>

    <main>
        <div class="container">
            <aside class="sidebar">
                <h2>Filtros de Gênero</h2>
                <form method="GET">
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="acao" <?= isset($_GET['genre']) && in_array('acao', $_GET['genre']) ? 'checked' : ''; ?>> Ação
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="aventura" <?= isset($_GET['genre']) && in_array('aventura', $_GET['genre']) ? 'checked' : ''; ?>> Aventura
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="comedia" <?= isset($_GET['genre']) && in_array('comedia', $_GET['genre']) ? 'checked' : ''; ?>> Comédia
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="drama" <?= isset($_GET['genre']) && in_array('drama', $_GET['genre']) ? 'checked' : ''; ?>> Drama
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="fantasia" <?= isset($_GET['genre']) && in_array('fantasia', $_GET['genre']) ? 'checked' : ''; ?>> Fantasia
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="romance" <?= isset($_GET['genre']) && in_array('romance', $_GET['genre']) ? 'checked' : ''; ?>> Romance
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="sci-fi" <?= isset($_GET['genre']) && in_array('sci-fi', $_GET['genre']) ? 'checked' : ''; ?>> Sci-Fi
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="shonen" <?= isset($_GET['genre']) && in_array('shonen', $_GET['genre']) ? 'checked' : ''; ?>> Shonen
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="suspense" <?= isset($_GET['genre']) && in_array('suspense', $_GET['genre']) ? 'checked' : ''; ?>> Suspense
                    </label>
                    <label class="filter-label">
                        <input type="checkbox" name="genre[]" value="terror" <?= isset($_GET['genre']) && in_array('terror', $_GET['genre']) ? 'checked' : ''; ?>> Terror
                    </label>
                    <button type="submit" class="submit-button">Aplicar Filtros</button>
                </form>
                <a href="formulario.html" class="submit-button">Submeter Resumo</a>
            </aside>

            <section class="content">
                <section class="intro">
                    <h2>Bem-vindo ao UniAni</h2>
                    <p>Encontre os melhores resumos de animes feitos por fãs em um só lugar!</p>
                </section>

                <section>
                    <h2>Em Alta</h2>
                    <div class="anime-list">
                    <?php
if (count($animes) > 0) {
    foreach ($animes as $anime) {
        $anime_name = urlencode($anime['nome_anime']);
        $api_url = "https://api.jikan.moe/v4/anime?q=$anime_name";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        $image_url = null;
        
        if (isset($data['data'][0]['images']['jpg']['large_image_url'])) {
            $image_url = $data['data'][0]['images']['jpg']['large_image_url'];
        }

        $mal_url = "https://myanimelist.net/anime/" . $data['data'][0]['mal_id'];

        echo '<div class="anime-item">';
        echo '<h3><a href="' . $mal_url . '" target="_blank">' . htmlspecialchars($anime['nome_anime']) . '</a></h3>';
        if ($image_url) {
            echo '<img src="' . $image_url . '" alt="' . htmlspecialchars($anime['nome_anime']) . '" style="height: 200px; width: auto;" class="anime-image">';
        }
        echo '<p>' . htmlspecialchars($anime['resumo']) . '</p>';
        echo '<p><strong>Gênero:</strong> ' . htmlspecialchars($anime['genero']) . '</p>';
        echo '<p><strong>Nota:</strong> ' . htmlspecialchars($anime['nota']) . '/10</p>';
        echo '</div>';
    }
} else {
    echo '<p>Nenhum anime encontrado com os filtros selecionados.</p>';
}
?>
                    </div>
                </section>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 UniAni. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        fetch('header.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById('header-container').innerHTML = data;
            })
            .catch(error => {
                console.error('Erro ao carregar o header:', error);
            });
    </script>
</body>
</html>
