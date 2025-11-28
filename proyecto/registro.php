<?php
$pageTitle = "Registro";
include __DIR__ . '/include/header.php';
?>

<section class="auth-form-container">
    <div class="auth-form-box">
        <h2 class="auth-form-title">Crear Cuenta</h2>
        <form action="register.php" method="post">
            <div class="auth-form-group">
                <input type="text" name="username" class="auth-form-input" placeholder="Nombre de usuario" required>
            </div>
            <div class="auth-form-group">
                <input type="email" name="email" class="auth-form-input" placeholder="Correo electrónico" required>
            </div>
            <div class="auth-form-group">
                <input type="password" name="password" class="auth-form-input" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="auth-form-button">Registrarse</button>
        </form>
        <div class="auth-form-link">
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/include/footer.php'; ?>
