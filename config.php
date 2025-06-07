<?php
/**
 * Mayın Tarlası Oyunu - Ana Konfigürasyon
 * Versiyon: 1.0
 */

// Hata raporlama (production'da kapatılmalı)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Karakter kodlaması
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Session ayarları
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // HTTPS için 1 yapın
    session_start();
}

// Uygulama sabitleri
define('APP_NAME', 'Mayın Tarlası');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));

// Dosya yolları
define('DATA_DIR', __DIR__ . '/data');
define('SCORES_FILE', DATA_DIR . '/scores.json');

// Data klasörünü oluştur
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Scores dosyasını oluştur
if (!file_exists(SCORES_FILE)) {
    file_put_contents(SCORES_FILE, json_encode([]));
}

// Oyun ayarları
$GAME_SETTINGS = [
    'easy' => ['rows' => 9, 'cols' => 9, 'mines' => 10, 'name' => 'Kolay'],
    'medium' => ['rows' => 16, 'cols' => 16, 'mines' => 40, 'name' => 'Orta'],
    'hard' => ['rows' => 16, 'cols' => 30, 'mines' => 99, 'name' => 'Zor']
];

/**
 * Güvenli çıktı fonksiyonu
 */
function safe_output($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * JSON yanıt gönder
 */
function send_json($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Zaman formatla
 */
function format_time($seconds) {
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d', $minutes, $seconds);
}
?>
