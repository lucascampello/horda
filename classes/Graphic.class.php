<?php
namespace Classes;

if(!defined("ERRO"))
    require("includes/define.inc.php");

class Graphic {

    public function __contruct() {
    }

    public static function drawHand(array $cartas) : string  {
        $html = "
                <div id=\"hand\">
                    <h4 align=\"center\">MÃO DO ADVERSÁRIO</h4>";
        foreach ( $cartas as $id => $carta ) {
            $html .= "
                    <div class=\"hand_card\">
                        <a href=\"" . $carta ['image'] . "\" target=\"_new\"><img src='" . $carta ['icon'] . "'/></a>
                    </div>";
        }
        $html .= '
                    <br clear="all">
                </div>';
        return $html;
    }

    public static function drawTokenAttack(array $carta, int $indice) : string {
        return "<div class=\"board_card\">
                    <img src='" . $carta ['attack'] . "' witdh='206px' height='287px'/>
                    <a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Destruir' Title='Destruir'></a>
                    </div>";
    }

    public static function drawCreatureAttack(array $carta,int $indice) : string {
        return "<div class=\"board_card\">
                    <img src='" . $carta ['attack'] . "' witdh='206px' height='287px'/>
                    <a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Destruir' Title='Destruir'></a>
                    <a href=\"?op=bounce&id=$indice\"><img src='images/button/bounce.png' alt='Retornar pra mão' Title='Retornar pra mão'></a>
                </div>";
    }

    public static function drawToken(array $carta, int $indice) : string {
        return "<div class=\"board_card\">
                    <img src='" . $carta ['icon'] . "' witdh='206px' height='287px'/>
                    <a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Destruir' Title='Destruir'></a>
                    <a href=\"?op=tap&id=$indice\"><img src='images/button/tap.png' alt='Virar Criatura' Title='Virar Criatura'></a>
                </div>";
    }

    public static function drawCard(array $carta,int  $indice) : string {
        return "<div class=\"board_card\">
                    <img src='" . $carta ['icon'] . "' witdh='206px' height='287px'/>
                    <a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Eliminar' Title='Eliminar'></a>
                    <a href=\"?op=bounce&id=$indice\"><img src='images/button/bounce.png' alt='Retornar pra mão' Title='Retornar pra mão'></a>
                    <a href=\"?op=tap&id=$indice\"><img src='images/button/tap.png' alt='Virar Criatura' Title='Virar Criatura'></a>
                </div>";
    }

    public static function drawTapCard(array $carta,int  $indice) : string {
        return "<div class=\"board_card\">
                    <img src='" . $carta ['tap'] . "' witdh='206px' height='287px'/>
                    <a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Eliminar' Title='Eliminar'></a>
                    <a href=\"?op=bounce&id=$indice\"><img src='images/button/bounce.png' alt='Retornar pra mão' Title='Retornar pra mão'></a>
                </div>";
    }

    public static function drawTapToken(array $carta,int  $indice) : string {
        return "<div class=\"board_card\">
                    <img src='" . $carta ['tap'] . "' witdh='206px' height='287px'/>
                    <a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Eliminar' Title='Eliminar'></a>
                </div>";
    }

    public static function drawSpellCard(array $carta,int  $indice) : string {
        return "<div class=\"board_card\">
                    <img src='" . $carta ['icon'] . "' witdh='206px' height='287px'/>
                    <a href=\"?op=kill&id=$indice\"><img src='images/button/destroy.png' alt='Eliminar' Title='Eliminar'></a>
                    <a href=\"?op=bounce&id=$indice\"><img src='images/button/bounce.png' alt='Retornar pra mão' Title='Retornar pra mão'></a>
                </div>";
    }


    public static function drawBoard(array $cartasBoard) : string {
        $html = "
                <div id=\"board\">
                    <h4 align=\"center\">CAMPO</h4>";

        foreach ( $cartasBoard as $indice => $carta ) 
        {
            
            if($carta['type'] == SPELL OR $carta['type'] == ENCHANTMENT)
                $html .= Graphic::drawSpellCard ( $carta, $indice );
            else if($carta['type'] == TOKEN)
            {
                if(empty($carta['status']))
                    $html .= Graphic::drawToken ( $carta, $indice );
                else if($carta['status'] == ATTACK)
                    $html .= Graphic::drawTokenAttack ( $carta, $indice );
                else if($carta['status'] == TAP)
                    $html .= Graphic::drawTapToken ( $carta, $indice );
                else if($carta['status'] == NAO_ATTACK)
                    $html .= Graphic::drawToken ( $carta, $indice );
            }
            else if($carta['type'] == CREATURE)
            {
                if(empty($carta['status']))
                    $html .= Graphic::drawCard ( $carta, $indice );
                else if($carta['status'] == ATTACK)
                    $html .= Graphic::drawCreatureAttack( $carta, $indice );
                else if($carta['status'] == TAP)
                    $html .= Graphic::drawTapCard ( $carta, $indice );
                else if($carta['status'] = NAO_ATTACK)
                    $html .= Graphic::drawCard ( $carta, $indice );
            }
        }

        $html .= '
                    <br clear="all">
                </div>';
        return $html;
    }

    public static function drawCemiterio($cartasCemiterio) : string {
        $html = "
                <div id=\"cemiterio\">
                    <h4 align=\"center\">CEMITERIO</h4>";
        foreach ( $cartasCemiterio as $id => $carta ) {
            if ($carta ['type'] != TOKEN) {
                $html .= "
                        <div class=\"cemiterio_card\">
                            <a href=\"" . $carta ['image'] . "\" target=\"_new\"><img class='card_icone' src='" . $carta ['icon'] . "'/> </a>
                            <a href=\"?op=goBack&id=$id\"><img id='icone' src='images/button/bounce.png' alt='Retornar pro Jogo' Title='Retornar pro Jogo'></a>
                            <a href=\"?op=RemoveBack&id=$id\"><img id='icone' src='images/button/remove.png' alt='Remova do Cemitério' Title='Remova do Cemitério'></a>
                        </div>";
            }
        }
        $html .= '
                    <br clear="all">
                </div>';
        return $html;
    }

    public static function drawRemoved($cartasRemovidas) : string {
        $html = "
                <div id=\"removed\">
                    <h4 align=\"center\">ZONA DE REMOVIDAS</h4>";
        foreach ( $cartasRemovidas as $id => $carta ) 
        {
            if ($carta ['type'] != TOKEN) 
            {
                $html .= "
                        <div class=\"cemiterio_card\">
                            <a href=\"" . $carta ['image'] . "\" target=\"_new\"><img class='card_icone' src='" . $carta ['icon'] . "'/> </a>
                        </div>";
            }
        }
        $html .= '
                    <br clear="all">
                </div>';
        return $html;
    }

    public static function drawMenu(int $totalCemiterio, int $totalLibrary, int $totalRemoved) {
        $html = "
            <div id=\"action\">
                <h4 align=\"center\">AÇÕES</h4>
                <fieldset>
                    <legend> ETAPAS </legend>
                    <a href=\"?op=draw\" /><img src='images/button/draw.png' alt='Comprar' title='Comprar'></a>  <img src='images/button/divisoria.png'>
                    <a href=\"?op=play\" /><img src='images/button/play.png' alg='Jogar Cartas' title='Jogar Cartas'></a>  <img src='images/button/divisoria.png'>
                    <a href=\"?op=attack\"><img src='images/button/attack.png' alt='Atacar' title='Atacar'></a>
                </fieldset>
                <fieldset>
                    <legend> ADICIONAR TOKEN </legend>
                    <form id='add_token' method='GET' action='jogo.php'>
                        <select name='add_token'>";
        for($i=1;$i <= 10; $i++)
            $html .= "
                            <option value='$i'>$i</option>";
        
        $html .= "
                        </select>
                        <img src='images/".$_SESSION ['game'] ['horda']."/token.png' onclick='submitForm(\"add_token\");' alt='Adicionar Token' title='Adicionar Token'>                 </fieldset>
                    </form>
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
                    <img src='images/button/grimorio.png' alt='Grimório' title='Grimório'> <b>". $totalLibrary . "</b> <img src='images/button/divisoria.png'> 
                    <img src='images/button/cemiterio.png' alt='Cemitério' title='Cemitério'> <b>" .$totalCemiterio . "</b> <img src='images/button/divisoria.png'> 
                    <img src='images/button/exilada.png' alt='Cartas Exiladas' title='Cartas Exiladas'> <b>" .$totalRemoved . "</b> 
                </fieldset>
                <fieldset>
                    <legend> SAIR </legend> 
                    <a href=\"?op=quit\" />
                        <img src='images/button/quit.png' alt='Sair do Jogo' title='Sair do Jogo'> 
                    </a>
                </fieldset>
            </div>
                ";
        return $html;
    }
}