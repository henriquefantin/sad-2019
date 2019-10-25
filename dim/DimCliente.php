<?php

namespace dimensoes;

require_once('Cliente.php');
require_once('Sumario.php');

use dimensoes\Cliente;
use dimensoes\Sumario;
use Exception;

mysqli_report(MYSQLI_REPORT_STRICT);

use mysqli;
use mysqli_sql_exception;

class DimCliente
{
    public function carregarDimCliente()
    {
        $dataAtual = date('Y-m-d');
        $sumario = new Sumario;
        try {
            $connDimensao = $this->conectarBanco('dm_comercial');
            $connComercial = $this->conectarBanco('bd_comercial');
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $sqlDim = $connDimensao->prepare('select SK_cliente, cpf, nome, sexo, idade, rua, bairro, cidade, uf from dim_cliente');
        $sqlDim->execute();
        $result = $sqlDim->get_result();

        if ($result->num_rows === 0) {
            $sqlComercial = $connComercial->prepare("select * from cliente"); //cria variavel com comando sql
            $sqlComercial->execute(); //executa comando sql
            $resultComercial = $sqlComercial->get_result(); //atribui a variavel o resultado da consulta
            if ($resultComercial->num_rows !== 0) { //tresta se a cpnsulta retornou dados
                while ($linhaCliente = $resultComercial->fetch_assoc()) { // atribui a variavel cada linha a te o fim do loop
                    $cliente = new Cliente();
                    $cliente->setCliente(
                        $linhaCliente['cpf'],
                        $linhaCliente['nome'],
                        $linhaCliente['sexo'],
                        $linhaCliente['idade'],
                        $linhaCliente['rua'],
                        $linhaCliente['bairro'],
                        $linhaCliente['cidade'],
                        $linhaCliente['uf']
                    );
                    $sqlInsertDim = $connDimensao->prepare("insert into dim_cliente (cpf, nome, sexo, idade, rua, bairro, cidade, uf, data_ini) values (?,?,?,?,?,?,?,?,?)");
                    $sqlInsertDim->bind_param("sssisssss", $cliente->cpf, $cliente->nome, $cliente->sexo, $cliente->idade, $cliente->rua, $cliente->bairro, $cliente->cidade, $cliente->uf, $dataAtual);
                    $sqlInsertDim->execute();
                    $sumario->setQuantidadeInclusoes();
                }
                $sqlComercial->close();
                $sqlDim->close();
                $sqlInsertDim->close();
                $connComercial->close();
                $connDimensao->close();
            }
        } else { //dimecao ja contem dados
            $sqlComercial = $connComercial->prepare('select * from cliente');
            $sqlComercial->execute();
            $resultComercial = $sqlComercial->get_result();
            while ($linhaComercial = $resultComercial->fetch_assoc()) { //percorrendo as linhas da tabela cliente e colocand o resultado em $linhaComercial

                $sqlDim = $connDimensao->prepare('SELECT SK_cliente, nome, cpf, sexo, idade, bairro, cidade, uf 
                                                  FROM dim_cliente WHERE cpf = ? AND data_fim IS NULL');
                $sqlDim->bind_param("s", $linhaComercial['cpf']);
                $sqlDim->execute();
                $resultDim = $sqlDim->get_result();
                if ($resultDim->num_rows === 0) { //o cliente nao esta na dimensao
                    $sqlInsertDim = $connDimensao->prepare('INSERT INTO dim_cliente (cpf, nome, sexo, idade, rua, bairro, cidade, uf, data_ini) VALUES (?,?,?,?,?,?,?,?,?)');
                    $sqlInsertDim->bind_param('sssisssss', $linhaComercial['cpf'], $linhaComercial['nome'], $linhaComercial['sexo'], $linhaComercial['idade'], $linhaComercial['rua'], $linhaComercial['bairro'], $linhaComercial['cidade'], $linhaComercial['uf'], $dataAtual);
                    $sqlInsertDim->execute();
                    if ($sqlInsertDim->error) {
                        throw new \Exception('Erro: Cliente novo não incluso');
                    }
                    $sumario->setQuantidadeInclusoes();

                    $sqlComercial->close();
                    $sqlDim->close();
                    $sqlInsertDim->close();
                    $connDimensao->close();
                    $connComercial->close();
                } else { //o cliente da comenrcial ja esta na dimensional
                    $strComercialTeste = $linhaComercial['cpf'] . $linhaComercial['nome'] . $linhaComercial['sexo'] . $linhaComercial['idade'] . $linhaComercial['rua'] . $linhaComercial['bairro'] . $linhaComercial['cidade'] . $linhaComercial['uf'];
                    $linhaDim = $resultDim->fetch_assoc();
                    $strDimencionalTeste = $linhaDim['cpf'] . $linhaDim['nome'] . $linhaDim['sexo'] . $linhaDim['idade'] . $linhaDim['rua'] . $linhaDim['bairro'] . $linhaDim['cidade'] . $linhaDim['uf'];
                    if ($this->strIgual($strComercialTeste, $strDimencionalTeste)) {
                        $sqlUpdateDim = $connDimensao->prepare('UPDATE dim_cliente SET data_fim = ? WHERE SK_cliente = ?');
                        $sqlUpdateDim->bind_param('si', $dataAtual, $linhaDim['SK_cliente']);
                        $sqlUpdateDim->execute();
                        if (!$sqlUpdateDim->error) {
                            $sqlInsertDim = $connDimensao->prepare('INSERT INTO dim_cliente (cpf, nome, sexo, idade, rua, bairro, cidade, uf, data_ini) VALUES (?,?,?,?,?,?,?,?,?)');
                            $sqlInsertDim->bind_param('sssisssss', $linhaComercial['cpf'], $linhaComercial['nome'], $linhaComercial['sexo'], $linhaComercial['idade'], $linhaComercial['rua'], $linhaComercial['bairro'], $linhaComercial['cidade'], $linhaComercial['uf'], $dataAtual);
                            $sqlInsertDim->execute();
                            $sumario->setQuantidadeAlteracoes();
                        }else {
                            throw new \Exception('Erro: Erro no processo de alteração!');
                        } 
                    }
                }
            }
        }

        return $sumario;
    }

    private function strIgual($strAtual, $strNova)
    {
        $hashAtual = md5($strAtual);
        $hashNovo = md5($strNova);
        if ($hashAtual === $hashNovo) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function conectarBanco($banco)
    {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        if (!defined('BASE_DIR')) {
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
