<?php
namespace Classes;

if(!defined("ERRO"))
    require("includes/define.inc.php");

// Classe de Mensageria do Sistema
class Generic 
{
    private string $mensagem;
    private bool $status;
    
    public function __construct()
    {
        $this->mensagem = "";
        $this->status = OK;
    }
    
    public function setWarning($msg = "") : void
    {
        $this->mensagem= $msg;
        $this->status = ERRO;
    }
    
    public function setOK($msg = "") : void
    {
        $this->mensagem= $msg;
        $this->status = OK;
    }
    
    public function getState() : bool
    {
        return $this->status;
    }
    
    public function getMesage() : string
    {
        return $this->mensagem;
    }
    
}

?>
