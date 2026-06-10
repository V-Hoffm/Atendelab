<?php

class PessoasController
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

        $sql = 'SELECT id, nome, email, curso, periodo, tipo_documento, documento, criado_em
                FROM pessoas
                ORDER BY id DESC';
                
        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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

        $sql = 'SELECT id, nome, email, curso, periodo, tipo_documento, documento, criado_em
                FROM pessoas
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['error' => 'Pessoa não encontrada']);
            return;
        }

        echo json_encode($pessoa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = $_POST['curso'] ?? '';
        $periodo = $_POST['periodo'] ?? '';
        $tipo_documento = $_POST['tipo_documento'] ?? 'matricula';
        $documento = trim($_POST['documento'] ?? '');

        if ($nome === '' || $email === '' || $tipo_documento === '' || $documento === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Os campos nome, email, tipo de documento e documento são obrigatórios']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email inválido']);
            return;
        }

        if (!in_array($tipo_documento, ['cpf', 'matricula'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de documento deve ser "cpf" ou "matricula"']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (nome, email, curso, periodo, tipo_documento, documento)
                    VALUES (:nome, :email, :curso, :periodo, :tipo_documento, :documento)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':curso', $curso);
            $stmt->bindParam(':periodo', $periodo);
            $stmt->bindParam(':tipo_documento', $tipo_documento);
            $stmt->bindParam(':documento', $documento);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'message' => 'Pessoa cadastrada com sucesso',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao cadastrar pessoa: ' . $e->getMessage()]);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = $_POST['curso'] ?? '';
        $periodo = $_POST['periodo'] ?? '';
        $tipo_documento = $_POST['tipo_documento'] ?? 'matricula';
        $documento = trim($_POST['documento'] ?? '');

        if (!$id || $nome === '' || $email === '' || $tipo_documento === '' || $documento === '') {
                http_response_code(400);
                echo json_encode(['error' => 'ID, nome, email, tipo de documento e documento são obrigatórios']);
                return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email inválido']);
            return;
        }

        if (!in_array($tipo_documento, ['cpf', 'matricula'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de documento deve ser "cpf" ou "matricula"']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas
                    SET nome = :nome, 
                        email = :email, 
                        curso = :curso, 
                        periodo = :periodo, 
                        tipo_documento = :tipo_documento, 
                        documento = :documento
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':curso', $curso);
            $stmt->bindParam(':periodo', $periodo);
            $stmt->bindParam(':tipo_documento', $tipo_documento);
            $stmt->bindParam(':documento', $documento);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['message' => 'Pessoa atualizada com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar pessoa: ' . $e->getMessage()]);
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
            $sql = 'DELETE FROM pessoas WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['message' => 'Pessoa excluída com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir pessoa: ' . $e->getMessage()]);
        }
    }

}