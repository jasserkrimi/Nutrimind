import pathlib
p = pathlib.Path(r'c:\xampp\htdocs\Nutrimind\views\backoffice\index.php')
text = p.read_text(encoding='utf-8').replace('\r\n', '\n')
old = '''<<<<<<< HEAD
// Include controllers
require_once '../../controllers/MealController.php';
require_once '../../controllers/IngredientController.php';

// Initialize controllers
$mealController = new MealController();
$ingredientController = new IngredientController();

// Get data
$meals = $mealController->getAll();
$ingredients = $ingredientController->getAll();

// Handle delete operations
if (isset($_GET['delete_meal'])) {
    $deleteId = htmlspecialchars($_GET['delete_meal']);
    if ($mealController->delete($deleteId)) {
        $_SESSION['success_message'] = "Repas supprimé avec succès!";
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du repas!";
    }
}

if (isset($_GET['delete_ingredient'])) {
    $deleteId = htmlspecialchars($_GET['delete_ingredient']);
    if ($ingredientController->delete($deleteId)) {
        $_SESSION['success_message'] = "Ingrédient supprimé avec succès!";
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de l'ingrédient!";
    }
}
=======
// Check for new objectives
$newObjectives = [];
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../controllers/UserController.php';
    require_once __DIR__ . '/../../models/Objectif.php';

    $userController = new UserController();
    if (empty($_SESSION['last_login'])) {
        $profile = $userController->getProfile();
        $_SESSION['last_login'] = $profile['last_login'] ?? null;
    }

    if (!empty($_SESSION['last_login'])) {
        $objectifModel = new Objectif();
        $newObjectives = $objectifModel->getAddedAfter($_SESSION['last_login']);
    }
>>>>>>> planning'''
new = '''// Include controllers
require_once '../../controllers/MealController.php';
require_once '../../controllers/IngredientController.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/Objectif.php';

// Initialize controllers
$mealController = new MealController();
$ingredientController = new IngredientController();
$userController = new UserController();

// Get data
$meals = $mealController->getAll();
$ingredients = $ingredientController->getAll();

// Check for new objectives
$newObjectives = [];
if (isset($_SESSION['user_id'])) {
    if (empty($_SESSION['last_login'])) {
        $profile = $userController->getProfile();
        $_SESSION['last_login'] = $profile['last_login'] ?? null;
    }

    if (!empty($_SESSION['last_login'])) {
        $objectifModel = new Objectif();
        $newObjectives = $objectifModel->getAddedAfter($_SESSION['last_login']);
    }
}

// Handle delete operations
if (isset($_GET['delete_meal'])) {
    $deleteId = htmlspecialchars($_GET['delete_meal']);
    if ($mealController->delete($deleteId)) {
        $_SESSION['success_message'] = "Repas supprimé avec succès!";
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du repas!";
    }
}

if (isset($_GET['delete_ingredient'])) {
    $deleteId = htmlspecialchars($_GET['delete_ingredient']);
    if ($ingredientController->delete($deleteId)) {
        $_SESSION['success_message'] = "Ingrédient supprimé avec succès!";
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de l'ingrédient!";
    }
}
?>'''
if old not in text:
    raise RuntimeError('Conflict block not found')
text = text.replace(old, new, 1)
p.write_text(text.replace('\n', '\r\n'), encoding='utf-8')
print('fixed')
