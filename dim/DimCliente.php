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
        $dataAtual = date('Y-m-d');
        try{
            $connDimensao = $this->conectarBanco('dm_comercial');
        $connComercial = $this->conectarBanco('bd_comercial');
        }catch(\Exception $e){
            die($e->getMessage());
        }
        $sqlDim = $connDimensao->prepare('select SK_cliente, cpf, nome, sexo, idade, rua, bairro, cidade, uf from dim_cliente');
            $sqlDim->execute();
            $result = $sqlDim->get_result();

        if($result->num_rows === 0){
            $sqlComercial = $connComercial->prepare("select * from cliente"); //cria variavel com comando sql
            $sqlComercial->execute();//executa comando sql
            $resultComercial = $sqlComercial->get_result(); //atribui a variavel o resultado da consulta
            if($resultComercial->num_rows !== 0){//tresta se a cpnsulta retornou dados
                while ($linhaCliente = $resultComercial->fetch_assoc()) { // atribui a variavel cada linha a te o fim do loop
                    $cliente = new Cliente();
                    $cliente->setCliente($linhaCliente['cpf'], $linhaCliente['nome'], $linhaCliente['sexo'], $linhaCliente['idade'], $linhaCliente['rua'],
                     $linhaCliente['bairro'], $linhaCliente['cidade'], $linhaCliente['uf']);
                     $sqlInsertDim = $connDimensao->prepare("insert into dim_cliente (cpf, nome, sexo, idade, rua, bairro, cidade, uf, data_ini) values (?,?,?,?,?,?,?,?,?)");
                    $sqlInsertDim->bind_param("sssisssss", $cliente->cpf, $cliente->nome, $cliente->sexo, $cliente->idade, $cliente->rua, $cliente->bairro, $cliente->cidade, $cliente->uf, $dataAtual);
                    $sqlInsertDim->execute();
                }
                $sqlComercial->close();
                $sqlDim->close();
                $sqlInsertDim->close();
                $connComercial->close();
                $connDimensao->close();
            }

        }else{
            
        }
        
    }
    private function conectarBanco($banco)
    {
        if(!defined('DS')){
            define('DS', DIRECTORY_SEPARATOR);
        }
        if(!defined('BASE_DIR')){
            define('BASE_DIR', dirname(__FILE__) . DS);
        }
        
        require(BASE_DIR . 'config.php');
        try {
            $conn = new \MYSQLI($dbhost, $user, $password, $banco);
            return $conn;
        } catch (mysqli_sql_exception $e) {
            throw new \Exception($e);
            die;
        }
    }
}
