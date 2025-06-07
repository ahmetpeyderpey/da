<?php
require_once 'config.php';
require_once 'functions.php';

$scores = get_scores(20);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Skor Tablosu</title>
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
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .header h1 {
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            color: #718096;
        }
        
        .scores-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            background: #edf2f7;
            font-weight: 600;
            color: #2d3748;
        }
        
        tr:hover {
            background: #f7fafc;
        }
        
        .rank {
            font-weight: bold;
            color: #4299e1;
        }
        
        .rank.gold { color: #d69e2e; }
        .rank.silver { color: #718096; }
        .rank.bronze { color: #c05621; }
        
        .difficulty {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .difficulty.easy {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .difficulty.medium {
            background: #fef5e7;
            color: #744210;
        }
        
        .difficulty.hard {
            background: #fed7d7;
            color: #742a2a;
        }
        
        .controls {
            text-align: center;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 0 0.5rem;
            transition: all 0.2s;
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
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #718096;
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏆 Skor Tablosu</h1>
            <p>En iyi oyuncular ve skorları</p>
        </div>
        
        <div class="scores-table">
            <?php if (empty($scores)): ?>
                <div class="empty-state">
                    <h3>Henüz skor yok</h3>
                    <p>İlk skoru sen oluştur!</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Sıra</th>
                            <th>Oyuncu</th>
                            <th>Skor</th>
                            <th>Zorluk</th>
                            <th>Süre</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $index => $score): ?>
                            <tr>
                                <td>
                                    <span class="rank <?php 
                                        echo $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : '')); 
                                    ?>">
                                        #<?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td><?php echo safe_output($score['player']); ?></td>
                                <td><strong><?php echo number_format($score['score']); ?></strong></td>
                                <td>
                                    <span class="difficulty <?php echo $score['difficulty']; ?>">
                                        <?php echo $GAME_SETTINGS[$score['difficulty']]['name']; ?>
                                    </span>
                                </td>
                                <td><?php echo format_time($score['time']); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($score['date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="controls">
            <a href="game.php" class="btn btn-primary">Oyuna Dön</a>
            <a href="index.php" class="btn btn-secondary">Ana Sayfa</a>
        </div>
    </div>
</body>
</html>
