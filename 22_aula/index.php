<?php
// index.php
// --- 1. CONEXÃO COM O BANCO DE DADOS USANDO PDO ---
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');
// DSN (Data Source Name) - define a fonte de dados para a conexão PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
    // Criamos a conexão PDO dentro de um bloco try...catch
    $pdo = new PDO($dsn, $user, $pass);
    // Configuramos o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se a conexão falhar, o script é interrompido e uma mensagem de erro é exibida
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}

// --- 2. CRIAÇÃO AUTOMÁTICA DA TABELA ---
// Este código garante que a tabela 'tarefas' exista.
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tarefas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
            ");
} catch (PDOException $e) {
    die("Erro ao criar a tabela: " . $e->getMessage());
}
// Restante do código virá aqui...

// --- 3. LÓGICA DA APLICAÇÃO (CRUD) ---
$erro = '';
try {
    // Lógica para ADICIONAR uma nova tarefa
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tarefa'])) {
        if (!empty($_POST['titulo_tarefa'])) {
            $titulo = $_POST['titulo_tarefa'];
            // :titulo é um "placeholder" (parâmetro nomeado).
            // Usar prepared statements previne ataques de SQL Injection.
            $sql = "INSERT INTO tarefas (titulo) VALUES (:titulo)";
            $stmt = $pdo->prepare($sql);
            // Executa a query passando os parâmetros em um array
            $stmt->execute(['titulo' => $titulo]);
            header("Location: index.php"); // Redireciona para evitar reenvio
            exit();
        } else {
            $erro = "O título da tarefa não pode estar vazio.";
        }
    }
    // Lógica para EXCLUIR uma tarefa
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tarefa'])) {
        $id = $_POST['id_tarefa'];
        $sql = "DELETE FROM tarefas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $erro = "Ocorreu um erro: " . $e->getMessage();
}
// --- 4. BUSCAR TODAS AS TAREFAS PARA EXIBIÇÃO ---
$stmt = $pdo->query("SELECT * FROM tarefas ORDER BY criada_em DESC");
$tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas | Railway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5" style="max-width: 600px;">
        <header class="text-center mb-4">
            <h1 class="display-5 fw-bold">📝 Lista de Tarefas</h1>
            <p class="text-body-secondary">Criada com PHP, MySQL e deploy no Railway</p>
        </header>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="index.php" method="POST">
                    <div class="input-group">
                        <input type="text" name="titulo_tarefa" class="form-control" placeholder="Qual é a próxima tarefa?" required>
                        <button class="btn btn-primary" type="submit" name="add_tarefa">Adicionar</button>
                    </div>
                    <?php if ($erro) : ?>
                        <div class="text-danger small mt-2"><?php echo $erro; ?></div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div>
            <?php if (count($tarefas) > 0) : ?>
                    <ul class="list-group shadow-sm">
                        <?php foreach ($tarefas as $tarefa) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?php echo htmlspecialchars($tarefa['titulo']); ?></span>
                                <form action="index.php" method="POST" class="ms-2">
                                    <input type="hidden" name="id_tarefa" value="<?php echo $tarefa['id'];
                                                                                    ?>">
                                    <button type="submit" name="delete_tarefa" class="btn btn-sm btn-outline-danger">✖</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
            <?php else : ?>
                    <div class="text-center text-body-secondary p-4">
                        <p>Nenhuma tarefa na lista. Adicione uma acima!</p>
                    </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>