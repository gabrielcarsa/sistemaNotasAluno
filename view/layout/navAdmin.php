<nav class="navbar navbar-expand-lg fixed-top navbar-dark">
  <a class="navbar-brand" href="#">Painel SCA</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse collapse justify-content-end" id="navbarSupportedContent">
    <ul class="navbar-nav pull-right">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Conta
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <p class="dropdown-item">Olá, <b><?php echo htmlspecialchars($_SESSION["nome"]); ?></b></p>
          <a class="dropdown-item" href="redefinir-senha.php">Alterar Senha</a>
          <a class="dropdown-item" href="sair.php">Sair</a>
        </div>
      </li>
  </div>
</nav>