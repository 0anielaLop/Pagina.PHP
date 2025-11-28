<?php 
$page_title = "Manual de Usuario";
include __DIR__ . '/include/header.php';
?>

<div class="content-page">
    <h1>Manual de Usuario</h1>
    <p>Bienvenido a AnimeList. Esta guía te ayudará a utilizar todas las funciones de nuestra plataforma.</p>

    <div class="content-card">
        <h3>1. Registro y Inicio de Sesión</h3>
        <p>Para comenzar, necesitas una cuenta. Ve a <a href="registro.php">Registrarse</a> y completa el formulario. Luego, inicia sesión en <a href="login.php">Iniciar Sesión</a>.</p>
    </div>

    <div class="content-card">
        <h2>2. Explorar Animes</h2>
        <p>En la página de inicio, puedes ver una selección de animes. Utiliza la barra de búsqueda para encontrar animes específicos.</p>
    </div>

    <div class="content-card">
        <h2>3. Gestionar Tu Lista</h2>
        <p>En tu <a href="panel.php">Panel de Control</a>, puedes agregar animes a tu lista. Puedes categorizarlos en:</p>
        <ul>
            <li><strong>Viendo:</strong> Animes que estás viendo actualmente.</li>
            <li><strong>Por Ver:</strong> Animes que planeas ver.</li>
            <li><strong>Vistos:</strong> Animes que ya has completado.</li>
            <li><strong>Abandonados:</strong> Animes que has dejado de ver.</li>
        </ul>
        <p>Puedes editar tu lista en cualquier momento.</p>
    </div>

    <div class="content-card">
        <h2>4. Detalles del Anime</h2>
        <p>Al hacer clic en un anime,podras agregarlo a tu lista, en la cual verás sus detalles con información como sinopsis, género, episodios, etc.</p>
    </div>

    <div class="content-card">
        <h2>5. Cerrar Sesión</h2>
        <p>Para salir de tu cuenta, haz clic en el botón de cerrar sesión en la esquina superior derecha.</p>
    </div>

    <div class="content-card">
        <h2>6. Problemas Comunes</h2>
        <p>Si experimentas problemas técnicos, contacta a nuestro soporte.</p>
    </div>
</div>

<?php include __DIR__ . '/include/footer.php'; ?>