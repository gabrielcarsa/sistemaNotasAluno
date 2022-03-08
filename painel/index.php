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

//Variáveis para contar quantidade de alunos Aprovados ou reprovados
$ap = $rep = 0;

//Váriavel para contar quantidade de alunos por intervalo de notas
$zero_dois = $tres_cinco = $seis_oito = $nove_dez = 0;

//Consulta de Alunos
$consulta = $conn->query("SELECT * FROM tb_aluno ORDER BY nome;");
$consulta->execute();
$qntd = $consulta->rowCount();

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

    <!--CHATS JS-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>Painel - Controle de Alunos</title>
</head>
<body>
    <?php include '../view/layout/navAdmin.php';?>
   
    <section class="dashboard">
        <h2>Dashboard</h2>
        <div>
            <div class="canvas-div">
                <canvas id="pieChart"></canvas>
            </div>
            <div class="canvas-div">
                <canvas id="barChart"></canvas>   
            </div>
        </div>
    </section>

    <hr>

    <section class="aluno">
        <h2>Alunos Cadastrados (<?php echo $qntd;?>)</h2>
        <div class="container-busca">
            <div>
                <a href="aluno/adicionar.php">Adicionar aluno</a>
            </div>
            <form action="pesquisa.php" class="form-pesquisa">
                <input type="search" autocomplete="off" name="search" placeholder="Pesquisar" aria-label="Pesquisar" class="form-control">
                <button type="submit" class="btn btn-primary mb-2">Pesquisar</button>
            </form>
        </div>
        <?php
            if(isset($_GET['delete'])){
                echo "
                <div class='alert alert-danger' role='alert'>
                    Aluno excluído com sucesso!
                </div>";
            }elseif(isset($_GET['update'])){
                echo "
                <div class='alert alert-primary' role='alert'>
                    Aluno alterado com sucesso!
                </div>";
            }elseif(isset($_GET['create'])){
                echo "
                <div class='alert alert-primary' role='alert'>
                    Aluno adicionado com sucesso!
                </div>";
            }
            
        ?>
        <div class="alunos">
            <?php
                if($consulta->rowCount() >= 1){
                    while ($linha = $consulta->fetch(PDO::FETCH_ASSOC)) {
                        $situacao = ($linha['nota_final'] >= 6) ? "Aprovado" : "Reprovado";
                        //Cálculo de Aprovados e Reprovados
                        if($situacao == "Aprovado"){
                            $ap++;
                        }else{
                            $rep++;
                        }
                        //Cálculo de Intervalo de Notas
                        if($linha['nota_final'] >= 0 && $linha['nota_final'] < 3){
                            $zero_dois++;
                        }elseif($linha['nota_final'] > 2 && $linha['nota_final'] < 6){
                            $tres_cinco++;
                        }elseif($linha['nota_final'] > 5 && $linha['nota_final'] < 9){
                            $seis_oito++;
                        }else{
                            $nove_dez++;
                        }
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
                                    <a id='delete-aluno' href='aluno/excluir.php?id_aluno_delete={$linha['id']}'>Excluir</a> 
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
                    
            ?>        
        </div>
    </section>
    <script>
        //Gráfico 1
        var xValues = ["Aprovados", "Reprovados"];
        var yValues = [<?php echo $ap;?>, <?php echo $rep;?>];
        var barColors = [
        "#7B1FA2",
        "#D32F2F"
        ];

        new Chart("pieChart", {
            type: "pie",
            data: {
                labels: xValues,
                datasets: [{
                backgroundColor: barColors,
                data: yValues
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "Gráfico Situação dos Alunos"
                }
            }
        });

        //Gráfico 2
        const ctx = document.getElementById('barChart');
        const barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Notas: 0-2', '3-5', '6-8', '9-10'],
                datasets: [{
                    label:'Quantidade de Alunos por Notas',
                    data: [<?php echo "$zero_dois, $tres_cinco, $seis_oito, $nove_dez"; ?>],
                    backgroundColor: [
                        'rgba(211,47,47,1)',
                        'rgba(255, 206, 86)',
                        'rgba(54, 162, 235)',
                        'rgba(75, 192, 192)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="../assets/js/admin-login.js"></script>
</body>

</html>