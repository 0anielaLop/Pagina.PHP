<?php
$pageTitle = "Iniciar Sesión";
include __DIR__ . '/include/header.php';

?>

<section class="auth-form-container">
    <div class="auth-form-box">
        <h2 class="auth-form-title">Iniciar Sesión</h2>
        <form action="authenticate.php" method="post">
            <div class="auth-form-group">
                <input type="text" name="identifier" class="auth-form-input" placeholder="Usuario o correo electrónico" required>
            </div>
            <div class="auth-form-group">
                <input type="password" name="password" class="auth-form-input" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="auth-form-button">Entrar a Mi Cuenta</button>
        </form>
        <div class="auth-form-link">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/include/footer.php';
?>