<?php

$precoUnitario = 0.50;
    $quantidade = 6;
    $valorTotal = $precoUnitario * $quantidade;
    echo $valorTotal;
    echo "<br>";
    echo "<br>";
    $pesoElefante = 2000;
    $pesoCoelho = 2;
    if($pesoCoelho > $pesoElefante){
        echo "O Coelho é mais pesado!!!";
    }else if($pesoElefante > $pesoCoelho){
        echo "O Elefante é mais pesado!!!";
    }else{
        echo "Ambos tem o mesmo peso";
    }
    echo "<br>";
    echo "<br>";
    for($i=0; $i<10; $i++){
        echo $i."<br>";
    }
    echo "<br>";
    echo "<br>";
    $j = 0;
    while ($j < 10) {
        echo $j."<br>";
        $j++;
    }
    echo "<br>";
    echo "<br>";
    $nomesUsuarios = array("Jorge","Alfredo","Matin");
    print_r($nomesUsuarios);
    echo "<br>";
    echo "<br>";
    $dadosUsuarios = array(
        array(
            "Nome" => "Paulo",
            "Idade" => 18,
            "Altura" => 178,
            
        ),array(
            "Nome" => "Jorge",
            "Idade" => 19,
            "Altura" => 185,
            
        ),array(
            "Nome" => "Joelma",
            "Idade" => 22,
            "Altura" => 168,
            
        )
    );
    print_r($dadosUsuarios);
    echo "<br>";
    echo "<br>";
    foreach($dadosUsuarios as $linha){
        $nome = $linha['Nome'];
        $idade = $linha['Idade'];
        $altura = $linha['Altura'];

        echo "Nome: ".$nome." - "."Idade: ".$idade." - "."Altura: ".$altura;
        echo "<br>";
    }
    ?>