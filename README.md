# Sistema de Controle de Alunos e Notas em PHP

   Este é um sistema de controle de alunos que permite cadastrar alunos com nome, uma imagem do aluno, nota final, uma descrição do aluno.O sistema contém um pequeno dashboard com dados das notas dos alunos. O intuito desse sistema não é a implementação para ser usado como realmente um sistema para ser usado em escolas e afins, mas sim para aplicar conhecimentos em PHP.

## Tecnologias Utilizadas
- PHP
- HTML
- CSS
- Javascript
- PostgreSQL
- Bootstrap
- Chart.JS

## Funcionalidades
### Login e Cadastro Administrador
   O sistema permite fazer login do administrador e caso não tenha conta, permite cadastrar quantos quiser.
   #### Segurança:
   - Foi usado para comunicação com o Banco de Dados o PDO para previnir ataques de SQL Injection
   - Foi usado para criptografia da senha o bcrypt, através da função do PHP "password_hash"
### Cadastrar, Ler, Atualizar e Deletar Alunos
  O sistema permite fazer o CRUD(Create, Read, Update e Delete) com os alunos.
### Alterar Senha
  O sistema permite que o administrador altere sua senha
### Pesquisa por Aluno
  O sistema permite pesquisar pelo nome do aluno e retornar os resultados conforme a pesquisa por nome.
### Responsivo
  O sistema é responsivo para todos os tamanhos de tela, por conta do uso de boas práticas em CSS, uso de de Media Queries e do Bootstrap.

## Sobre o projeto
  Este projeto foi criado para colocar em prática alguns conhecimentos adquiridos em PHP, JS, HTML e CSS. Para desenvolver em PHP foi usado o servidor web Apache2 no Linux.
  
  Foram usados as linguagens de programação PHP e com um leve toque de Javascript e usando as tecnologias HTML5 e CSS3 com Bootstrap para o estilo e design. O banco de dados usado foi o PostgreSQL. Além de englobar conceitos e técnicas de layout do CSS3 como: Flexbox, Grid e Media Queries para responsividade.
  
  
## Dashboard
  O sistema possui um simples dashboard com dois gráficos feitos com Javascript usando uma biblioteca chamada Charts.JS, [clique aqui para ver biblioteca](https://www.chartjs.org/)
  
  ![FireShot Capture 008 - Painel - Controle de Alunos - localhost](https://user-images.githubusercontent.com/63206031/157270603-34f71adf-184f-4c90-824a-f6a9b62574f6.png)

## Layout

![FireShot Capture 010 - Cadastro Administrador - localhost](https://user-images.githubusercontent.com/63206031/157272632-bab35337-fdf1-4677-a86f-aef357fd0f87.png)

![FireShot Capture 006 - Painel - Controle de Alunos - localhost](https://user-images.githubusercontent.com/63206031/157272708-d1a874e7-9bde-453f-8751-7dd471653d0f.png)

![FireShot Capture 009 - Adicionar alunos - localhost](https://user-images.githubusercontent.com/63206031/157273338-2034d420-0149-42f0-b477-b1e913203d86.png)
 
 ## Sobre o Autor
Instagram: [gabrielhcardoso_](https://www.instagram.com/gabrielhcardoso_/)
LinkedIn: [gabrielcardos0](https://www.linkedin.com/in/gabrielcardos0/)
