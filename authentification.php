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
    <form id="myform" action='http://localhost/projet/Affichage_dynamique/index.php'>
        <label class='label' for='login'>Token :</label>
        <input type='text' placeholder="Token" name='token' id='token'/><br/>
        <input type='submit' class="pure-button pure-button-primary" id="view-fullscreen" value="Confirmer"/>
    </form>
   
    </div>

    
</body>
</html>