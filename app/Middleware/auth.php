<?php

// Inicia a sessão somente se ela ainda não estiver ativa.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se existe um usuário autenticado na sessão.
 */
function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario'])
        && is_array($_SESSION['usuario'])
        && isset($_SESSION['usuario']['id']);
}

/**
 * Identifica se a requisição espera uma resposta JSON.
 *
 * No Thunder Client, Insomnia ou Postman, adicione:
 * Accept: application/json
 */
function requisicaoEsperaJson(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

    return str_contains($accept, 'application/json');
}

/**
 * Bloqueia o acesso quando não existe usuário autenticado.
 *
 * - Navegador: redireciona para o login.
 * - Cliente de API: retorna erro JSON com status HTTP 401.
 */
function exigirAutenticacao(): void
{
    if (usuarioAutenticado()) {
        return;
    }

    if (requisicaoEsperaJson()) {
        header('Content-Type: application/json; charset=utf-8');

        http_response_code(401);

        echo json_encode(
            [
                'erro' => true,
                'mensagem' => 'Usuário não autenticado.'
            ],
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $_SESSION['mensagem'] =
        'Faça login para acessar a área restrita.';

    header('Location: ?controller=auth&action=login');

    exit;
}

/**
 * Retorna os dados do usuário autenticado.
 */
function usuarioAtual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}