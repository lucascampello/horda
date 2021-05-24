<?php
if(!defined("ERRO"))
    require("includes/define.inc.php");

class Generic 
{
    private $mensagem;
    private $status;
    
    public function __construct()
    {
        $this->mesage = "";
        $this->status = OK;
    }
    
    public function setWarning($msg = "")
    {
        $this->mesage= $msg;
        $this->status = ERRO;
    }
    
    public function setOK($msg = "")
    {
        $this->mesage= $msg;
        $this->status = OK;
    }
    
    public function getState()
    {
        return $this->status;
    }
    
    public function getMesage()
    {
        return $this->mesage;
    }
    
}

?>
