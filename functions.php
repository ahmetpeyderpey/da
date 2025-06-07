<?php
/**
 * Oyun fonksiyonları
 */

require_once 'config.php';

/**
 * Yeni oyun başlat
 */
function start_new_game($difficulty = 'medium') {
    global $GAME_SETTINGS;
    
    if (!isset($GAME_SETTINGS[$difficulty])) {
        $difficulty = 'medium';
    }
    
    $settings = $GAME_SETTINGS[$difficulty];
    $rows = $settings['rows'];
    $cols = $settings['cols'];
    $mines = $settings['mines'];
    
    // Boş tahta oluştur
    $board = [];
    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            $board[$r][$c] = [
                'is_mine' => false,
                'is_revealed' => false,
                'is_flagged' => false,
                'neighbor_count' => 0
            ];
        }
    }
    
    // Mayınları yerleştir
    $placed_mines = 0;
    while ($placed_mines < $mines) {
        $r = rand(0, $rows - 1);
        $c = rand(0, $cols - 1);
        
        if (!$board[$r][$c]['is_mine']) {
            $board[$r][$c]['is_mine'] = true;
            $placed_mines++;
            
            // Komşu sayılarını güncelle
            for ($dr = -1; $dr <= 1; $dr++) {
                for ($dc = -1; $dc <= 1; $dc++) {
                    $nr = $r + $dr;
                    $nc = $c + $dc;
                    
                    if ($nr >= 0 && $nr < $rows && $nc >= 0 && $nc < $cols) {
                        $board[$nr][$nc]['neighbor_count']++;
                    }
                }
            }
        }
    }
    
    // Session'a kaydet - Önceki oyun verilerini tamamen temizle
    $_SESSION['game'] = [
        'board' => $board,
        'rows' => $rows,
        'cols' => $cols,
        'mines' => $mines,
        'difficulty' => $difficulty,
        'state' => 'playing', // playing, won, lost
        'start_time' => time(),
        'flags_used' => 0,
        'cells_revealed' => 0,
        'score' => 0,
        'game_id' => uniqid() // Benzersiz oyun ID'si
    ];
    
    return true;
}

/**
 * Hücre aç
 */
function reveal_cell($row, $col) {
    if (!isset($_SESSION['game'])) {
        return ['success' => false, 'message' => 'Oyun başlatılmamış'];
    }
    
    $game = &$_SESSION['game'];
    
    if ($game['state'] !== 'playing') {
        return ['success' => false, 'message' => 'Oyun bitmiş'];
    }
    
    if ($row < 0 || $row >= $game['rows'] || $col < 0 || $col >= $game['cols']) {
        return ['success' => false, 'message' => 'Geçersiz koordinat'];
    }
    
    $cell = &$game['board'][$row][$col];
    
    if ($cell['is_revealed'] || $cell['is_flagged']) {
        return ['success' => false, 'message' => 'Bu hücre açılamaz'];
    }
    
    // Hücreyi aç
    $cell['is_revealed'] = true;
    $game['cells_revealed']++;
    
    // Mayına bastıysa
    if ($cell['is_mine']) {
        $game['state'] = 'lost';
        
        // Tüm mayınları göster
        for ($r = 0; $r < $game['rows']; $r++) {
            for ($c = 0; $c < $game['cols']; $c++) {
                if ($game['board'][$r][$c]['is_mine']) {
                    $game['board'][$r][$c]['is_revealed'] = true;
                }
            }
        }
        
        return [
            'success' => true,
            'game_over' => true,
            'won' => false,
            'message' => 'Mayına bastınız!',
            'reload' => true
        ];
    }
    
    // Boş hücre ise komşuları da aç
    if ($cell['neighbor_count'] === 0) {
        $queue = [[$row, $col]];
        
        while (!empty($queue)) {
            list($r, $c) = array_shift($queue);
            
            for ($dr = -1; $dr <= 1; $dr++) {
                for ($dc = -1; $dc <= 1; $dc++) {
                    $nr = $r + $dr;
                    $nc = $c + $dc;
                    
                    if ($nr >= 0 && $nr < $game['rows'] && 
                        $nc >= 0 && $nc < $game['cols'] &&
                        !$game['board'][$nr][$nc]['is_revealed'] &&
                        !$game['board'][$nr][$nc]['is_flagged'] &&
                        !$game['board'][$nr][$nc]['is_mine']) {
                        
                        $game['board'][$nr][$nc]['is_revealed'] = true;
                        $game['cells_revealed']++;
                        
                        if ($game['board'][$nr][$nc]['neighbor_count'] === 0) {
                            $queue[] = [$nr, $nc];
                        }
                    }
                }
            }
        }
    }
    
    // Kazanma kontrolü
    $total_safe_cells = ($game['rows'] * $game['cols']) - $game['mines'];
    if ($game['cells_revealed'] >= $total_safe_cells) {
        $game['state'] = 'won';
        $game['score'] = calculate_score($game);
        
        // Skoru kaydet
        if (isset($_SESSION['player_name'])) {
            save_score($_SESSION['player_name'], $game);
        }
        
        return [
            'success' => true,
            'game_over' => true,
            'won' => true,
            'score' => $game['score'],
            'message' => 'Tebrikler! Oyunu kazandınız!',
            'reload' => true
        ];
    }
    
    return ['success' => true, 'game_over' => false, 'reload' => true];
}

/**
 * Bayrak koy/kaldır
 */
function toggle_flag($row, $col) {
    if (!isset($_SESSION['game'])) {
        return ['success' => false, 'message' => 'Oyun başlatılmamış'];
    }
    
    $game = &$_SESSION['game'];
    
    if ($game['state'] !== 'playing') {
        return ['success' => false, 'message' => 'Oyun bitmiş'];
    }
    
    if ($row < 0 || $row >= $game['rows'] || $col < 0 || $col >= $game['cols']) {
        return ['success' => false, 'message' => 'Geçersiz koordinat'];
    }
    
    $cell = &$game['board'][$row][$col];
    
    if ($cell['is_revealed']) {
        return ['success' => false, 'message' => 'Açık hücreye bayrak konulamaz'];
    }
    
    // Bayrak durumunu değiştir
    if ($cell['is_flagged']) {
        $cell['is_flagged'] = false;
        $game['flags_used']--;
    } else {
        $cell['is_flagged'] = true;
        $game['flags_used']++;
    }
    
    return [
        'success' => true,
        'is_flagged' => $cell['is_flagged'],
        'flags_used' => $game['flags_used'],
        'reload' => true
    ];
}

/**
 * Skor hesapla
 */
function calculate_score($game) {
    $time_taken = time() - $game['start_time'];
    $base_score = 1000;
    
    // Zorluk bonusu
    $difficulty_bonus = [
        'easy' => 1,
        'medium' => 2,
        'hard' => 3
    ];
    
    $bonus = $difficulty_bonus[$game['difficulty']] ?? 1;
    
    // Zaman bonusu (hızlı bitirme)
    $time_bonus = max(0, 300 - $time_taken);
    
    return ($base_score * $bonus) + $time_bonus;
}

/**
 * Skor kaydet
 */
function save_score($player_name, $game) {
    $scores = [];
    
    if (file_exists(SCORES_FILE)) {
        $scores = json_decode(file_get_contents(SCORES_FILE), true) ?: [];
    }
    
    $scores[] = [
        'player' => $player_name,
        'score' => $game['score'],
        'difficulty' => $game['difficulty'],
        'time' => time() - $game['start_time'],
        'date' => date('Y-m-d H:i:s')
    ];
    
    // Skorları sırala
    usort($scores, function($a, $b) {
        return $b['score'] - $a['score'];
    });
    
    // En iyi 50 skoru sakla
    $scores = array_slice($scores, 0, 50);
    
    file_put_contents(SCORES_FILE, json_encode($scores, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

/**
 * Skorları getir
 */
function get_scores($limit = 10) {
    if (!file_exists(SCORES_FILE)) {
        return [];
    }
    
    $scores = json_decode(file_get_contents(SCORES_FILE), true) ?: [];
    return array_slice($scores, 0, $limit);
}

/**
 * Hücre içeriğini getir
 */
function get_cell_content($cell) {
    if ($cell['is_flagged']) {
        return '🚩';
    }
    
    if (!$cell['is_revealed']) {
        return '';
    }
    
    if ($cell['is_mine']) {
        return '💣';
    }
    
    if ($cell['neighbor_count'] > 0) {
        return $cell['neighbor_count'];
    }
    
    return '';
}

/**
 * Hücre CSS sınıfını getir
 */
function get_cell_class($cell) {
    $classes = ['cell'];
    
    if ($cell['is_revealed']) {
        $classes[] = 'revealed';
        
        if ($cell['is_mine']) {
            $classes[] = 'mine';
        } elseif ($cell['neighbor_count'] > 0) {
            $classes[] = 'number-' . $cell['neighbor_count'];
        } else {
            $classes[] = 'empty';
        }
    } else {
        $classes[] = 'hidden';
        
        if ($cell['is_flagged']) {
            $classes[] = 'flagged';
        }
    }
    
    return implode(' ', $classes);
}
?>
