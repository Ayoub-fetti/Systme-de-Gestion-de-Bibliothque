<?php
define('BASE_URL', 'http://localhost/votre-projet');
require_once '../classes/Book.php';
require_once '../connection.php';
require_once 'check_admin.php';

// Vérification de la session admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$book = new Book("", "", 0, "", "", "");
$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}


// Get all books
$books = $book->getAllBooks();


// Handle Delete
if (isset($_POST['delete']) && isset($_POST['book_id'])) {
    $book->setId($_POST['book_id']);
    if ($book->deleteBook()) {
        $message = "Livre supprimé avec succès";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Livres</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
            <!-- Sidebar -->
            <div class="w-64 bg-blue-900 text-white min-h-screen">
                <div class="p-4 flex items-center">
                    <span class="ml-3 text-xl font-semibold">
                        <span class="text-blue-200">Admin</span> Dashboard
                    </span>
                </div>
                <nav class="mt-5">
                    <div class="mt-5">
                        <a class="flex items-center p-3 hover:bg-blue-800 rounded-lg" href="admin_dashboard.php">
                            <i class="fas fa-home"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </div>
                    <div class="mt-5">
                        <a class="flex items-center p-3 bg-blue-800 rounded-lg" href="admin_books.php">
                            <i class="fas fa-book"></i>
                            <span class="ml-3">Gestion des Livres</span>
                        </a>
                    </div>
                    <div class="mt-5">
                        <a class="flex items-center p-3 hover:bg-blue-800 rounded-lg" href="admin_categories.php">
                            <i class="fas fa-filter"></i>
                            <span class="ml-3">Gestion des categories</span>
                        </a>
                    </div>
                    <div class="mt-5">
                        <a class="flex items-center p-3 hover:bg-blue-800 rounded-lg" href="admin_rapport.php">
                            <i class="fas fa-file-pdf"></i>
                            <span class="ml-3">statistiques et rapports</span>
                        </a>
                    </div>
                    <div class="mt-5">
                        <a href="../logout.php" class="flex items-center p-3 hover:bg-blue-800 rounded-lg text-red-500 hover:text-red-400">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="ml-3">Déconnexion</span>
                        </a>
                    </div>
                </nav>
            </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="container">
                <h2 class="text-2xl font-semibold mb-4">Gestion des Livres</h2>
                
                <?php if ($message): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <a href="add_book.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Ajouter un livre
                </a>

                <div class="bg-white shadow-md rounded my-6">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID</th>
                                <th class="py-3 px-6 text-left">Titre</th>
                                <th class="py-3 px-6 text-left">Auteur</th>
                                <th class="py-3 px-6 text-left">Catégorie</th>
                                <th class="py-3 px-6 text-left">Status</th>
                                <th class="py-3 px-6 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            <?php foreach ($books as $book): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6"><?php echo htmlspecialchars($book['id']); ?></td>
                                <td class="py-3 px-6"><?php echo htmlspecialchars($book['title']); ?></td>
                                <td class="py-3 px-6"><?php echo htmlspecialchars($book['author']); ?></td>
                                <td class="py-3 px-6"><?php echo htmlspecialchars($book['category_id']); ?></td>
                                <td class="py-3 px-6"><?php echo htmlspecialchars($book['status']); ?></td>
                                <td class="py-3 px-6">
                                    <div class="flex space-x-2">
                                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" 
                                           class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded">
                                            Modifier
                                        </a>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                            <button type="submit" name="delete" 
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 