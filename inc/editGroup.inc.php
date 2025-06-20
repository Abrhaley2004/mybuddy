<?php
include '../Private/ConnectionMyBuddy/connection.php';



$loggedInUserId = $_SESSION['userId'] ?? null;
$currentDateTime = date('Y-m-d H:i:s');

$groupId = isset($_GET['groupId']) ? (int)$_GET['groupId'] : null;
$group = null;


if ($groupId && $loggedInUserId) {
    $stmt = $pdo->prepare("
        SELECT g.groupsId, g.name, g.image, g.totalAmount 
        FROM `groups` g 
        WHERE g.groupsId = :groupId AND g.createdByUserId = :userId
    ");
    $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
    $stmt->bindValue(':userId', $loggedInUserId, PDO::PARAM_INT);
    $stmt->execute();
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$group) {
    echo "<p>Groep niet gevonden of je hebt geen toegang.</p>";
    exit();
}
?>

    <div class="group-add-style">
        <h2>Groep bewerken</h2>
        <form enctype="multipart/form-data" method="post" action="php/editGroup.php">
            <input type="hidden" name="groupId" value="<?= htmlspecialchars($group['groupsId']) ?>">
            <input type="hidden" name="userId" value="<?= htmlspecialchars($loggedInUserId) ?>">


            <div class="form-group">
                <label for="name">Groeps naam</label>
                <input name="name" type="text" id="name" value="<?= htmlspecialchars($group['name']) ?>" required>
            </div>


            <div class="form-group">
                <label for="image">Selecteer een nieuwe foto (optioneel)</label><br>
                <?php if ($group['image']): ?>
                    <img class="group-photo"
                         src="<?= htmlspecialchars($group['image'] ? 'uploads/' . $group['image'] : 'img/mybuddy.png') ?>"
                         onerror="this.onerror=null;this.src='img/default.png';"/><br>
                <?php endif; ?>
                <input name="image" type="file" id="image" accept="image/*">
            </div>

            <!---->
            <!--        <div class="form-group">-->
            <!--            <label for="currencyId">Valuta</label>-->
            <!--            <select name="currencyId" id="currencyId" required>-->
            <!--                --><?php
            //                $stmt = $pdo->query("SELECT * FROM currencies");
            //                while ($currency = $stmt->fetch()) {
            //                    $selected = $currency['currencyId'] == $group['currencyId'] ? 'selected' : '';
            //                    echo '<option value="' . $currency['currencyId'] . '" ' . $selected . '>' . htmlspecialchars($currency['currencyName']) . '</option>';
            //                }
            //                ?>
            <!--            </select>-->
            <!--        </div>-->




            <input type="hidden" name="updatedAt" value="<?= $currentDateTime ?>">

            <button type="submit" class="add-group-button">Groep bijwerken</button>
        </form>
    </div>

<?php
?>