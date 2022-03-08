<?php
// Inicialize a sessão
session_start();
 
// Verifique se o usuário está logado, se não, redirecione-o para uma página de login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../../index.php");
    exit;
}

// Incluir arquivo de configuração
require_once('../../config/conexao.php');

//Excluir aluno
if(!empty($_GET)){
    try {
        //deletando img 
        $id_del = $_GET['id_aluno_delete'];

        $consulta = $conn->prepare("SELECT img_principal FROM tb_aluno WHERE id = :id");
        $consulta->bindParam(':id', $id_del);
        $consulta->execute();

        while ($linha = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $arquivo = "../".$linha['img_principal'];
        }
        if(file_exists( $arquivo )){
            unlink($arquivo);
            //deletando do banco de dados
            $stmt = $conn->prepare('DELETE FROM tb_aluno WHERE id = :id');
            $stmt->bindParam(':id', $id_del);
            $stmt->execute();

            header('Location: ../index.php?delete=true');

        }else{
            echo "
            <div class='alert alert-danger' role='alert'>
                Não foi possível excluir Aluno!
            </div>";
        }

    }catch(PDOException $e) {
        //echo 'Error: ' . $e->getMessage();
        echo "
        <div class='alert alert-danger' role='alert'>
            Não foi possível excluir Aluno!
        </div>";
    }
}
