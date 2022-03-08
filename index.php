<?php
// Inicialize a sessão
session_start();
 
// Verifique se o usuário já está logado, em caso afirmativo, redirecione-o para a página de boas-vindas
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: painel/index.php");
    exit;
}
 
// Incluir arquivo de configuração
require_once('config/conexao.php');
 
// Defina variáveis e inicialize com valores vazios
$email = $senha = "";
$email_err = $senha_err = $login_err = "";
 
// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Verifique se o email está vazio
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor, insira o email.";
    } elseif (filter_var ( trim($_POST["email"]), FILTER_VALIDATE_EMAIL ) ) {
        $email = trim($_POST["email"]);
    }else{
        $login_err = "Email ou senha inválidos.";
    }
    
    // Verifique se a senha está vazia
    if(empty(trim($_POST["senha"]))){
        $senha_err = "Por favor, insira sua senha.";
    } else{
        $senha = trim($_POST["senha"]);
    }
    
    // Validar credenciais
    if(empty($email_err) && empty($senha_err)){
        // Prepare uma declaração selecionada
        $sql = "SELECT id, nome, email, senha FROM tb_administrador WHERE email = :email";
        
        if($stmt = $conn->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_email = trim($_POST["email"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                // Verifique se o email existe, se sim, verifique a senha
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $nome = $row["nome"];
                        $email = $row["email"];
                        $hash_senha = $row["senha"];
                        if(password_verify($senha, $hash_senha)){

                            // A senha está correta, então inicie uma nova sessão
                            session_start();
                            
                            // Armazene dados em variáveis de sessão
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nome"] = $nome;
                            $_SESSION["email"] = $email;  
                            
                            //Alterando último acesso do usuário                            
                            $sql_ultimoAcesso = "UPDATE tb_administrador SET ultimo_acesso = :data_atual WHERE id = :id";
  
                            // Vincule as variáveis à instrução preparada como parâmetros
                            if($stmt = $conn->prepare($sql_ultimoAcesso)){
                                // Define o fuso horário de Brasilía
                                date_default_timezone_set('America/Sao_Paulo');
                                
                                $data_ultimoAcesso = date('Y/m/d H:i:s', time());

                                $stmt->bindValue(":data_atual", $data_ultimoAcesso);
                                $stmt->bindValue(":id", $_SESSION["id"]);

                                if($stmt->execute()){
                                    //Deu certo
                                }else{
                                    echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
                                }
                            }
                                                    
                            // Redirecionar o usuário para a página de boas-vindas
                            header("location: painel/index.php");
                        } else{
                            // A senha não é válida, exibe uma mensagem de erro genérica
                            $login_err = "Email ou senha inválidos.";
                        }
                    }
                } else{
                    // O Email não existe, exibe uma mensagem de erro genérica
                    $login_err = "Email ou senha inválidos.";
                }
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

    <title>Login - SCA</title>
   
</head>
<body class="body-login">
    <h1>Sistema de cadastro de alunos - SCA</h1>
    <section class="login">
        <h2>Login</h2>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Email</label>
                <input type="text" name="email" autocomplete="off" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div>
                <label>Senha</label>
                <input type="password" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $senha_err; ?></span>
            </div>
            <div>
                <input type="submit" class="btn btn-primary" id="btn-entrar" value="Entrar">
            </div>
            <p>Não tem uma conta? <a href="cadastrar.php">Inscreva-se agora</a>.</p>
        </form>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>
    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/2913b00e59.js" crossorigin="anonymous"></script>
</body>
</html>