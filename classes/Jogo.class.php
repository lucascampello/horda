<?php
namespace Classes;
use Classes\Generic;
use Classes\Graphic;

class Jogo extends Generic {
	private array $deck;					// Lista BASE de Cartas do Deck (import txt)
	private array $deckGame;				// Lista de Cartas do Deck (Pós-Parsing)
	private array $library;					// Lista de Cartas do Deck (InGame)
	private array $hand;					// Lista de Cartas na Mão 
	private array $board;					// Lista de Cartas no Campo
	private array $removed;					// Lista de Cartas na Zona de Exílio
	private array $cemiterio;				// Lista de Cartas na Zona do Cemitério
	private array $players;					// Dados Relativo aos Jogadores [SOBREVIVENTES]
	private string $nivel;					// Nível de Dificuldade da Partida

	private int $totalLibrary;				// Contador de Cartas na Mão
	private int $totalRemoved;				// Contador de Cartas Removidas
	private int $totalCemiterio;			// Contador de Cartas no Cemitério

	private string $horda;					// Tipo de Horda

	public function __construct() {
		// PREPARAÇÃO DAS LISTAS
		$this->deck = array ();
		$this->deckGame = array ();
		$this->library = array ();
		$this->hand = array ();
		$this->board = array ();
		$this->removed = array ();
		$this->cemiterio = array ();

		// PREPARAÇÃO DOS CONTADORES 
		$this->totalRemoved = $this->totalLibrary = $this->totalCemiterio = 0;
		
		$this->horda = 'Zumbi'; // Pré-Define o Tipo Base
		$this->nivel = 'Fácil'; // Pré-Define o Nível de Dificuldade Base
	}
	
	// Função Inicial que Carrega os Dados e Guarda na Sessão da Aplicação
	public function startGame() {
		self::setPlayers ($_POST ['jogador']);
		self::setHorda($_POST['tipo']);
		self::setNivel($_POST['nivel']);
		self::readDeck ();					// Carrega o Deck (TXT)
		self::setGameDeck ();				// Armazena no Deck InGame e Embaralha
		self::saveGame ();
	}
	
	/**
	 * Inicializa os jogadores
	 * 
	 * @param array $dados        	
	 */
	private function setPlayers($dados) {
		$this->players ['num_players'] = count ( $dados );
	}

	/**
	 * Define a Horda do Jogo
	 * 
	 * @param string $horda
	 */
	private function setHorda($horda) : void
	{
		$this->horda = (in_array(ucfirst($horda), HORDA))? $horda : $this->horda;
	}

	/**
	 * Define o Nível do Jogo
	 * 
	 * @param string $nivel
	 */
	private function setNivel($nivel) : void
	{
		$this->nivel = (in_array($nivel, NIVEL))? $nivel : $this->nivel;
	}

	/**
	 * Inicializa lendo TODO o deck
	 */
	private function readDeck() 
	{
		$deck = file ( "includes/decks/".$this->horda."/".$this->players['num_players']."P/".$this->nivel.".txt" );
		if($this->players['num_players'] == 1)
			$this->totalLibrary = TAMANHO_1PLAYER;
		else if($this->players['num_players'] == 2)
			$this->totalLibrary = TAMANHO_2PLAYERS;
		else if($this->players['num_players'] == 3)
			$this->totalLibrary = TAMANHO_3PLAYERS;

		// Cria um contador
		$contador_carta = 0;
		// Para cada elemento no texto
		foreach ( $deck as $carta ) { // explica as configura��es e coloca indice do array DECK
			$cartaTemporaria = explode ( ';', $carta );
			$img = implode("_",explode(" ",$cartaTemporaria[COLUNA_NOME]));
			
			$this->deck [$contador_carta] ['quantidade'] = $cartaTemporaria[COLUNA_QUANTIDADE];
			$this->deck [$contador_carta] ['nome'] = $cartaTemporaria[COLUNA_NOME];
			$this->deck [$contador_carta] ['icon'] = "images/".$this->horda."/card/" . $img . ".jpg";
			$this->deck [$contador_carta] ['image'] = $this->deck [$contador_carta] ['icon'];
			$this->deck [$contador_carta] ['type'] = $cartaTemporaria[COLUNA_TIPO];
			if($cartaTemporaria[COLUNA_TIPO] == TOKEN OR $cartaTemporaria[COLUNA_TIPO] == CREATURE)
			{
				$this->deck [$contador_carta] ['tap'] = "images/".$this->horda."/tap/" . $img . ".jpg";
				$this->deck [$contador_carta] ['attack'] = "images/".$this->horda."/attack/" . $img . ".jpg";
			}	
			$contador_carta ++;
		}
	}
	
	private function setGameDeck() {
		// COPIA o Deck para uma vari�vel LOCAL
		$deck = $this->deck;
		$zombies = array_pop ( $deck ); // Extrai o ultimo elemento do array (Ficha padrao)
		                             
		// Inicializa o Deck do Jogo
		for($i = 0; $i < $this->totalLibrary; $i ++)
			$this->deckGame [$i] = array ();
			
		// Para cada elemento do DECK (Sem a Ficha de Zumbi)
		foreach ( $deck as $carta ) {
			/**
			 * Para cada quantidade existente da copia daquela carta
			 */
			for($i = 0; $i < $carta ['quantidade']; $i ++) {
				// Posiciona a carta no deck FINAL
				for($set = 0; $set != 1;) { // Randomiza uma posicao
					$id = rand ( 0, $this->totalLibrary - 1 );
					// Se é uma posição válida (Aloca no deck e BUSCA A PRÓXIMA CARTA)
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
	
	// Grava os Dados da Partida na Sessão
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
		$_SESSION ['game'] ['horda'] = $this->horda;
		$_SESSION ['game'] ['nivel'] = $this->nivel;
	}
	
	// Carrega os Dados Salvos na Sessão
	public function getGame() {
		if(empty($_SESSION['game']))
			$this->quit();
		else 
		{
			foreach ( $_SESSION ['game'] as $id => $value )
				$this->$id = $value;
		}
	}
	
	// Renderiza o Jogo e Salva o Status na Sessão
	public function Game() : void {
		$html = "";
		$html .= Graphic::drawMenu($this->totalCemiterio, $this->totalLibrary, $this->totalRemoved);
		$html .= Graphic::drawHand($this->hand);
		$html .= Graphic::drawBoard ($this->board);
		$html .= Graphic::drawCemiterio ($this->cemiterio);
		$html .= Graphic::drawRemoved ($this->removed);

		echo $html;
		
		self::saveGame (); // Salva o Status Atual do Jogo
		return;
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
	public function addToken($valor) {
		$tmp = $this->deck;
		$token = array_pop ( $tmp );
		$token['status'] = NAO_ATTACK;
		
		for($i = 0; $i < $valor; $i++)
			$this->board [] = $token;
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
		
	// Função de Ação de Ataque (Icone do Menu)
	public function attack() {
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
	
	// Devolve pro Cemitério Carta Removida Por Engano
	public function RemoveBack()
	{
		$this->removed[] = $this->cemiterio[$_GET['id']];
		$this->totalRemoved++;
		unset($this->cemiterio[$_GET['id']]);
		$this->totalCemiterio--;
	}

	// Verifica o Fim do Jogo (DeckOver + EmptyLibrary)
	public function endGame() {
		$fim = true;
		if($this->totalLibrary == 0 && (count($this->hand) == 0))
		{
			if(count($this->board) != 0) // Só Termina Se o BoardGame Tiver Apenas Encantamento (Type = 2)
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
			echo "<script src=\"js/onOff.js\"></script>";

	}
}
?>