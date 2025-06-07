<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Tema ayarı
$isDarkMode = isset($_COOKIE['dark_mode']) ? $_COOKIE['dark_mode'] === 'true' : true;

// Skorları getir
$scores = getScores();

// İstatistikleri hesapla
$stats = calculateStats($scores);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mayın Tarlası - İstatistikler</title>
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="<?php echo $isDarkMode ? 'dark-mode' : 'light-mode'; ?>">
    <div class="min-h-screen transition-all duration-500 p-4">
        <div class="container mx-auto max-w-6xl">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="index.php" class="outline-button">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Ana Sayfa
                    </a>
                    <h1 class="text-4xl font-bold flex items-center gap-2">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        İstatistikler
                    </h1>
                </div>

                <button id="theme-toggle" class="control-button">
                    <?php if ($isDarkMode): ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    <?php else: ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    <?php endif; ?>
                </button>
            </div>

            <?php if (empty($scores)): ?>
                <div class="card">
                    <div class="card-content p-8 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold mb-2">Henüz İstatistik Yok!</h2>
                        <p class="mb-6">İstatistiklerini görmek için oyun oynamaya başla.</p>
                        <a href="game.php" class="primary-button">Oyuna Başla</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- General Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <?php
                    $generalStats = [
                        [
                            'icon' => '<svg class="w-5 h-5 mx-auto mb-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'value' => $stats['totalGames'],
                            'label' => 'Toplam Oyun'
                        ],
                        [
                            'icon' => '<svg class="w-5 h-5 mx-auto mb-1 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>',
                            'value' => number_format($stats['bestScore']),
                            'label' => 'En Yüksek Skor'
                        ],
                        [
                            'icon' => '<svg class="w-5 h-5 mx-auto mb-1 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'value' => formatTime($stats['fastestTime']),
                            'label' => 'En Hızlı Süre'
                        ],
                        [
                            'icon' => '<svg class="w-5 h-5 mx-auto mb-1 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                            'value' => $stats['streak'],
                            'label' => 'Mevcut Seri'
                        ]
                    ];
                    
                    foreach ($generalStats as $stat) {
                        echo '<div class="stat-card">';
                        echo '<div class="p-4 text-center">';
                        echo $stat['icon'];
                        echo '<div class="font-bold">' . $stat['value'] . '</div>';
                        echo '<div class="text-xs">' . $stat['label'] . '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>

                <!-- Detailed Statistics -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <div class="card h-full">
                            <div class="card-header">
                                <h2 class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    Performans Metrikleri
                                </h2>
                            </div>
                            <div class="card-content space-y-4">
                                <?php
                                $metrics = [
                                    ['label' => 'Ortalama Süre', 'value' => formatTime($stats['averageTime'])],
                                    ['label' => 'Toplam Skor', 'value' => number_format($stats['totalScore'])],
                                    ['label' => 'Ortalama Skor', 'value' => number_format($stats['averageScore'])],
                                    [
                                        'label' => 'Başarı Oranı', 
                                        'value' => $stats['winRate'] . '%
