<?php
if (isset($_GET['token'])) {
    $_SESSION['invite_token'] = $_GET['token'];
}
?>

<div class="container">
    <div class="form-box login">
        <form action="php/login.php" method="POST">
            <!--    succes/fout melding-->
            <?php foreach (['error', 'success'] as $type): ?>
                <?php if (!empty($_SESSION[$type])): ?>
                    <div class="<?= $type ?>-message">
                        <?= htmlspecialchars($_SESSION[$type]); unset($_SESSION[$type]); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <h1>Inloggen</h1>
            <div class="input-box">
                <input type="text" id="email" placeholder="Email" name="email" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" placeholder="Wachtwoord" name="password" required>
                <i class='bx bxs-lock-alt' ></i>
            </div>
            <div class="forgot-link">
                <a href="#">Wachtwoord vergeten?</a>
            </div>
            <button type="submit" class="btn">Inloggen</button>
        </form>
    </div>


    <div class="form-box register">
        <form action="php/register.php" method="POST">
            <h1>Registratie</h1>
            <!--    succes/fout melding-->
            <?php foreach (['error', 'success'] as $type): ?>
                <?php if (!empty($_SESSION[$type])): ?>
                    <div class="<?= $type ?>-message">
                        <?= htmlspecialchars($_SESSION[$type]); unset($_SESSION[$type]); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="input-box">
                <input type="email" placeholder="Email" id="email" name="email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="email" placeholder="Bevestig email" id="confirm_email" name="confirm_email" required>
                <i class='bx bxs-envelope' ></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Wachtwoord" id="password" name="password" required>
                <i class='bx bxs-lock-alt' ></i>
            </div>

            <div class="input-box">
                <input type="text" placeholder="Naam" id="name" name="name" required>
                <i class='bx bxs-user'></i>
            </div>

            <button type="submit" class="btn">Registreren</button>
        </form>
    </div>

    <div class="toggle-box">
        <div class="toggle-panel toggle-left">
            <h1>Hallo, welkom!</h1>
            <p>Heb je nog geen account?</p>
            <button class="btn register-btn">Registreren</button>
        </div>

        <div class="toggle-panel toggle-right">
            <h1>Welkom terug!</h1>
            <p>Heb je al een account?</p>
            <button class="btn login-btn">Inloggen</button>
        </div>
    </div>
</div>

<script src="js/loginRegister.js"></script>

