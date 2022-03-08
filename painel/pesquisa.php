<?php
// Inicialize a sessão
session_start();
 
// Verifique se o usuário está logado, se não, redirecione-o para uma página de login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../index.php");
    exit;
}

// Incluir arquivo de configuração
require_once('../config/conexao.php');

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(!preg_match('/[a-zA-Z\u00C0-\u00FF ]+/i', trim($_GET["search"]))){
        $resultado = "Nada";
    } else{
        $pesquisa = $_GET['search'];
    }
       
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!--STYLE-->
    <link rel="stylesheet" href="../assets/css/master.css">

    <!--GOOGLE FONTS-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins" rel="stylesheet">

    <title>Pesquisa por Aluno</title>
</head>
<body>
    <?php include '../view/layout/navAdmin.php';?>
    <section class="aluno" style="margin-top: 100px;">
        <h2>Resultados por '<?php echo $pesquisa;?>'</h2>
        <div class="container-busca">
            <a href="javascript:history.back()">Voltar</a>
            <form action="pesquisa.php" class="form-pesquisa">
                <input type="search" autocomplete="off" name="search" placeholder="Pesquisar" aria-label="Pesquisar" class="form-control">
                <button type="submit" class="btn btn-primary mb-2">Pesquisar</button>
            </form>
        </div>
        <div class="alunos">
            <?php
                $sql = "SELECT * FROM tb_aluno WHERE nome ILIKE :search";
                $consulta = $conn->prepare($sql);

                $pesquisa_param = '%'.$pesquisa.'%';
                $consulta->bindParam(":search", $pesquisa_param, PDO::PARAM_STR);
                    
                $consulta->execute();
                if($consulta->rowCount() >= 1){
                    while ($linha = $consulta->fetch(PDO::FETCH_ASSOC)) {
                        echo "
                            <div class='aluno-card'>
                                <h3>{$linha['nome']}</h3>
                                <img src={$linha['img_principal']}> <br>
                                <h4>Avaliação</h4>
                                <p>{$linha['avaliacao']}</p>
                                <h4>Nota Final</h4>
                                <p>{$linha['nota_final']}</p>
                                <h4>Data e hora criada</h4>
                                <p>".date('d/m/Y H:i:s', strtotime($linha['data_cadastro']))."</p>
                                <h4>Situação</h4>
                                <p id='$situacao'>$situacao</p>
                                <div>
                                    <a href='aluno/alterar.php?id={$linha['id']}'>Alterar</a>
                                    <a id='delete-aluno' href='?id_aluno_delete={$linha['id']}'>Excluir</a> 
                                </div>
                            </div>";
                    }
                        
                } else{
                        echo "
                        <div class='alert alert-warning' role='alert'>
                            Nenhum aluno cadastrado. Seus alunos devem aparecer aqui!
                        </div>
                        ";
                    }
                // Fechar declaração
                unset($consulta);
                
            ?>        
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="../assets/js/admin-login.js"></script>
</body>
</html>

