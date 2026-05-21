<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Paciente - BioVital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/css/all.min.css">
</head>
<?php
// NO iniciar sesión aquí - el Front Controller ya lo hace
if(!empty($_SESSION['us_tipo']) && $_SESSION['rol'] == 'paciente'){
    header('Location: ' . APP_URL . '/panel/paciente');
    exit();
}
?>
<body>
    <img class="wave" src="../img/wave.png" alt="">
    <div class="contenedor">
        <div class="img">
            <img src="../img/paciente.svg" alt="">
        </div>
        <div class="contenido-login">
            <form id="form-login" method="POST">
                <img src="../img/logo_azul.png" alt="">
                <h2>Paciente</h2>
                <input type="hidden" name="rol" value="paciente">
                <div class="input-div cedula">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <h5>Cédula</h5>
                        <input type="text" name="user" class="input" required>
                    </div>
                </div>
                <div class="input-div pass">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Contraseña</h5>
                        <input type="password" name="pass" class="input" required>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="<?php echo APP_URL; ?>/registro/paciente" class="btn btn-link" style="text-decoration: none;">Crear nueva cuenta</a>
                </div>
                <input type="submit" class="btn" value="Iniciar Sesión">
            </form>
        </div>
    </div>
</body>
<script src="../js/login.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#form-login').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?php echo APP_URL; ?>/login',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    window.location.href = response.redirect;
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                alert('Error de conexión');
            }
        });
    });
});
</script>
</html>