<?php
$dossierDistant = '../../dist/';

if (!file_exists($dossierDistant)) {
    mkdir($dossierDistant, 0777, true);
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fichierServeur = $dossierDistant . basename($_FILES['image']['name']);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $fichierServeur)) {
        $origin = rtrim($_SERVER['HTTP_ORIGIN'], '/') . '/';
        $urlImage = $origin . ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', $fichierServeur), '/');
        echo $urlImage;
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Erreur lors du téléversement de l\'image.';
    }
} else {
    // Erreur lors du téléversement du fichier
    header('HTTP/1.1 400 Bad Request');
    echo 'Erreur lors du téléversement de l\'image.';
}
?>
