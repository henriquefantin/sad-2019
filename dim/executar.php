<?php
    
    require_once('DimCliente.php');

    use dimensoes\DimCliente;

    try{
        $dimCliente = new DimCliente();
        $sumCliente = $dimCliente->carregarDimCliente();
        
        echo "Quantidade de Inclusões: ".$sumCliente->quantidadeInclusoes."<br>";
        echo "Quantidade de Alterações: ".$sumCliente->quantidadeAlteracoes;
    }catch(Exception $e){
        die($e->getMessage());
    }
    
    
?>