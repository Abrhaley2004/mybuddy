<?php
include '../Private/ConnectionMyBuddy/connection.php';

$loggedInUserId = $_SESSION['userId'] ?? null;
$currentDateTime = date('Y-m-d H:i:s');
?>

<div class="group-add-style">
    <h2>Groep toevoegen</h2>
    <form enctype="multipart/form-data" method="post" action="php/addGroup.php">
        <input type="hidden" name="userId" value="<?php echo $_SESSION['userId']; ?>">


        <div class="form-group">
            <label for="name">Groeps naam</label>
            <input name="name" type="text" id="name" placeholder="Groeps naam" required>
        </div>


        <div class="form-group-option">
            <label for="groupmembers">Groeps leden</label>
            <div class="custom-multiselect">
                <input type="text" id="memberSearch" placeholder="Klik om leden te kiezen..." autocomplete="off">
                <div id="memberOptions" class="options-list">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM users where userid != '$loggedInUserId' ");
                    while ($user = $stmt->fetch()) {
                        echo '<div class="option" data-id="' . $user['userId'] . '">' . htmlspecialchars($user['name']) . '</div>';
                    }
                    ?>
                </div>
                <div id="selectedMembers"></div>
            </div>

            <div id="hiddenInputs"></div>
        </div>



        <div class="form-group">
            <label for="image">Selecteer een foto</label>
            <input name="image" type="file" id="image" accept="image/*" required>
        </div>



        <!--        <div class="form-group">-->
        <!--            <label for="totalAmount">Totaalbedrag</label>-->
        <!--            <input name="totalAmount" type="number" step="0.01" id="totalAmount" placeholder="Bijv. 50.00" required>-->
        <!--        </div>-->


        <input type="hidden" name="createdAt" value="<?php echo $currentDateTime; ?>">

        <button type="submit" class="add-group-button">Groep aanmaken</button>
    </form>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('memberSearch');
        const optionsList = document.getElementById('memberOptions');
        const selectedMembersDiv = document.getElementById('selectedMembers');
        const hiddenInputs = document.getElementById('hiddenInputs');

        input.addEventListener('focus', () => {
            optionsList.style.display = 'block';
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-multiselect')) {
                optionsList.style.display = 'none';
            }
        });

        optionsList.addEventListener('click', function (e) {
            if (e.target.classList.contains('option')) {
                const userId = e.target.getAttribute('data-id');
                const userName = e.target.textContent;


                if (document.getElementById('member-' + userId)) return;


                const memberTag = document.createElement('div');
                memberTag.classList.add('selected-member');
                memberTag.id = 'member-' + userId;
                memberTag.innerHTML = userName + '<span class="remove">&times;</span>';
                selectedMembersDiv.appendChild(memberTag);


                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'groupmembers[]';
                hiddenInput.value = userId;
                hiddenInput.id = 'input-' + userId;
                hiddenInputs.appendChild(hiddenInput);
            }
        });


        selectedMembersDiv.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove')) {
                const parent = e.target.parentElement;
                const userId = parent.id.replace('member-', '');
                document.getElementById('input-' + userId)?.remove();
                parent.remove();
            }
        });
    });
</script>