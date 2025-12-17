<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/restaurant/public/assets/css/style.css">
</head>
<body>
    <?php require VIEWS_PATH . '/partials/header.php'; ?>
    <main class="container my-4">
    <?php
        if (isset($view)) {
            $viewFile = VIEWS_PATH . '/' . $view . '.php';
            if (is_readable($viewFile )) {
                require $viewFile ;
            }
            else {
                echo "View not found:" . $view;
            }
        }
    ?>
    <?php require VIEWS_PATH . '/partials/footer.php'; ?>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="/restaurant/public/assets/js/main.js"></script>
</body>
</html>
