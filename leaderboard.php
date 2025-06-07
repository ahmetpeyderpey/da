<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Tema ayarı
$isDarkMode = isset($_COOKIE['dark_mode']) ? $_COOKIE['dark_mode'] === 'true' : true;

// Skorları getir
$scores = getScores();

// Filtre
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
if ($filter !== 'all') {
    $scores = array_filter($scores, function($score) use ($filter) {
        return $score['difficulty'] === $filter;
    });
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mayın Tarlası - Liderlik Tablosu</title>
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="<?php echo $isDarkMode ? 'dark-mode' : 'light-mode'; ?>">
    <div class="min-h-screen transition-all duration-500 p-4">
        <div class="container mx-auto max-w-4xl">
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
                        <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        Liderlik Tablosu
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

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2 mb-6">
                <?php
                $filters = [
                    'all' => 'Tümü',
                    'easy' => 'Kolay',
                    'medium' => 'Orta',
                    'hard' => 'Zor'
                ];
                
                foreach ($filters as $key => $label) {
                    $buttonClass = $filter === $key ? 'primary-button' : 'outline-button';
                    echo "<a href='?filter=$key' class='$buttonClass'>$label</a>";
                }
                ?>
            </div>

            <!-- Statistics Cards -->
            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <?php
                $totalGames = count($scores);
                $highestScore = $totalGames > 0 ? max(array_column($scores, 'score')) : 0;
                $fastestTime = $totalGames > 0 ? min(array_column($scores, 'time')) : 0;
                
                $stats = [
                    [
                        'icon' => '<svg class="w-8 h-8 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        'value' => $totalGames,
                        'label' => 'Toplam Oyun'
                    ],
                    [
                        'icon' => '<svg class="w-8 h-8 mx-auto mb-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>',
                        'value' => number_format($highestScore),
                        'label' => 'En Yüksek Skor'
                    ],
                    [
                        'icon' => '<svg class="w-8 h-8 mx-auto mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        'value' => formatTime($fastestTime),
                        'label' => 'En Hızlı Süre'
                    ]
                ];
                
                foreach ($stats as $stat) {
                    echo '<div class="stat-card">';
                    echo '<div class="p-4 text-center">';
                    echo $stat['icon'];
                    echo '<div class="text-2xl font-bold">' . $stat['value'] . '</div>';
                    echo '<div>' . $stat['label'] . '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Leaderboard -->
            <div>
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center text-2xl">🏆 En İyi Skorlar</h2>
                    </div>
                    <div class="card-content">
                        <?php if (empty($scores)): ?>
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                                <p class="text-xl mb-2">Henüz skor yok!</p>
                                <p class="mb-4">İlk skorunu kaydetmek için oyun oynamaya başla.</p>
                                <a href="game.php" class="primary-button">Oyuna Başla</a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php 
                                $rank = 0;
                                foreach (array_slice($scores, 0, 20) as $score): 
                                    $rank++;
                                    $isTopThree = $rank <= 3;
                                    $rowClass = $isTopThree ? 'leaderboard-row-top' : 'leaderboard-row';
                                ?>
                                    <div class="<?php echo $rowClass; ?>">
                                        <div class="flex items-center gap-4">
                                            <div class="rank-icon rank-<?php echo $rank; ?>">
                                                <?php if ($rank <= 3): ?>
                                                    <?php if ($rank === 1): ?>
                                                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                                        </svg>
                                                    <?php elseif ($rank === 2): ?>
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                        </svg>
                                                    <?php else: ?>
                                                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                                        </svg>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="w-6 h-6 flex items-center justify-center font-bold"><?php echo $rank; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-lg"><?php echo htmlspecialchars($score['nickname']); ?></div>
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="difficulty-badge difficulty-<?php echo $score['difficulty']; ?>">
                                                        <?php echo getDifficultyLabel($score['difficulty']); ?>
                                                    </span>
                                                    <span><?php echo date('d.m.Y', strtotime($score['date'])); ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <div class="font-bold text-xl"><?php echo number_format($score['score']); ?></div>
                                            <div class="text-sm flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <?php echo formatTime($score['time']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Back to Game Button -->
            <div class="text-center mt-8">
                <a href="game.php" class="primary-button text-lg px-8 py-3">
                    🎮 Oyuna Dön
                </a>
            </div>
        </div>
    </div>

    <script src="assets/js/leaderboard.js"></script>
</body>
</html>
