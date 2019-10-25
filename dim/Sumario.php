<?php
namespace dimensoes;
class Sumario{
    public $quantidadeInclusoes;
    public $quantidadeAlteracoes;

    function __construct()
    {
        $this->quantidadeAlteracoes = 0;
        $this->quantidadeInclusoes = 0;
    }

    public function setQuantidadeInclusoes(){
        $this->quantidadeInclusoes ++;
    }

    public function setQuantidadeAlteracoes(){
        $this->quantidadeAlteracoes ++;
    }
}


?>