<?php

class UsuariosController
{

    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';

        global $pdo;

        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id, nome, email, perfil, status, criado_em
                FROM usuarios
                ORDER BY id DESC';
                
        $stmt = $this->pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        $sql = 'SELECT id, nome, email, perfil, status, criado_em
                FROM usuarios
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuário não encontrado']);
            return;
        }

        echo json_encode($usuario, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '' || $email === '' || $senha === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Nome, email e senha são obrigatórios']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email inválido']);
            return;
        }

        if (!in_array($perfil, ['admin', 'atendente', 'aluno'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Perfil deve ser "admin","atendente" ou "aluno"']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Status deve ser "ativo" ou "inativo"']);
            return;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = 'INSERT INTO usuarios (nome, email, senha, perfil, status)
                    VALUES (:nome, :email, :senha, :perfil, :status)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':perfil', $perfil);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'message' => 'Usuário cadastrado com sucesso',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao cadastrar usuário: ' . $e->getMessage()]);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if (!$id || $nome === '' || $email === '') {
                http_response_code(400);
                echo json_encode(['error' => 'ID, nome e email são obrigatórios']);
                return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email inválido']);
            return;
        }

        if (!in_array($perfil, ['admin', 'atendente', 'aluno'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Perfil deve ser "admin","atendente" ou "aluno"']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Status deve ser "ativo" ou "inativo"']);
            return;
        }

        try {
            $sql = 'UPDATE usuarios
                    SET nome = :nome, 
                        email = :email, 
                        perfil = :perfil, 
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':perfil', $perfil);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['message' => 'Usuário atualizado com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar usuário: ' . $e->getMessage()]);
        }
    }

    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        try {
            $sql = 'DELETE FROM usuarios WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['message' => 'Usuário excluído com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir usuário: ' . $e->getMessage()]);
        }
    }

}