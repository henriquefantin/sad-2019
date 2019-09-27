<?php

    echo calcular(8, 16.98);
    echo "<br>";
    echo fatorial(5,1);

    function calcular($quantidade, $precoUnit){
        return $quantidade*$precoUnit;
    }

    function fatorial($num,$fator){
        for ($i=1; $i<=$num; $i++) {
            $fator=$fator*$i;
        }
        echo "Fator de $num é $fator";
    }




?>