<?php
require ("Generic.class.php");
class Jogo extends Generic {
	public function __construct() {
		$this->options = array ();
		/**
		 * Deck (txt do include)
		 * 
		 * @var array
		 */
		$this->deck = array ();
		/**
		 * Deck explitado
		 * 
		 * @var array
		 */
		$this->deckGame = array ();
		/**
		 * Deck de compra
		 * 
		 * @var array
		 */
		$this->library = array ();
		/**
		 * Cartas na m�o
		 * 
		 * @var array
		 */
		$this->hand = array ();
		
		$this->board = array ();
		
		$this->removed = array ();
		$this->totalRemoved = 0;
		
		$this->cardAtual = 0;
		$this->totalLibrary = 0;
		$this->cemiterio = array ();
		$this->totalCemiterio = 0;
	}
	
	public function startGame() {
		self::setPlayers ( $_POST ['jogador'] );
		self::readDeck ();
		self::setGameDeck ();
		self::saveGame ();
	}
	
	/**
	 * Inicializa os jogadores
	 * 
	 * @param array $dados        	
	 */
	private function setPlayers($dados) {
		$contador = 1;
		// Para cada jogador
		foreach ( $dados as $jogador ) {
			// Se n�o estiver vazio
			if (! empty ( $jogador )) {
				$this->players [$contador] ['nome'] = $jogador; // Preenche a posi��o do jogador
				$contador ++;
			}
		}
		$this->players ['num_players'] = count ( $this->players );
		$this->players ['vida'] = MULTIPLICADOR_VIDA * count ( $this->players ); // Define a vida total do deck
	}
	
	public function playCard() {
	}
	
	/**
	 * Inicializa lendo TODO o deck
	 */
	private function readDeck() 
	{
		$deck = file ( "includes/decks/".$this->players['num_players']."P/".$_POST['nivel'].".txt" );
		if($this->players['num_players'] == 1)
			$this->totalLibrary = TAMANHO_1PLAYER;
		else if($this->players['num_players'] == 2)
			$this->totalLibrary = TAMANHO_2PLAYERS;
		else if($this->players['num_players'] == 3)
			$this->totalLibrary = TAMANHO_3PLAYERS;
		else if($this->players['num_players'] == 4)
			$this->totalLibrary = TAMANHO_4PLAYERS;
		else if($this->players['num_players'] == 5) 
			$this->totalLibrary = TAMANHO_5PLAYERS;

		// Cria um contador
		$contador_carta = 0;
		// Para cada elemento no texto
		foreach ( $deck as $carta ) { // explica as configura��es e coloca indice do array DECK
			$tmp = explode ( ';', $carta );
			$img = implode("_",explode(" ",$tmp[1]));
			
			$this->deck [$contador_carta] ['quantidade'] = $tmp [0];
			$this->deck [$contador_carta] ['nome'] = $tmp [1];
			$this->deck [$contador_carta] ['icon'] = "images/cards/icons/" . $img . ".jpg";
			$this->deck [$contador_carta] ['image'] = "images/cards/" . $img . ".jpg";
			$this->deck [$contador_carta] ['type'] = $tmp [2];
			if($tmp[2] == TOKEN OR $tmp[2] == CREATURE)
			{
				$this->deck [$contador_carta] ['tap'] = "images/cards/tap/" . $img . ".jpg";
				$this->deck [$contador_carta] ['attack'] = "images/cards/attack/" . $img . ".jpg";
			}	
			$contador_carta ++;
		}
	}
	
	/**
	 */
	private function setGameDeck() {
		// COPIA o Deck para uma vari�vel LOCAL
		$deck = $this->deck;
		$zombies = array_pop ( $deck ); // Extrai o ultimo elemento do array (Ficha padr�o)
		                             
		// Inicializa o Deck do Jogo
		for($i = 0; $i < $this->totalLibrary; $i ++)
			$this->deckGame [$i] = array ();
			
			// Para cada elemento do DECK (Sem o Zumbi Padr�o)
		foreach ( $deck as $carta ) {
			/**
			 * Para cada quantidade existente da c�pia daquela carta
			 */
			for($i = 0; $i < $carta ['quantidade']; $i ++) {
				// Posiciona a carta no deck FINAL
				for($set = 0; $set != 1;) { // Randomiza uma posi��o
					$id = rand ( 0, $this->totalLibrary - 1 );
					// Se � uma posi��o v�lida (Aloca no deck e PARA)
					if (empty ( $this->deckGame [$id] )) {
						$this->deckGame [$id] = $carta;
						// Elimina o item quantidade da carta
						unset ( $this->deckGame [$id] ['quantidade'] );
						$set = 1;
					}
				}
			}
		}
		
		// COLOCA AS FICHAS DE ZOMBI NOS SLOTS SOBRANDO
		foreach ( $this->deckGame as $id => $value ) {
			if (empty ( $this->deckGame [$id] )) {
				$this->deckGame [$id] = $zombies;
				unset ( $this->deckGame [$id] ['quantidade'] );
			}
		}
		$this->library = $this->deckGame;
	}
	
	/**
	 * Torna a sess�o local os dados das classes
	 */
	public function saveGame() {
		$_SESSION ['game'] ['players'] = $this->players;
		$_SESSION ['game'] ['deck'] = $this->deck;
		$_SESSION ['game'] ['deckGame'] = $this->deckGame;
		$_SESSION ['game'] ['library'] = $this->library;
		$_SESSION ['game'] ['hand'] = $this->hand;
		$_SESSION ['game'] ['board'] = $this->board;
		$_SESSION ['game'] ['totalLibrary'] = $this->totalLibrary;
		$_SESSION ['game'] ['cemiterio'] = $this->cemiterio;
		$_SESSION ['game'] ['totalCemiterio'] = $this->totalCemiterio;
		$_SESSION ['game'] ['removed'] = $this->removed;
		$_SESSION ['game'] ['totalRemoved'] = $this->totalRemoved;
	}
	
	/**
	 * L� os dados da sess�o para a classe
	 */
	public function getGame() {
		if(empty($_SESSION['game']))
			$this->quit();
		else 
		{
			foreach ( $_SESSION ['game'] as $id => $value )
				$this->$id = $value;
		}
	}
	
	/**
	 * Executa o jogo
	 */
	public function Game() {
		echo "  <div id=\"action\">
                    <h4 align=\"center\">AÇÕES</h4>
                    " . self::drawAction () . "
                </div>
                <div id=\"hand\">
                    <h4 align=\"center\">MÃO DO ADVERSÁRIO</h4>
              	      " . self::drawHand () . "
                    <br clear=\"all\">
                </div>
                <div id=\"board\">
                    <h4 align=\"center\">CAMPO</h4>
                    " . self::drawBoard () . "
                    <br clear=\"all\">
                </div>
                <div id=\"cemiterio\">
                    <h4 align=\"center\">CEMITERIO</h4>
                    " . self::drawCemiterio () . "
                    <br clear=\"all\">
                </div>
                <div id=\"removed\">
                    <h4 align=\"center\">ZONA DE REMOVIDAS</h4>
                    " . self::drawRemoved () . "
                    <br clear=\"all\">
                </div>";
		self::saveGame ();
	}
	
	/**
	 * Retorna a interface do topo
	 * 
	 * @return string
	 */
	private function drawAction() {
		$html = "
				<fieldset>
  					<legend> ETAPAS </legend>
                    <a href=\"?op=draw\" /><img src='images/button/draw.png' alt='Comprar' title='Comprar'></a>  <img src='images/button/divisoria.png'>
                    <a href=\"?op=play\" /><img src='images/button/play.png' alg='Jogar Cartas' title='Jogar Cartas'></a>  <img src='images/button/divisoria.png'>
        			<a href=\"?op=attack\"><img src='images/button/attack.png' alt='Atacar' title='Atacar'></a>
				</fieldset>
				<fieldset>
  					<legend> ADICIONAR TOKEN </legend>
					<form id='add_zumbi' method='GET' action='jogo.php'>
						<select name='add_zumbi'>";
				for($i=1;$i <= 10; $i++)
					$html .= "
							<option value='$i'>$i</option>";
		$html .= "
						</select>
						<img src='images/button/token.png' onclick='submitForm(\"add_zumbi\");' alt='Adicionar Zumbi 2/2' title='Adicionar Zumbi 2/2'> 
					</form>
				 	<img src='images/button/divisoria.png'>
					<b> <a href=\"?op=add13Zumbi\"><img src='images/button/token_13.png' alt='Adicionar 13 Fichas 2/2 Viradas' title='Adicionar 13 Fichas 2/2 Viradas'></a></b>
				</fieldset>
				<fieldset>
  					<legend> DANO </legend>
					<form id='remove' method='GET' action='jogo.php'>
						<select name='remove'>";
				for($i=1;$i <= 10; $i++)
					$html .= "
							<option value='$i'>$i</option>";
		$html .= "
						</select>
						<img src='images/button/excluir.png' onclick='submitForm(\"remove\");' alt='Remover Cartas do Topo' title='Remover Cartas do Topo'> 
					</form>
				</fieldset>
				<fieldset>
  					<legend> MILL </legend>
					<form id='mill' method='GET' action='jogo.php'>
						<select name='mill'>";
				for($i=1;$i <= 10; $i++)
					$html .= "
							<option value='$i'>$i</option>";
		$html .= "
						</select>
						<img src='images/button/mill.png' onclick='submitForm(\"mill\");' alt=' Millar Cartas do Topo' title='Millar Cartas do Topo'> 
					</form>
				</fieldset>
				<fieldset>
  					<legend> STATUS DO DECK </legend> 
					<img src='images/button/grimorio.png' alt='Grimório' title='Grimório'> <b>". $this->totalLibrary . "</b> <img src='images/button/divisoria.png'> 
					<img src='images/button/cemiterio.png' alt='Cemitério' title='Cemitério'> <b>" .$this->totalCemiterio . "</b> <img src='images/button/divisoria.png'> 
					<img src='images/button/exilada.png' alt='Cartas Exiladas' title='Cartas Exiladas'> <b>" .$this->totalRemoved . "</b> 
				</fieldset>
				<fieldset>
  					<legend> SAIR </legend> 
					  <a href=\"?op=quit\" />
						  <img src='images/button/quit.png' alt='Sair do Jogo' title='Sair do Jogo'> 
					  </a>
				</fieldset>
				";
		return $html;
	}
	
	/**
	 * Retorna a interface da m�o do jogador
	 */
	private function drawHand() {
		$html = "";
		foreach ( $this->hand as $id => $carta )
			$html .= "
                    <div class=\"hand_card\">
                        <a href=\"" . $carta ['image'] . "\" target=\"_new\"><img src='" . $carta ['icon'] . "'/></a>
                    </div>";
		return $html;
	}
	
	private function drawTokenAttack($carta, $indice) {
		return "<div class=\"board_card\">
			    	<img src='" . $carta ['attack'] . "'/>
        			<a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Destruir' Title='Destruir'></a>
        			</div>";
	}
	
	private function drawCreatureAttack($carta, $indice) {
		return "<div class=\"board_card\">
			    	<img src='" . $carta ['attack'] . "'/> 
			        <a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Destruir' Title='Destruir'></a>
			        <a href=\"?op=bounce&id=$indice\"><img src='images/button/bounce.png' alt='Retornar pra mão' Title='Retornar pra mão'></a>
		        </div>";
	}
	
	private function drawToken($carta, $indice) {
		return "<div class=\"board_card\">
			    	<img src='" . $carta ['icon'] . "'/>
        			<a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Destruir' Title='Destruir'></a>
        			<a href=\"?op=tap&id=$indice\"><img src='images/button/tap.png' alt='Virar Criatura' Title='Virar Criatura'></a>
        		</div>";
	}
	private function drawCard($carta, $indice) {
		return "<div class=\"board_card\">
			    	<img src='" . $carta ['icon'] . "'/>
  			    	<a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Eliminar' Title='Eliminar'></a>
   			    	<a href=\"?op=bounce&id=$indice\"><img src='images/button/bounce.png' alt='Retornar pra mão' Title='Retornar pra mão'></a>
        			<a href=\"?op=tap&id=$indice\"><img src='images/button/tap.png' alt='Virar Criatura' Title='Virar Criatura'></a>
   			    </div>";
	}
	private function drawTapCard($carta, $indice) {
		return "<div class=\"board_card\">
			    	<img src='" . $carta ['tap'] . "'/>
			    	<a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Eliminar' Title='Eliminar'></a>
			    	<a href=\"?op=bounce&id=$indice\"><img src='images/button/bounce.png' alt='Retornar pra mão' Title='Retornar pra mão'></a>
    	    	</div>";
	}
	private function DrawTapToken($carta, $indice) {
		return "<div class=\"board_card\">
			    	<img src='" . $carta ['tap'] . "'/>
   			    	<a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Eliminar' Title='Eliminar'></a>
   		    	</div>";
	}
	
	private function drawSpellCard($carta, $indice) {
		return "<div class=\"board_card\">
			    	<img src='" . $carta ['icon'] . "'/>
			    	<a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Eliminar' Title='Eliminar'></a>
			    	<a href=\"?op=bounce&id=$indice\"><img src='images/button/bounce.png' alt='Retornar pra mão' Title='Retornar pra mão'></a>
    	    	</div>";
	}
	
 	/**
	 * Retorna a tela do jogo
	 * 
	 * @return string
	 */
	private function drawBoard() {
		$html = "";
		foreach ( $this->board as $indice => $carta ) 
		{
			
			if($carta['type'] == SPELL OR $carta['type'] == ENCHANTMENT)
				$html .= self::drawSpellCard ( $carta, $indice );
			else if($carta['type'] == TOKEN)
			{
				if(empty($carta['status']))
					$html .= self::drawToken ( $carta, $indice );
				else if($carta['status'] == ATTACK)
					$html .= self::drawTokenAttack ( $carta, $indice );
				else if($carta['status'] == TAP)
					$html .= self::DrawTapToken ( $carta, $indice );
				else if($carta['status'] == NAO_ATTACK)
					$html .= self::drawToken ( $carta, $indice );
			}
			else if($carta['type'] == CREATURE)
			{
				if(empty($carta['status']))
					$html .= self::drawCard ( $carta, $indice );
				else if($carta['status'] == ATTACK)
					$html .= self::drawCreatureAttack( $carta, $indice );
				else if($carta['status'] == TAP)
					$html .= self::drawTapCard ( $carta, $indice );
				else if($carta['status'] = NAO_ATTACK)
					$html .= self::drawCard ( $carta, $indice );
			}
		}
		return $html;
	}
	
	/**
	 * Retorna o cemit�rio
	 * 
	 * @return string
	 */
	private function drawCemiterio() {
		$html = "";
		foreach ( $this->cemiterio as $id => $carta ) {
			if ($carta ['type'] != TOKEN) {
				$html .= "
	                    <div class=\"cemiterio_card\">
	                        <a href=\"" . $carta ['image'] . "\" target=\"_new\"><img class='card_icone' src='" . $carta ['icon'] . "'/> </a>
	                        <a href=\"?op=goBack&id=$id\"><img id='icone' src='images/button/bounce.png' alt='Retornar pro Jogo' Title='Retornar pro Jogo'></a>
	                        <a href=\"?op=RemoveBack&id=$id\"><img id='icone' src='images/button/remove.png' alt='Remova do Cemitério' Title='Remova do Cemitério'></a>
	                    </div>";
			}
		}
		return $html;
	}
	
	private function drawRemoved() {
		$html = "";
		foreach ( $this->removed as $id => $carta ) 
		{
			if ($carta ['type'] != TOKEN) 
			{
				$html .= "
	                    <div class=\"cemiterio_card\">
	                        <a href=\"" . $carta ['image'] . "\" target=\"_new\"><img class='card_icone' src='" . $carta ['icon'] . "'/> </a>
		                </div>";
			}
		}
		return $html;
	}
	
	/**
	 * Fase de Jogar na Mesa! (Revisar)
	 */
	public function play() {
		// Jogo os Tokens Primeiro !
		$quantidade_cartas = count ( $this->hand );
		if ($quantidade_cartas == 0)
			return;
		foreach ( $this->hand as $indice => $carta ) {
			if ($carta ['type'] == TOKEN) {
				$this->board [] = $this->hand [$indice];
				unset ( $this->hand [$indice] );
				return;
			}
		}
		
		$this->board [] = array_shift ( $this->hand );
		return;
	}
	
	/**
	 * Fase de Compra
	 */
	public function draw() {
		for($fimDraw = 0; $fimDraw == 0;) {
			if ($this->totalLibrary == 0)
				$fimDraw = 1;
			else {
				$carta = array_shift ( $this->library );
				$this->totalLibrary --;
				
				$carta ['status'] = NAO_ATTACK;
				$this->hand [] = $carta;
				
				if ($carta ['type'] != TOKEN)
					$fimDraw = 1;
			}
		}
		
		foreach ( $this->board as $id => $carta ) {
			if ($carta ['type'] != ENCHANTMENT)
				$this->board [$id] ['status'] = NAO_ATTACK;
		}
	}
	public function addZumbi($valor) {
		$tmp = $this->deck;
		$zumbi = array_pop ( $tmp );
		$zumbi['status'] = NAO_ATTACK;
		
		for($i = 0; $i < $valor; $i++)
			$this->board [] = $zumbi;
	}
	
	public function mill($value) {
		if ($this->totalLibrary == 0)
			return;
		
		$quantidade_menor = $value;
		if ($this->totalLibrary < $value)
			$quantidade_menor = $this->totalLibrary;
		
		for($i = 0; $i < $quantidade_menor; $i ++)
		{
			
			$this->cemiterio[] = array_shift ( $this->library );
		}
	
		$this->totalCemiterio += $quantidade_menor;
		$this->totalLibrary -= $quantidade_menor;
	}

	public function quit() 
	{
		unset($_GET);
		unset($_POST);
		unset($_SESSION);
		session_destroy();
		header("location: index.php");
		exit();
	}
	
	public function add13Zumbi() {
		$tmp = $this->deck;
		$zumbi = array_pop ( $tmp );
		$zumbi['status'] = TAP;
		for($i = 0; $i < 13; $i++)
			$this->board [] = $zumbi;
	}
	
	public function attack() {
		$jogadorAtacavel = array ();
		foreach ( $this->board as $id => $carta ) {
			if ($carta ['type'] == SPELL)
			{
				$this->cemiterio[] = $this->board [$id];
				$this->totalCemiterio++;
				unset ( $this->board [$id] );
			}
			else if (($carta ['type'] == CREATURE or $carta ['type'] == TOKEN) and ($carta ['status'] != TAP))
				$this->board [$id] ['status'] = ATTACK;
		}
	}
	public function kill() {
		if ($this->board [$_GET ['id']] ['type'] != TOKEN) {
			if ($this->board [$_GET ['id']] ['type'] == CREATURE)
				$this->board [$_GET ['id']] ['status'] = NAO_ATTACK;
			
			$this->cemiterio [] = $this->board [$_GET ['id']];
			$this->totalCemiterio ++;
		}
		unset ( $this->board [$_GET ['id']] );
	}
	public function goBack() {
		$this->board [] = $this->cemiterio [$_GET ['id']];
		$this->totalCemiterio --;
		unset ( $this->cemiterio [$_GET ['id']] );
	}
	public function goRemovedBack() {
		$this->board [] = $this->removed [$_GET ['id']];
		$this->totalRemoved--;
		unset ( $this->removed [$_GET ['id']] );
	}
	public function bounce() {
		$this->board [$_GET ['id']]['status'] = NAO_ATTACK;
		$this->hand [] = $this->board [$_GET ['id']];
		unset ( $this->board [$_GET ['id']] );
	}
	public function tap() {
		$this->board [$_GET ['id']] ['status'] = TAP;
	}
	public function remove($value) {
		if ($this->totalLibrary == 0)
			return;
		
		$quantidade_menor = $value;
		if ($this->totalLibrary < $value)
			$quantidade_menor = $this->totalLibrary;
		
		for($i = 0; $i < $quantidade_menor; $i ++)
			$this->removed[] = array_shift ( $this->library );
		
		$this->totalRemoved += $quantidade_menor;
		$this->totalLibrary -= $quantidade_menor;
	}
	
	public function RemoveBack()
	{
		$this->removed[] = $this->cemiterio[$_GET['id']];
		$this->totalRemoved++;
		unset($this->cemiterio[$_GET['id']]);
		$this->totalCemiterio--;
	}

	public function endGame() {
		$fim = true;
		if($this->totalLibrary == 0 && (count($this->hand) == 0))
		{
			if(count($this->board) != 0)
			{
				foreach($this->board as $carta)
				{
					if($carta['type'] < 2)
					{
						$fim = false;
						break;
					}
				}
			}
		}		
		else
			$fim = false;

		if($fim == true)
		{
			echo "<script src=\"js/onOff.js\"></script>";
		}

	}
}
?>