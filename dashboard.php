<?php
session_start();

// Verifica se o usuário está logado
if (isset($_SESSION['logado']) && $_SESSION['logado'] == 1) {
    // Conecte-se ao banco de dados
    $conexao = mysqli_connect("localhost", "root", "", "torcedores");
    
    // Verifica se a conexão foi bem sucedida
    if (mysqli_connect_errno()) {
        echo "Falha ao conectar ao MySQL: " . mysqli_connect_error();
        exit();
    }

    // Variáveis para controlar a exibição dos modais
    $showEditModal = false;
    $showDeleteModal = false;
    $editId = $editNome = $editTime = $deleteId = "";

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['add'])) {
            // Processa os dados do formulário para adicionar um novo torcedor
            $nome = $_POST['nome'];
            $time = $_POST['time'];
            
            // Insere os dados do novo torcedor na tabela
            $query = "INSERT INTO cadastro (nome, time) VALUES ('$nome', '$time')";
            $result = mysqli_query($conexao, $query);
            
            // Verifica se houve algum erro na inserção
            if (!$result) {
                echo "Erro ao inserir torcedor: " . mysqli_error($conexao);
                exit();
            }
        } elseif (isset($_POST['edit'])) {
            // Processa os dados do formulário para editar um torcedor existente
            $id = $_POST['id_cadastro'];
            $nome = $_POST['nome'];
            $time = $_POST['time'];
            
            // Atualiza os dados do torcedor na tabela
            $query = "UPDATE cadastro SET nome='$nome', time='$time' WHERE id_cadastro='$id'";
            $result = mysqli_query($conexao, $query);
            
            // Verifica se houve algum erro na atualização
            if (!$result) {
                echo "Erro ao atualizar torcedor: " . mysqli_error($conexao);
                exit();
            }
        } elseif (isset($_POST['delete'])) {
            // Processa os dados do formulário para apagar um torcedor existente
            $id = $_POST['id_cadastro'];
            
            // Deleta os dados do torcedor na tabela
            $query = "DELETE FROM cadastro WHERE id_cadastro='$id'";
            $result = mysqli_query($conexao, $query);
            
            // Verifica se houve algum erro na exclusão
            if (!$result) {
                echo "Erro ao apagar torcedor: " . mysqli_error($conexao);
                exit();
            }
        } elseif (isset($_POST['showEditModal'])) {
            // Exibe o modal de edição
            $showEditModal = true;
            $editId = $_POST['id_cadastro'];
            $editNome = $_POST['nome'];
            $editTime = $_POST['time'];
        } elseif (isset($_POST['showDeleteModal'])) {
            // Exibe o modal de exclusão
            $showDeleteModal = true;
            $deleteId = $_POST['id_cadastro'];
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Torcedores</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMp5FMZVoPY35N5YmIDrF43aS+8bJo/50q5ONwd" crossorigin="anonymous">
</head>
<body>

<div id="dashboard">
    <!-- Formulário para inserir um novo torcedor -->
    <div id="new-torcedor">
        <h2>Adicionar Novo Torcedor</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="add" value="1">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br><br>
            <label for="time">Time:</label>
            <input type="text" id="time" name="time" required><br><br>
            <button type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16">
                    <path d="M48 96V416c0 8.8 7.2 16 16 16H384c8.8 0 16-7.2 16-16V170.5c0-4.2-1.7-8.3-4.7-11.3l33.9-33.9c12 12 18.7 28.3 18.7 45.3V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96C0 60.7 28.7 32 64 32H309.5c17 0 33.3 6.7 45.3 18.7l74.5 74.5-33.9 33.9L320.8 84.7c-.3-.3-.5-.5-.8-.8V184c0 13.3-10.7 24-24 24H104c-13.3 0-24-10.7-24-24V80H64c-8.8 0-16 7.2-16 16zm80-16v80H272V80H128zm32 240a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"/>
                </svg>
                Salvar
            </button>
        </form>
    </div>

    <!-- Tabela de torcedores -->
    <h2>Tabela de torcedores</h2>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Time</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Consulta SQL para buscar os dados dos torcedores
                $query = "SELECT id_cadastro, nome, time FROM cadastro";
                
                // Executa a consulta
                $result = mysqli_query($conexao, $query);
                
                // Verifica se houve algum erro na execução da consulta
                if (!$result) {
                    echo "Erro na consulta: " . mysqli_error($conexao);
                    exit();
                }

                // Exibe os dados dos torcedores na tabela
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . $row['time'] . "</td>";
                    echo "<td>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='id_cadastro' value='" . $row['id_cadastro'] . "'>
                                <input type='hidden' name='nome' value='" . $row['nome'] . "'>
                                <input type='hidden' name='time' value='" . $row['time'] . "'>
                                <button type='submit' name='showEditModal'>
                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' width='16' height='16'>
                                        <path d='M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z'/>
                                    </svg>
                                    Modificar
                                </button>
                            </form>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='id_cadastro' value='" . $row['id_cadastro'] . "'>
                                <button type='submit' name='showDeleteModal'>
                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' width='16' height='16'>
                                        <path d='M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z'/>
                                    </svg>
                                    Apagar
                                </button>
                            </form>
                          </td>";
                    echo "</tr>";
                }

                // Fecha a conexão com o banco de dados
                mysqli_close($conexao);
            ?>
        </tbody>
    </table>
    <a href="deslogar.php">Logout</a>
</div>

<!-- Modal para editar torcedor -->
<?php if ($showEditModal): ?>
    <a name="editModal"></a>
    <div id="edit-modal" style="display:block;">
        <span class="close" onclick="window.location.href='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'">&times;</span>
        <h2>Editar Torcedor</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" id="edit-id" name="id_cadastro" value="<?php echo $editId; ?>">
            <input type="hidden" name="edit" value="1">
            <label for="edit-nome">Nome:</label>
            <input type="text" id="edit-nome" name="nome" value="<?php echo $editNome; ?>" required><br><br>
            <label for="edit-time">Time:</label>
            <input type="text" id="edit-time" name="time" value="<?php echo $editTime; ?>" required><br><br>
            <button type="submit">Salvar</button>
        </form>
    </div>
    <script>window.location.hash = '#editModal';</script>
<?php endif; ?>

<!-- Modal para apagar torcedor -->
<?php if ($showDeleteModal): ?>
    <a name="deleteModal"></a>
    <div id="delete-modal" style="display:block;">
        <span class="close" onclick="window.location.href='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'">&times;</span>
        <h2>Apagar Torcedor</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" id="delete-id" name="id_cadastro" value="<?php echo $deleteId; ?>">
            <input type="hidden" name="delete" value="1">
            <p>Tem certeza que deseja apagar este torcedor?</p>
            <button type="submit"><i class="fas fa-trash-alt"></i> Sim</button>
            <button type="button" onclick="window.location.href='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'">Não</button>
        </form>
    </div>
    <script>window.location.hash = '#deleteModal';</script>
<?php endif; ?>

</body>
</html>

<?php
} else {
    // O usuário não está logado, redireciona para a página de login
    header("Location: index.php");
    exit(); // Termina o script para garantir que a página não seja carregada se o usuário não estiver logado
}
?>
