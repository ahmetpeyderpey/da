<?php
require_once 'config.php';

// Eğer oyuncu adı varsa oyun sayfasına yönlendir
if (isset($_SESSION['player_name']) && !empty($_SESSION['player_name'])) {
    header('Location: game.php');
    exit;
}

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_name'])) {
    $player_name = trim($_POST['player_name']);
    
    if (!empty($player_name) && strlen($player_name) <= 20) {
        $_SESSION['player_name'] = $player_name;
        header('Location: game.php');
        exit;
    } else {
        $error = 'Lütfen geçerli bir isim girin (1-20 karakter)';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Giriş</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        
        .container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        
        h1 {
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-size: 2.5rem;
        }
        
        .subtitle {
            color: #718096;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #4a5568;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .error {
            background: #fed7d7;
            color: #c53030;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .features {
            margin-top: 2rem;
            text-align: left;
        }
        
        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            color: #718096;
        }
        
        .feature-icon {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💣 Mayın Tarlası</h1>
        <p class="subtitle">Klasik mayın tarlası oyunu</p>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo safe_output($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="player_name">Oyuncu Adınız:</label>
                <input type="text" id="player_name" name="player_name" 
                       placeholder="Adınızı girin..." maxlength="20" required>
            </div>
            
            <button type="submit" class="btn">Oyuna Başla</button>
        </form>
        
        <div class="features">
            <div class="feature">
                <span class="feature-icon">🎯</span>
                <span>3 farklı zorluk seviyesi</span>
            </div>
            <div class="feature">
                <span class="feature-icon">🏆</span>
                <span>Skor tablosu</span>
            </div>
            <div class="feature">
                <span class="feature-icon">⚡</span>
                <span>Hızlı ve akıcı oynanış</span>
            </div>
        </div>
    </div>
</body>
</html>
