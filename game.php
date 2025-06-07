<?php
require_once 'config.php';
require_once 'functions.php';

// Oyuncu adı kontrolü
if (!isset($_SESSION['player_name']) || empty($_SESSION['player_name'])) {
    header('Location: index.php');
    exit;
}

// Yeni oyun başlatma - Zorla yeni oyun başlat
if (isset($_GET['new']) || isset($_POST['new_game']) || !isset($_SESSION['game'])) {
    $difficulty = $_GET['difficulty'] ?? $_POST['difficulty'] ?? 'medium';
    start_new_game($difficulty);
    
    // Yeni oyun başlatıldıktan sonra aynı sayfaya redirect et
    if (isset($_POST['new_game'])) {
        header('Location: game.php');
        exit;
    }
}

$game = $_SESSION['game'];
$player_name = $_SESSION['player_name'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Oyun</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7fafc;
            min-height: 100vh;
            padding: 1rem;
        }
        
        .header {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .game-info {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .info-item {
            text-align: center;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: #718096;
            display: block;
        }
        
        .info-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2d3748;
        }
        
        .controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
            background: none;
        }
        
        .btn-primary {
            background: #4299e1;
            color: white;
        }
        
        .btn-primary:hover {
            background: #3182ce;
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        
        select {
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
        }
        
        .game-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .game-board {
            display: inline-grid;
            gap: 1px;
            background: #cbd5e0;
            border: 2px solid #cbd5e0;
            border-radius: 8px;
            padding: 10px;
            margin: 1rem 0;
        }
        
        .cell {
            width: 30px;
            height: 30px;
            background: #e2e8f0;
            border: 1px solid #cbd5e0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            user-select: none;
            transition: all 0.1s;
        }
        
        .cell:hover {
            background: #cbd5e0;
        }
        
        .cell.revealed {
            background: #f7fafc;
            cursor: default;
        }
        
        .cell.revealed:hover {
            background: #f7fafc;
        }
        
        .cell.mine {
            background: #fed7d7 !important;
            color: #c53030;
        }
        
        .cell.flagged {
            background: #fef5e7;
            color: #d69e2e;
        }
        
        .cell.empty {
            background: #f0fff4;
        }
        
        .cell.number-1 { color: #3182ce; }
        .cell.number-2 { color: #38a169; }
        .cell.number-3 { color: #e53e3e; }
        .cell.number-4 { color: #805ad5; }
        .cell.number-5 { color: #d53f8c; }
        .cell.number-6 { color: #dd6b20; }
        .cell.number-7 { color: #2b6cb0; }
        .cell.number-8 { color: #2d3748; }
        
        .game-status {
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .status-won {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .status-lost {
            background: #fed7d7;
            color: #742a2a;
        }
        
        .instructions {
            margin-top: 2rem;
            padding: 1rem;
            background: #edf2f7;
            border-radius: 8px;
            text-align: left;
        }
        
        .instructions h3 {
            margin-bottom: 0.5rem;
            color: #2d3748;
        }
        
        .instructions ul {
            list-style: none;
            padding-left: 0;
        }
        
        .instructions li {
            margin-bottom: 0.5rem;
            color: #4a5568;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .game-info {
                justify-content: center;
            }
            
            .cell {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="game-info">
            <div class="info-item">
                <span class="info-label">Oyuncu</span>
                <span class="info-value"><?php echo safe_output($player_name); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Süre</span>
                <span class="info-value" id="timer">00:00</span>
            </div>
            <div class="info-item">
                <span class="info-label">Mayın</span>
                <span class="info-value" id="mines-left"><?php echo $game['mines'] - $game['flags_used']; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Zorluk</span>
                <span class="info-value"><?php echo $GAME_SETTINGS[$game['difficulty']]['name']; ?></span>
            </div>
        </div>
        
        <div class="controls">
            <form method="POST" style="display: inline;">
                <select name="difficulty" onchange="this.form.submit()">
                    <?php foreach ($GAME_SETTINGS as $key => $setting): ?>
                        <option value="<?php echo $key; ?>" <?php echo $key === $game['difficulty'] ? 'selected' : ''; ?>>
                            <?php echo $setting['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="new_game" value="1">
            </form>
            
            <form method="POST" style="display: inline;">
                <input type="hidden" name="new_game" value="1">
                <input type="hidden" name="difficulty" value="<?php echo $game['difficulty']; ?>">
                <button type="submit" class="btn btn-primary">Yeni Oyun</button>
            </form>
            
            <a href="scores.php" class="btn btn-secondary">Skorlar</a>
            <a href="logout.php" class="btn btn-secondary">Çıkış</a>
        </div>
    </div>
    
    <div class="game-container">
        <?php if ($game['state'] === 'won'): ?>
            <div class="game-status status-won">
                🎉 Tebrikler! Oyunu kazandınız! Skorunuz: <?php echo $game['score']; ?>
            </div>
        <?php elseif ($game['state'] === 'lost'): ?>
            <div class="game-status status-lost">
                💥 Mayına bastınız! Tekrar deneyin.
            </div>
        <?php endif; ?>
        
        <div class="game-board" style="grid-template-columns: repeat(<?php echo $game['cols']; ?>, 1fr);">
            <?php for ($r = 0; $r < $game['rows']; $r++): ?>
                <?php for ($c = 0; $c < $game['cols']; $c++): ?>
                    <?php $cell = $game['board'][$r][$c]; ?>
                    <div class="<?php echo get_cell_class($cell); ?>" 
                         data-row="<?php echo $r; ?>" 
                         data-col="<?php echo $c; ?>"
                         onclick="revealCell(<?php echo $r; ?>, <?php echo $c; ?>)"
                         oncontextmenu="toggleFlag(<?php echo $r; ?>, <?php echo $c; ?>); return false;">
                        <?php echo get_cell_content($cell); ?>
                    </div>
                <?php endfor; ?>
            <?php endfor; ?>
        </div>
        
        <div class="instructions">
            <h3>Nasıl Oynanır:</h3>
            <ul>
                <li>🖱️ <strong>Sol tık:</strong> Hücreyi açmak için</li>
                <li>🖱️ <strong>Sağ tık:</strong> Bayrak koymak/kaldırmak için</li>
                <li>🎯 <strong>Amaç:</strong> Tüm mayınları bulmadan güvenli hücreleri açın</li>
                <li>🔢 <strong>Sayılar:</strong> Komşu hücrelerdeki mayın sayısını gösterir</li>
            </ul>
        </div>
    </div>
    
    <script>
        let gameState = '<?php echo $game['state']; ?>';
        let startTime = <?php echo $game['start_time']; ?>;
        let gameId = '<?php echo $game['game_id']; ?>';
        
        // Timer
        function updateTimer() {
            if (gameState === 'playing') {
                const elapsed = Math.floor(Date.now() / 1000) - startTime;
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                document.getElementById('timer').textContent = 
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }
        }
        
        setInterval(updateTimer, 1000);
        updateTimer();
        
        // Hücre açma
        function revealCell(row, col) {
            if (gameState !== 'playing') return;
            
            fetch('ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=reveal&row=' + row + '&col=' + col + '&game_id=' + gameId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.game_over) {
                        gameState = data.won ? 'won' : 'lost';
                    }
                    if (data.reload) {
                        location.reload();
                    }
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
            });
        }
        
        // Bayrak koyma
        function toggleFlag(row, col) {
            if (gameState !== 'playing') return;
            
            fetch('ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=flag&row=' + row + '&col=' + col + '&game_id=' + gameId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.reload) {
                        location.reload();
                    }
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
            });
        }
    </script>
</body>
</html>
