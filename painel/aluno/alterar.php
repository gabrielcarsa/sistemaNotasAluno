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

$nome = $avaliacao = "";
$nome_err = $avaliacao_err = $file_err = "";

$consulta = $conn->prepare("SELECT * FROM tb_aluno WHERE id = :id_uso");
$param_id_uso = trim($_GET['id']);
$consulta->bindParam(':id_uso', $param_id_uso);
$consulta->execute();
while ($linha = $consulta->fetch(PDO::FETCH_ASSOC)) {
    $nome = $linha['nome'];
    $avaliacao = $linha['avaliacao'];
    $nota = $linha['nota_final'];
}

// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){

    //Verficar campo nome
    if(empty(trim($_POST["nome"]))){
        $nome_err = "Por favor insira um nome para aluno.";     
    }elseif(!preg_match('/[a-zA-Z\u00C0-\u00FF ]+/i', trim($_POST["nome"]))){
        $nome_err = "O nome pode conter apenas letras e espaços";
    }else{
        $sql = "SELECT id FROM tb_aluno WHERE nome = :nome AND id != :id_uso";
        
        if($stmt = $conn->prepare($sql)){
            // Definir parâmetros
            $param_nome = trim($_POST["nome"]);
            $param_id_uso = trim($_GET['id']);
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            $stmt->bindParam(":id_uso", $param_id_uso, PDO::PARAM_STR);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $nome_err = "Este nome de aluno já existe.";
                } else{
                    $nome = trim($_POST["nome"]);
                }
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
    
    //Verifica o campo avaliação
    if(empty(trim($_POST["avaliacao"]))){
        $avaliacao_err = "Por favor insira uma avaliação.";     
    }else{
        $avaliacao = trim($_POST["avaliacao"]);
    }

    //Verifica o campo nota
    if(empty(trim($_POST["nota"]))){
        $nota_err = "Por favor insira uma nota.";     
    }elseif(trim($_POST["nota"]) > 10 || trim($_POST["nota"]) < 0){
        $nota_err = "Por favor insira uma nota entre 0 e 10.";     
    }else{
        $nota = trim($_POST["nota"]);
    }

    //Verifica input file
    if ($_FILES['img_principal']['size'] > 0) {
        $file_err ="";
    } else {
        $file_err = "Envie uma imagem do formato JPG ou PNG!";
    }
   
    if(empty($nome_err) && empty($avaliacao_err) && empty($nota_err) && empty($file_err)){//Validar se campos estão preenchidos
        $img_principal = $_FILES['img_principal'];
        if($img_principal['size'] > 2097152){
            //Tamanho máximo de arquivo permitido
            $file_err = "Arquivo muito grande! Max: 2MB";
        }elseif($img_principal['error']){
            //Se houver algum tipo de erro
            $file_err = "Falha ao enviar! Nenhum arquivo recebido";
        }else{
            //Deletando img antiga
            $arquivo = "";
            $consulta = $conn->prepare("SELECT img_principal FROM tb_aluno WHERE id = :id");
            $consulta->bindParam(':id', $param_id_uso);
            $consulta->execute();

            while ($linha = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $arquivo = "../".$linha['img_principal'];
            }

            if(file_exists( $arquivo )){
                unlink($arquivo);
            }

            $diretorio = "../upload/";//define diretorio
            $nome_img_principal = $img_principal['name'];//Variável recebe o nome da aluno
            $novoNome_img_principal = preg_replace('/\s+/', '',$nome);//Tira qualquer espaços do nome
            $extensao = strtolower(pathinfo($nome_img_principal, PATHINFO_EXTENSION));//Retira o tipo de arquivo

            if($extensao != "jpg" && $extensao != "png"){
                //Verifica o tipo de arquivos suportados
                $file_err = "Tipo de arquivo não aceito, apenas JPG e PNG";
            }else{
               
                $caminho_img_principal = $diretorio.$novoNome_img_principal.".".$extensao;//Define o caminho para o diretório
                $status = move_uploaded_file($img_principal['tmp_name'], $caminho_img_principal);//efetua o upload
                
                if(!$status){
                    //Caso algum erro, a variavel $status retorna um booleano
                    echo "Falha ao enviar";
                }

                $sql = "UPDATE tb_aluno SET nome = :nome, avaliacao = :avaliacao, nota_final = :nota_final, img_principal = :img_principal WHERE id = :id";
                    
                if($stmt = $conn->prepare($sql)){
                    //Definindo parâmetros
                    $nome_aluno = trim($_POST["nome"]);
                    $avaliacao_aluno = trim($_POST["avaliacao"]);
                    $nota_aluno = trim($_POST["nota"]);

                    //Alterando o caminho para salvar no Banco de Dados
                    $caminho_img_principal = "upload/".$novoNome_img_principal.".".$extensao;

                    // Vincule as variáveis à instrução preparada como parâmetros
                    $stmt->bindValue(":nome", $nome_aluno);
                    $stmt->bindValue(":avaliacao", $avaliacao_aluno);
                    $stmt->bindValue(":nota_final", $nota_aluno);
                    $stmt->bindValue(":img_principal", $caminho_img_principal);
                    $stmt->bindValue(":id", $param_id_uso);
            
                    // Tente executar a declaração preparada
                    if($stmt->execute()){
                        header("location: ../index.php?update=true");

                    } else{
                        echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
                    }
            
                    // Fechar declaração
                    unset($stmt);
                }
            }
        
        }
        
    }
    
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--BOOTSTRAP-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!--STYLE-->
    <link rel="stylesheet" href="../../assets/css/master.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css" />

    <!--GOOGLE FONTS-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins" rel="stylesheet">

    <title>Alterar - <?php echo $nome;?></title>
</head>
<body class="body-login">
    <section class="login">
        <div class="voltar">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
        <h2>Alterar aluno</h2>
        <div>
            <p>Obs.: Para alterar aluno é necessário fazer upload de outra imagem principal!</p>
        </div>
        <form method="post" enctype="multipart/form-data">
            <div class="form-add">
                <label>Nome</label>
                <input type="text" name="nome" placeholder="Ex.: Camisetas Masculinas" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" autocomplete="off" value="<?php echo $nome; ?>">
                <span class="invalid-feedback"><?php echo $nome_err; ?></span>
            </div>    
            <div class="form-add">
                <label>Avaliação</label>
                <input type="text" name="avaliacao" autocomplete="off" class="form-control <?php echo (!empty($avaliacao_err)) ? 'is-invalid' : ''; ?>" placeholder="Ex.: Todos os tipos de camisetas masculinas..." value="<?php echo $avaliacao; ?>">
                <span class="invalid-feedback"><?php echo $avaliacao_err; ?></span>
            </div>    
            <div class="form-add">
                <label>Nota Final</label>
                <input type="number" name="nota" step="any" autocomplete="off" class="form-control <?php echo (!empty($nota_err)) ? 'is-invalid' : ''; ?>" placeholder="Ex.: 6.75" value="<?php echo $nota; ?>">
                <span class="invalid-feedback"><?php echo $nota_err; ?></span>
            </div>  
            <div class="input-file">
                <label>Imagem Principal</label>
                <input name="img_principal" type="file">
                <span><?php echo $file_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" id="btn-entrar" value="Salvar">
            </div>
        </form>
    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/2913b00e59.js" crossorigin="anonymous"></script>
</body>
</html>