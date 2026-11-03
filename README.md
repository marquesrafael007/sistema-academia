# Sistema de academia — PHP puro e MySQL

Este projeto usa MVC sem framework e sem Composer. A chave `alunos.plano_id` referencia `planos.id`. “Categorias” foi interpretado como um engano no enunciado.

## Como funciona

- **Model:** conversa com o banco usando PDO e consultas preparadas.
- **View:** desenha o HTML com Bootstrap e escapa os textos cadastrados.
- **Controller:** recebe o formulário, valida os dados, chama o model e escolhe a view.
- **index.php:** é o porteiro do site. Escolhe uma ação permitida e exige login para a gestão.
- **api:** devolve JSON e reutiliza os mesmos models e validações.

Exemplo: `index.php?controller=aluno&acao=listar` chama `AlunoController::listar()`, que consulta `Aluno` e abre `views/alunos/index.php`.

## Instalação no XAMPP

1. Inicie **Apache** e **MySQL** no painel do XAMPP.
2. Copie esta pasta para `C:\xampp\htdocs\sistema-academia`.
3. Abra `http://localhost/phpmyadmin`, clique em **Importar**, selecione **banco.sql** e execute. Faça isso uma vez em uma instalação nova: o script não apaga tabelas existentes.
4. Confira **config/Database.php**: banco `academia`, host `127.0.0.1`, usuário `root`, senha vazia. Altere se sua instalação usar outras credenciais.
5. Abra `http://localhost/sistema-academia/index.php`.
6. Entre com **admin@academia.com** e senha **Academia@123**.
7. Use **Planos** para cadastrar, editar e excluir planos; use **Alunos** para cadastrar alunos escolhendo um plano no select.

O banco tem cinco usuários, cinco planos e cinco alunos. Os cinco usuários de exemplo usam a mesma senha didática e têm acesso à gestão; não há níveis de permissão neste escopo. A senha foi convertida em hash com `password_hash`; o login verifica com `password_verify`.

Nesta sessão o banco **academia** já foi importado no MySQL local para testes. Se estiver usando esse mesmo MySQL, não importe novamente.

O código é compatível com PHP 7.3 ou superior, com PDO MySQL habilitado. Para uma instalação nova, prefira uma versão mantida do PHP. A aparência Bootstrap precisa de internet para carregar o CDN.

Alternativa sem mover a pasta: no terminal aberto neste projeto execute:
```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8000 -t .
```
Mantenha o MySQL iniciado e abra `http://127.0.0.1:8000/index.php`. Encerre com Ctrl+C.

## Testar a API no navegador

Usando Apache/XAMPP:

- Todos os alunos: `http://localhost/sistema-academia/api/alunos.php`
- Aluno 1: `http://localhost/sistema-academia/api/alunos.php?id=1`
- Planos: `http://localhost/sistema-academia/api/planos.php`

O navegador faz GET. A lista de alunos contém `plano_nome`; cada aluno contém `ativo` como booleano e IDs/idade como inteiros. O valor do plano vem como string decimal, por exemplo `"99.90"`, para preservar o valor monetário.

## Cadastrar pelo Postman

1. Escolha **POST**.
2. Use `http://localhost/sistema-academia/api/alunos.php`.
3. Em **Body**, escolha **raw** e **JSON**.
4. Confira o cabeçalho `Content-Type: application/json`.
5. Envie:
```json
{
  "nome": "Fernando Oliveira",
  "idade": 26,
  "ativo": true,
  "telefone": "(11) 98888-7777",
  "imagem_url": "",
  "plano_id": 1
}
```
O retorno será **201 Created**, com o aluno criado e seu ID. A imagem é opcional: informe uma URL http/https ou deixe vazia. Não há upload de arquivos.

Testes de erro:

| Teste | Status |
|---|---|
| GET com `?id=abc` | 400 |
| GET com ID positivo inexistente | 404 |
| POST com JSON quebrado, nome vazio ou plano inexistente | 400 |
| PUT em alunos ou POST em planos | 405 |
| OPTIONS nos endpoints | 204 |
| Banco indisponível | 500 |

Erros retornam objetos JSON, por exemplo `{"erro":"Aluno não encontrado."}`. Erros de formulário incluem `detalhes`. A API não revela a mensagem interna do banco; ela vai para o log do PHP.

Para verificar 500 manualmente em ambiente local, pare temporariamente o MySQL, faça GET na API e inicie o MySQL novamente.

## Usar no Flutter

No emulador Android, o computador costuma ser acessível por `10.0.2.2`: use `http://10.0.2.2/sistema-academia/api/alunos.php` se estiver no Apache padrão. Em aparelho físico, use o IP do computador na mesma rede. `localhost` no aparelho aponta para o próprio aparelho.

A API foi implementada pública, sem sessão, conforme os endpoints pedidos, com CORS `*`. Assim, qualquer cliente com acesso ao servidor pode listar e cadastrar alunos. Antes de disponibilizar dados reais na internet, implemente autenticação própria para a API.

## Regras e proteção dos formulários

- Nome obrigatório; idade inteira de 1 a 120; telefone obrigatório; plano existente.
- Valores de plano não negativos e com até duas casas decimais.
- Nome limitado a 100 bytes no servidor (acentos podem ocupar mais de um byte).
- Plano com alunos não pode ser excluído. Primeiro troque o plano desses alunos.
- Excluir e sair usam POST; token CSRF protege os formulários.
- Senhas são hashes; a sessão recebe um novo identificador ao entrar.
- Todo SQL da aplicação usa `prepare` e `execute`.
- Se houver erro de sessão, confira se o PHP/Apache pode gravar no `session.save_path` do php.ini.

## Arquivos em uso

```text
sistema-academia/
├── index.php
├── banco.sql
├── README.md
├── CODIGO_COMPLETO.md
├── config/
│   ├── Database.php
│   ├── bootstrap.php
│   ├── validacao.php
│   └── web.php
├── models/
│   ├── Usuario.php
│   ├── Aluno.php
│   └── Plano.php
├── controllers/
│   ├── UsuarioController.php
│   ├── AlunoController.php
│   └── PlanoController.php
├── views/
│   ├── login.php
│   ├── dashboard.php
│   ├── erro.php
│   ├── partials/
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── erros.php
│   ├── alunos/
│   │   ├── index.php
│   │   └── form.php
│   └── planos/
│       ├── index.php
│       └── form.php
└── api/
    ├── bootstrap.php
    ├── alunos.php
    └── planos.php
```

Foram preservados os arquivos antigos de biblioteca já existentes: `models/Categoria.php`, `models/Livro.php`, `controllers/AuthController.php`, `controllers/CategoriaController.php`, `controllers/LivroController.php` e `views/categorias/{index,form}.php`. Eles não são carregados nem usados pelo sistema de academia.

## Código completo, arquivo por arquivo

Abra **CODIGO_COMPLETO.md**. Ele reúne o SQL e todo o código do sistema novo, em blocos separados, com uma explicação antes de cada arquivo. Os arquivos PHP também estão prontos nas respectivas pastas; não é necessário copiar código desse documento para rodar.
