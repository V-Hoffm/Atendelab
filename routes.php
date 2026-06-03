<?php
require_once __DIR__ . '/app/controllers/UsuariosController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// if ($controller === 'usuarios') {
//     $usuariosController = new UsuariosController();
//     switch ($action) {
//         case 'listar':
//             $usuariosController->listar();
//             break;
//         case 'buscar':
//             $usuariosController->buscarPorId();
//             break;
//         case 'criar':
//             $usuariosController->criar();
//             break;
//         case 'atualizar':
//             $usuariosController->atualizar();
//             break;
//         case 'excluir':
//             $usuariosController->excluir();
//             break;
//         default:
//             echo "Ação não de usuários não encontrada.";
//             break;
//     }
// } else {
//     echo "<h1>AtendeLab</h1>";
//     echo "<p>Projeto em execução. Use ?controller=usuarios&action=listar para testar.</p>";
// }

require_once __DIR__ . '/app/controllers/PessoasController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

if ($controller === 'pessoas') {
    $pessoasController = new PessoasController();
    switch ($action) {
        case 'listar':
            $pessoasController->listar();
            break;
        case 'buscar':
            $pessoasController->buscarPorId();
            break;
        case 'criar':
            $pessoasController->criar();
            break;
        case 'atualizar':
            $pessoasController->atualizar();
            break;
        case 'excluir':
            $pessoasController->excluir();
            break;
        default:
            echo "Ação não de pessoas não encontrada.";
            break;
    }
} else {
    echo "<h1>AtendeLab</h1>";
    echo "<p>22222Projeto em execução. Use ?controller=pessoas&action=listar para testar.</p>";
}