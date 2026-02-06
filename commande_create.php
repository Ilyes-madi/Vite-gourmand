<?php
require_once 'auth.php';
requireLogin();
require_once 'db.php';
require_once 'partials/header.php';

if (!isset($_GET['menu_id']) || !ctype_digit($_GET['menu_id'])) {
    echo "<p>Menu invalide.</p>";
    require_once 'partials/footer.php';
    exit;
}

$menuId = (int)$_GET['menu_id'];

$stmt = $pdo->prepare("SELECT id, title, min_people, base_price, stock FROM menus WHERE id = ?");
$stmt->execute([$menuId]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    echo "<p>Menu introuvable.</p>";
    require_once 'partials/footer.php';
    exit;
}

if ((int)$menu['stock'] <= 0) {
    echo "<p>Menu indisponible (stock = 0).</p>";
    require_once 'partials/footer.php';
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventDate = $_POST['event_date'] ?? '';
    $eventTime = $_POST['event_time'] ?? '';
    $eventAddress = trim($_POST['event_address'] ?? '');
    $peopleCount = (int)($_POST['people_count'] ?? 0);
    $kmOutside = (float)($_POST['km_outside_bordeaux'] ?? 0);

    if ($eventDate === '' || $eventTime === '' || $eventAddress === '') {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($peopleCount < (int)$menu['min_people']) {
        $error = "Minimum requis : " . (int)$menu['min_people'] . " personnes.";
    } elseif ($kmOutside < 0) {
        $error = "Distance invalide.";
    } else {
        $priceFood = $peopleCount * (float)$menu['base_price'];
        $priceDelivery = $kmOutside * 0.30;
        $priceTotal = $priceFood + $priceDelivery;

        $stmt = $pdo->prepare("
            INSERT INTO commandes
            (user_id, menu_id, event_date, event_time, event_address, km_outside_bordeaux,
             people_count, price_food, price_delivery, price_total, status_current)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'reçue')
        ");
        $stmt->execute([
            (int)$_SESSION['user_id'],
            $menuId,
            $eventDate,
            $eventTime,
            $eventAddress,
            $kmOutside,
            $peopleCount,
            $priceFood,
            $priceDelivery,
            $priceTotal
        ]);

        $pdo->prepare("UPDATE menus SET stock = stock - 1 WHERE id = ? AND stock > 0")->execute([$menuId]);

        header('Location: dashboard.php');
        exit;
    }
}
?>

<h2>Commander : <?= htmlspecialchars($menu['title'], ENT_QUOTES) ?></h2>

<div class="card">
    <p><strong>Prix par personne :</strong> <?= number_format((float)$menu['base_price'], 2, ',', ' ') ?> €</p>
    <p><strong>Minimum personnes :</strong> <?= (int)$menu['min_people'] ?></p>
    <p><strong>Stock :</strong> <?= (int)$menu['stock'] ?></p>

    <?php if ($error !== ''): ?>
        <p style="color:#dc2626;"><strong><?= htmlspecialchars($error, ENT_QUOTES) ?></strong></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-row">
            <div>
                <label for="event_date">Date</label><br>
                <input class="input" type="date" id="event_date" name="event_date" required>
            </div>
            <div>
                <label for="event_time">Heure</label><br>
                <input class="input" type="time" id="event_time" name="event_time" required>
            </div>
        </div>

        <br>

        <label for="event_address">Adresse</label><br>
        <input class="input" type="text" id="event_address" name="event_address" required>

        <br><br>

        <div class="form-row">
            <div>
                <label for="people_count">Nombre de personnes</label><br>
                <input class="input" type="number" id="people_count" name="people_count" min="<?= (int)$menu['min_people'] ?>" required>
            </div>
            <div>
                <label for="km_outside_bordeaux">Km hors Bordeaux</label><br>
                <input class="input" type="number" step="0.1" min="0" id="km_outside_bordeaux" name="km_outside_bordeaux" value="0">
            </div>
        </div>

        <br>

        <button class="btn btn-primary" type="submit">Valider la commande</button>
        <a class="btn" href="menu_show.php?id=<?= (int)$menuId ?>">Retour</a>
    </form>
</div>

<?php require_once 'partials/footer.php'; ?>