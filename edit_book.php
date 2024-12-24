<?php
include 'Book.php';

$message = '';
$book = new Book("", "", 0, "", "", "");

// Récupérer le livre à modifier
if (isset($_GET['id'])) {
    $bookData = $book->getBookById($_GET['id']);
    if ($bookData) {
        $book = new Book(
            $bookData['title'],
            $bookData['author'],
            $bookData['category_id'],
            $bookData['cover_image'],
            $bookData['summary'],
            $bookData['status']
        );
        $book->setId($_GET['id']);
    }
}

// Récupérer les catégories
$database = new Database;
$conn = $database->connect();
$categories = [];

if ($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

if (isset($_POST['submit'])) {
    // Mettre à jour les données du livre
    $book = new Book(
        $_POST['title'],
        $_POST['author'],
        $_POST['category_id'],
        $bookData['cover_image'],
        $_POST['summary'],
        $_POST['status']
    );
    $book->setId($_GET['id']);

    // Gestion de la nouvelle image si fournie
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['cover_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            if (!empty($bookData['cover_image']) && file_exists($bookData['cover_image'])) {
                unlink($bookData['cover_image']);
            }
            
            $cover_image = 'covers/' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image);
            $book->setCoverImage($cover_image);
        }
    }

    // Sauvegarder les modifications
    if ($book->updateBook()) {
        $message = "Le livre a été modifié avec succès!";
        // Rediriger vers la page admin avec le message
        header('Location: admin_books.php?message=' . urlencode($message));
        exit();
    } else {
        $message = "Erreur lors de la modification du livre.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un Livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Modifier un Livre</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Titre</label>
                <input type="text" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($bookData['title'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Auteur</label>
                <input type="text" name="author" class="form-control" 
                       value="<?php echo htmlspecialchars($bookData['author'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Catégorie</label>
                <select name="category_id" class="form-control" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($bookData['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Image de couverture</label>
                <?php if (!empty($bookData['cover_image'])): ?>
                    <img src="<?php echo htmlspecialchars($bookData['cover_image']); ?>" 
                         style="max-width: 200px;" class="d-block mb-2">
                <?php endif; ?>
                <input type="file" name="cover_image" class="form-control">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Résumé</label>
                <textarea name="summary" class="form-control" rows="3"><?php echo htmlspecialchars($bookData['summary'] ?? ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="available" <?php echo ($bookData['status'] == 'available') ? 'selected' : ''; ?>>Disponible</option>
                    <option value="borrowed" <?php echo ($bookData['status'] == 'borrowed') ? 'selected' : ''; ?>>Emprunté</option>
                </select>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Modifier</button>
            <a href="admin_books.php" class="btn btn-secondary">Retour</a>
        </form>
    </div>
</body>
</html>