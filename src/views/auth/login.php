<?php $title = 'Iniciar Sesión'; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4">Iniciar Sesión</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
        
        <p class="mt-3">¿No tienes cuenta? <a href="/restaurant/public/register">Regístrate aquí</a></p>
    </div>
</div>


