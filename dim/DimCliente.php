<?php

namespace dimensoes;
require_once('Cliente.php');
use dimensoes\Cliente;
use Exception;

mysqli_report(MYSQLI_REPORT_STRICT);

use mysqli;
use mysqli_sql_exception;

class DimCliente
{
    public function carregarDimCliente(){
        try{
            $connDimensao = $this->conectarBanco('dm_comercial');
        $connComercial = $this->conectarBanco('bd_comercial');
        }catch(\Exception $e){
            die($e->getMessage());
        }
        $sqlDim = $connDimensao->prepare('select SK_cluente, cpf, nome, sexo, idade, rua, bairro, cidade, uf from dim_cliente');
            $sqlDim->execute();
            $result = $sqlDim->get_result();

        if($result->num_rows !== 0){
            
        }else{
            
        }
        
    }
    private function conectarBanco($banco)
    {
        define('DS', DIRECTORY_SEPARATOR);
        define('BASE_DIR', dirname(__FILE__) . DS);
        require_once(BASE_DIR . 'config.php');
        try {
            $conn = new \mysqli($dbhost, $user, $password, $banco);
            return $conn;
        } catch (mysqli_sql_exception $e) {
            throw new \Exception($e);
            die;
        }
    }
}
