<?php
require_once 'config.php';
require_once 'functions.php';

// JSON header
header('Content-Type: application/json');

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Geçersiz istek'], 405);
}

// Action parametresini kontrol et
$action = $_POST['action'] ?? '';

// Oyun ID kontrolü
$game_id = $_POST['game_id'] ?? '';
if (isset($_SESSION['game']) && isset($_SESSION['game']['game_id']) && $_SESSION['game']['game_id'] !== $game_id) {
    send_json(['success' => false, 'message' => 'Oyun ID uyuşmuyor', 'reload_page' => true]);
}

switch ($action) {
    case 'reveal':
        $row = intval($_POST['row'] ?? -1);
        $col = intval($_POST['col'] ?? -1);
        $result = reveal_cell($row, $col);
        send_json($result);
        break;
        
    case 'flag':
        $row = intval($_POST['row'] ?? -1);
        $col = intval($_POST['col'] ?? -1);
        $result = toggle_flag($row, $col);
        send_json($result);
        break;
        
    default:
        send_json(['success' => false, 'message' => 'Geçersiz action'], 400);
}
?>
