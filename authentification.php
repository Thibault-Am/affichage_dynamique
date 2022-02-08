<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification affichage dynamique</title>
    <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
</head>
<body>
    <h1>Inter'Display</h1>
    <div id='container'>
    <?php
        echo "
        <form action='http://localhost/Affichage_dynamique/affichage.php'>
        <label class='label' for='login'>Token d'authentification :</label>
        <input type='text' placeholder=\"Token\" name='token' id='token'/><br/>
        <label class='label' for='ecran'>Nom de l'écran :</label>
        <input type=\"text\" placeholder=\"Nom de l'écran\" name='ecran' id=\"ecran\" required /><br/>
        <input type=\"submit\" class=\"pure-button pure-button-primary\" value='Confirmer'/></form>";
    ?>
    </div>
    <script>
        var password = document.getElementById("password")
        , confirm_password = document.getElementById("confirm_password");

        function validatePassword(){
        if(password.value != confirm_password.value) {
            confirm_password.setCustomValidity("Passwords Don't Match");
        } else {
            confirm_password.setCustomValidity('');
        }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    </script>
</body>
</html>