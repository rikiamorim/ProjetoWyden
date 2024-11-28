<?php

$db = new PDO('sqlite:database.sqlite'); 

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $nome_anime = $_POST['nome-anime'];
    $genero = $_POST['genero'];
    $resumo = $_POST['resumo'];
    $nota = $_POST['nota'];

    $query = "INSERT INTO animes (nome, nome_anime, genero, resumo, nota) VALUES (:nome, :nome_anime, :genero, :resumo, :nota)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':nome_anime', $nome_anime);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':resumo', $resumo);
    $stmt->bindParam(':nota', $nota, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Erro ao inserir os dados.";
    }
}
?>
