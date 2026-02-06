<?php
require_once 'auth.php';
requireLogin();
require_once 'db.php';
require_once 'partials/header.php';

$userId = (int)($_SESSION['user_id'] ?? 0);

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo "<p>Commande invalide.</p>";
    require_once 'partials/footer.php';
    exit;
}

$commandeId = (int)$_GET['id'];

$sql = "
    SELECT
        c.id,
        c.user_id,
        c.menu_id,
        c.event_date,
        c.event_time,
        c.event_address,
        c.km_outside_bordeaux,
        c.people_count,
        c.status_current,
        m.title AS menu_title,
        m.min_people,
        m.base_price
    FROM commandes c
    JOIN menus m ON m.id = c.menu_id
    WHERE c.id = ?
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$commandeId]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande || (int)$commande['user_id'] !== $userId) {
    echo "<p>Accès refusé.</p>";
    require_once 'partials/footer.php';
    exit;
}

if ((string)$commande['status_current'] !== 'reçue') {
    echo "<p>Commande non modifiable (statut : " . htmlspecialchars((string)$commande['status_current'], ENT_QUOTES) . ").</p>";
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
    } elseif ($peopleCount < (int)$commande['min_people']) {
        $error = "Minimum requis : " . (int)$commande['min_people'] . " personnes.";
    } elseif ($kmOutside < 0) {
        $error = "Distance invalide.";
    } else {
        $priceFood = $peopleCount * (float)$commande['base_price'];
        $priceDelivery = $kmOutside * 0.30;
        $priceTotal = $priceFood + $priceDelivery;

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            UPDATE commandes
            SET event_date = :event_date,
                event_time = :event_time,
                event_address = :event_address,
                km_outside_bordeaux = :km_outside_bordeaux,
                people_count = :people_count,
                price_food = :price_food,
                price_delivery = :price_delivery,
                price_total = :price_total
            WHERE id = :id AND user_id = :user_id AND status_current = 'reçue'
        ");
        $stmt->execute([
            'event_date' => $eventDate,
            'event_time' => $eventTime,
            'event_address' => $eventAddress,
            'km_outside_bordeaux' => $kmOutside,
            'people_count' => $peopleCount,
            'price_food' => $priceFood,
            'price_delivery' => $priceDelivery,
            'price_total' => $priceTotal,
            'id' => $commandeId,
            'user_id' => $userId
        ]);

        $stmt = $pdo->prepare("
            INSERT INTO commande_historiques (commande_id, status, changed_by, cancel_reason, changed_at)
            VALUES (:commande_id, :status, :changed_by, NULL, NOW())
        ");
        $stmt->execute([
            'commande_id' => $commandeId,
            'status' => 'modifiée',
            'changed_by' => $userId
        ]);

        $pdo->commit();

        header('Location: dashboard.php');
        exit;
    }
}
?>

<main class="container">
    <h2>Modifier ma commande</h2>

    <p><strong>Menu :</strong> <?= htmlspecialchars((string)$commande['menu_title'], ENT_QUOTES) ?></p>
    <p><strong>Minimum personnes :</strong> <?= (int)$commande['min_people'] ?></p>
    <p><strong>Prix par personne :</strong> <?= number_format((float)$commande['base_price'], 2, ',', ' ') ?> €</p>

    <?php if ($error !== ''): ?>
        <p style="color:#dc2626;"><strong><?= htmlspecialchars($error, ENT_QUOTES) ?></strong></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-row">
            <div>
                <label for="event_date">Date</label><br>
                <input class="input" type="date" id="event_date" name="event_date" required value="<?= htmlspecialchars((string)$commande['event_date'], ENT_QUOTES) ?>">
            </div>
            <div>
                <label for="event_time">Heure</label><br>
                <input class="input" type="time" id="event_time" name="event_time" required value="<?= htmlspecialchars((string)$commande['event_time'], ENT_QUOTES) ?>">
            </div>
        </div>

        <br>

        <label for="event_address">Adresse</label><br>
        <input class="input" type="text" id="event_address" name="event_address" required value="<?= htmlspecialchars((string)$commande['event_address'], ENT_QUOTES) ?>">

        <br><br>

        <div class="form-row">
            <div>
                <label for="people_count">Nombre de personnes</label><br>
                <input class="input" type="number" id="people_count" name="people_count" min="<?= (int)$commande['min_people'] ?>" required value="<?= (int)$commande['people_count'] ?>">
            </div>
            <div>
                <label for="km_outside_bordeaux">Km hors Bordeaux</label><br>
                <input class="input" type="number" step="0.1" min="0" id="km_outside_bordeaux" name="km_outside_bordeaux" value="<?= htmlspecialchars((string)$commande['km_outside_bordeaux'], ENT_QUOTES) ?>">
            </div>
        </div>

        <br>

        <button type="submit">Enregistrer</button>
        <a href="dashboard.php">Retour</a>
    </form>
</main>

<?php require_once 'partials/footer.php'; ?>