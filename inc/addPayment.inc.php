<?php
$today = date("Y-m-d");

$userId = (int)($_SESSION['userId'] ?? 0);
$groupId = (int)($_GET['groupId'] ?? 0);

$sqlMembers = "
    SELECT gm.groupMemberId, gm.isDeleted, u.userId, u.name, g.createdByUserId
    FROM `group_members` gm
    INNER JOIN `users` u ON gm.userId = u.userId
    INNER JOIN `groups` g ON gm.groupId = g.groupsId
    WHERE gm.groupId = :groupId AND gm.isDeleted = 0
    ORDER BY g.createdByUserId ASC
";
$stmtMembers = $pdo->prepare($sqlMembers);
$stmtMembers->bindValue(':groupId', $groupId, PDO::PARAM_INT);
$stmtMembers->execute();
$members = $stmtMembers->fetchAll(PDO::FETCH_ASSOC);

// Zoek de groepsmaker onder de leden
$groupCreatorId = null;
$groupCreatorName = null;
foreach ($members as $member) {
    if ($member['userId'] == $member['createdByUserId']) {
        $groupCreatorId = $member['groupMemberId'];
        $groupCreatorName = $member['name'];
        break;
    }
}
?>

<div class="form-container">

    <?php foreach (['error', 'success'] as $type): ?>
        <?php if (!empty($_SESSION[$type])): ?>
            <div class="<?= $type ?>-message">
                <?= htmlspecialchars($_SESSION[$type]); unset($_SESSION[$type]); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <form action="php/addPayment.php" method="post" enctype="multipart/form-data">

        <input type="hidden" name="groupId" value="<?= htmlspecialchars($groupId) ?>">

        <div class="form-grid">
            <div>
                <label for="amount">Bedrag</label>
                <div class="inline-select">
                    <input type="number" id="amount" name="amount" placeholder="0,00" step="0.01" min="0" required>
                    <span>EUR</span>
                </div>
            </div>

            <div>
                <label for="paidByUserId">Betaald door</label>
                <?php if ($groupCreatorId !== null): ?>
                    <select name="paidByUserId" id="paidByUserId" required>
                        <option value="<?= htmlspecialchars($groupCreatorId) ?>">
                            <?= htmlspecialchars($groupCreatorName) ?>
                            <?php if ($userId == $member['userId']): ?> (Jij)<?php endif; ?>
                        </option>
                    </select>
                    <input type="date" name="paymentDate" value="<?= $today ?>" required max="<?= $today ?>">
                <?php else: ?>
                    <p>Geen groepsmaker gevonden.</p>
                <?php endif; ?>
            </div>
        </div>

        <div >
            <label for="currencyId">Valuta</label>
            <select name="currencyId" id="currencyId" required>
                <?php
                $stmt = $pdo->query("SELECT * FROM currencies");
                while ($currency = $stmt->fetch()) {
                    echo '<option value="' . $currency['currencyId'] . '">' . htmlspecialchars($currency['currencyName']) . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="form-grid">
            <div>
                <label for="description">Omschrijving</label>
                <input type="text" id="description" name="description" placeholder="bijv. borreltje" required>
            </div>
            <div>
                <label>Voeg een foto toe</label>
                <label class="upload-box" for="fotoUpload" style="cursor:pointer;">📷 Klik om een foto te uploaden</label>
                <input type="file" id="fotoUpload" name="foto" accept="image/*" style="display:none;" required>
            </div>
        </div>

        <div>
            <label for="distributionMethod">Verdeel</label>
            <div class="inline-select" style="display:flex; align-items:center; gap:10px;">
                <select name="distributionMethod" id="distributionMethod" required>
                    <option value="shares">Aandelen</option>
                    <option value="exact">Exact bedrag</option>
                </select>
                <button type="button" id="everyoneOneShareBtn" style="display:none;">👥 Iedereen 1x</button>
            </div>
        </div>

        <div class="users-section" style="margin-top:10px;">
            <?php if (!empty($members)): ?>
                <?php foreach ($members as $member): ?>
                    <div class="user-row" style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                        <span class="label" style="flex:1;">
                            <?= htmlspecialchars($member['name']) ?>
                            <?php if ($member['userId'] === $userId): ?>
                                <small>Jij</small>
                            <?php endif; ?>
                        </span>
                        <input
                                type="number"
                                step="0.01"
                                min="0"
                                value=""
                                name="userShares[<?= htmlspecialchars($member['groupMemberId']) ?>]"
                                aria-label="Bedrag voor <?= htmlspecialchars($member['name']) ?>"
                                style="width:70px; text-align:right;"
                                class="user-share"
                                readonly
                        />
                        <span class="count" style="width:30px; text-align:center;">0x</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Geen leden om te verdelen.</p>
            <?php endif; ?>
        </div>

        <div class="controls" style="margin-top:15px;">
            <button type="button" class="save" onclick="location.href='index.php?page=groupchat&groupId=<?= htmlspecialchars($groupId) ?>'">Terug</button>
            <button type="submit" class="submit">Klaar</button>
        </div>
    </form>
</div>

<script>
    function updateCountDisplay(input, value) {
        const countSpan = input.parentElement.querySelector('.count');
        if (countSpan) countSpan.textContent = `${value}x`;
    }

    function toggleEveryoneButton() {
        const isShares = distributionMethodSelect.value === 'shares';
        everyoneBtn.style.display = isShares ? 'inline-block' : 'none';

        document.querySelectorAll('.user-share').forEach(input => {
            input.readOnly = isShares;  // readonly ipv disabled
        });
    }

    const distributionMethodSelect = document.getElementById('distributionMethod');
    const everyoneBtn = document.getElementById('everyoneOneShareBtn');

    distributionMethodSelect.addEventListener('change', toggleEveryoneButton);
    toggleEveryoneButton(); // initialisatie

    everyoneBtn.addEventListener('click', () => {
        const amountInput = document.getElementById('amount');
        const amount = parseFloat(amountInput.value);
        if (isNaN(amount) || amount <= 0) {
            alert('Vul eerst een geldig bedrag in!');
            return;
        }

        const members = document.querySelectorAll('.user-share');
        const perPerson = parseFloat((amount / members.length).toFixed(2));

        members.forEach(input => {
            input.value = perPerson;
            updateCountDisplay(input, 1);
        });
    });
</script>
