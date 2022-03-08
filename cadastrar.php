<?php

require_once('config/conexao.php');

// Defina variáveis e inicialize com valores vazios
$nome = $senha = $confirmar_senha = $email = "";
$nome_err = $senha_err = $confirmar_senha_err = $email_err = "";

 
// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validar nome
    if(empty(trim($_POST["nome"]))){
        $nome_err = "Por favor coloque um nome.";
    } elseif(!preg_match('/^[a-zA-Z ]+$/', trim($_POST["nome"]))){
        $nome_err = "O nome pode conter apenas letras e espaços";
    } else{
        // Prepare uma declaração selecionada
        $sql = "SELECT id FROM tb_administrador WHERE nome = :nome";
        
        if($stmt = $conn->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_nome = trim($_POST["nome"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
               $nome = trim($_POST["nome"]);
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
    //validar email
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor coloque um email.";
    } elseif (filter_var ( trim($_POST["email"]), FILTER_VALIDATE_EMAIL ) ) {
         // Prepare uma declaração selecionada
         $sql = "SELECT id FROM tb_administrador WHERE email = :email";
        
         if($stmt = $conn->prepare($sql)){
             // Vincule as variáveis à instrução preparada como parâmetros
             $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
             
             // Definir parâmetros
             $param_email = trim($_POST["email"]);
             
             // Tente executar a declaração preparada
             if($stmt->execute()){
                 if($stmt->rowCount() == 1){
                     $email_err = "Este email já está em uso.";
                 } else{
                     $email = trim($_POST["email"]);
                 }
             } else{
                 echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
             }
 
             // Fechar declaração
             unset($stmt);
         }
    } else {
        $email_err = "Email inválido";

    }

    // Validar senha
    if(empty(trim($_POST["senha"]))){
        $senha_err = "Por favor insira uma senha.";     
    } elseif(strlen(trim($_POST["senha"])) < 6){
        $senha_err = "A senha deve ter pelo menos 6 caracteres.";
    } else{
        $senha = trim($_POST["senha"]);
    }

    // Validar e confirmar a senha
    if(empty(trim($_POST["confirmar_senha"]))){
        $confirmar_senha_err = "Por favor, confirme a senha.";     
    } else{
        $confirmar_senha = trim($_POST["confirmar_senha"]);
        if(empty($senha_err) && ($senha != $confirmar_senha)){
            $confirmar_senha_err = "A senha não confere.";
        }
    }

    //Pegar IP do Administrador que está se cadastrando
    $ip = $_SERVER['REMOTE_ADDR'];

    // Define o fuso horário de Brasilía
    date_default_timezone_set('America/Sao_Paulo');

    //Data de criação 
    $data_criacao = date('Y/m/d H:i:s', time());
    
    //Último Acesso, neste caso valor será definido como a data da criação e só será mudado após o usuário fazer login
    $ultimo_acesso = $data_criacao;

    // Verifique os erros de entrada antes de inserir no banco de dados
    if(empty($nome_err) && empty($email_err) && empty($senha_err) && empty($confirmar_senha_err)){
    
        // Prepare uma declaração de inserção
        $sql = "INSERT INTO tb_administrador (nome, email, senha, ultimo_ip, ultimo_acesso, criado_em) VALUES (:nome, :email, :senha, '".$ip."', '".$ultimo_acesso."', '".$data_criacao."')";
        
        if($stmt = $conn->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":senha", $param_senha, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_nome = $nome;
            $param_email = $email;
            $param_senha = password_hash($senha, PASSWORD_DEFAULT); // Cria um hash para a senha
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                // Redirecionar para a página de login
                header("location: index.php");
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }

    // Fechar conexão
    unset($conn);
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
    <link rel="stylesheet" href="assets/css/master.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css" />

    <!--GOOGLE FONTS-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins" rel="stylesheet">

    <title>Cadastro Administrador</title>
  
</head>
<body class="body-login">
    <h1>Sistema de cadastro de alunos - SCA</h1>
    <section class="login">
        <h2>Cadastro de Administrador</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                <span class="invalid-feedback"><?php echo $nome_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $senha_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirme a senha</label>
                <input type="password" name="confirmar_senha" class="form-control <?php echo (!empty($confirmar_senha_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirmar_senha_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" id="btn-entrar-cad" value="Criar Conta">
                <input type="reset" class="btn btn-secondary ml-2" id="btn-entrar-cad1" value="Apagar Dados">
            </div>
            <p>Já tem uma conta? <a href="index.php">Entre aqui</a>.</p>
        </form>
    </section>    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/2913b00e59.js" crossorigin="anonymous"></script>
</body>
</html>