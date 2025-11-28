<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>

<?php echo htmlspecialchars($pageTitle ?? 'AnimeList', ENT_QUOTES, 'UTF-8');

?></title>

<link rel="stylesheet" href="./style.css">

</head>

<body>

<header>

<h1><?php echo htmlspecialchars($pageTitle ?? 'AnimeList', ENT_QUOTES, 'UTF-8'); ?></h1>

<?php include __DIR__ . '/nav.php'; ?>

</header>

<main>