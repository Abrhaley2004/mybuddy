<?php
$groupId = $_GET['groupId'];
?>

<script type="text/javascript">
    setTimeout(function() {
        window.location.href = "index.php?page=groupchat&groupId=<?= $groupId; ?>";
    }, 1000);
</script>

<div class="success-container">
    <div class="success-icon">
        <span class="checkmark">&#10003;</span>
    </div>
    <div class="message">Betaling Geslaagd</div>
</div>