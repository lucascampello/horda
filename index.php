<?php
session_start();
require("includes/define.inc.php");
require("classes/Jogo.class.php");
$objJogo = new Jogo();

if(!empty($_POST))
{
	if($_POST['action'])
	{
		// Contabiliza o número de players
		$jogadores = array();
		foreach($_POST['jogador'] as $chave => $jogador)
		{
			if(!empty(trim($jogador)))
				$jogadores[] = $jogador; 	// Preenche os jogadores
		}
		// Se não há jogadores
	    if(count($jogadores) == 0)
	    	$objJogo->setWarning("N° mínimo 1 sobrevivente");
	    else
	        $objJogo->setOK("Jogadores suficientes!");
	
	    // Vamor preencher denovo os jogadores
	    unset($_POST['jogador']);
	    foreach($jogadores as $chave => $jogador)
	    	$_POST['jogador'][$chave+1] = $jogador;

	    // Pronto pra come�ar?
	    if($objJogo->getState() == OK)
	    {
	        $objJogo->startGame();
	        header("location: jogo.php");
	        exit();
	    }
	    else
	    	$retorno = $objJogo->getMesage();
	}
}
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Horda de Zumbi</title>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,600,700,900&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="css/index.css">
		<link rel="shortcut icon" href="images/icone.ico" type="image/x-icon" />
	</head>
	<body>
		<div id="container">
            <section id="login">
                <main>
					<img src="images/logo.png">
					<form action="index.php" method="post" id="form1">
						<label>Jogador 1:</label> <input type="text" value="<?php echo (empty($_POST['jogador'][1])? "" : $_POST['jogador'][1]);?>" name="jogador[1]" /> <br/>
						<label>Jogador 2:</label> <input type="text" value="<?php echo (empty($_POST['jogador'][2])? "" : $_POST['jogador'][2]);?>" name="jogador[2]" /> <br/>
						<label>Jogador 3:</label> <input type="text" value="<?php echo (empty($_POST['jogador'][3])? "" : $_POST['jogador'][3]);?>" name="jogador[3]" /> <br/>
						<label>Nível :</label> 
						<select name='nivel'>
							<option value='facil' selected>Facil</option>
							<option value='medio'> Medio </option>
							<option value='dificil'> Dificil </option>
						</select>
						<button type="submit" name="action" form="form1" value="Submit">Começar jogo!</button>
					</form>
					<?php 
						if(!empty($retorno))
							echo $retorno;
					?>
				</main>
			</section>
			<footer></footer>
		</div>
	</body>
</html>