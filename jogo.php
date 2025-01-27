<?php
require("classes/Autoloader.class.php");
require("includes/define.inc.php");
session_start();

use Classes\Jogo;

$objJogo = new Jogo();
$objJogo->getGame();

if(!empty($_GET['op']))
{
    $acao = $_GET['op'];
    $objJogo->$acao();
}
else if(!empty($_GET['add_token']))
	$objJogo->addToken($_GET['add_token']);
else if(!empty($_GET['remove']))
	$objJogo->remove($_GET['remove']);
else if(!empty($_GET['mill']))
	$objJogo->mill($_GET['mill']);	
else if(!empty($_GET['quit']))
    $objJogo->quit();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Horda de Zumbi</title>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,600,700,900&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="css/jogo.css">
		<link rel="shortcut icon" href="images/icone.png" type="image/png" />
    </head>
    <body>
        <div id="centro">
            <div id="conteudo"><?php $objJogo->game(); ?></div>
            <div id="aviso" class="hide">
                <div id="esquerda">
                    <img src="images/logo.png">
                </div>
                <div id="direita">
                    <img src="images/endGame.png">
                    <a href="?op=quit"><img src="images/newGame.png"></a>
                </div>                
                <?php $objJogo->endGame(); ?>
            </div>
        </div>
        <script src="js/main.js"></script>
    </body>
</html>