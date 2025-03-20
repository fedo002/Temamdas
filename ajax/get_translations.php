<?php
// ajax/get_translations.php

// Güvenlik kontrolleri
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Dil parametresini al
$lang = $_GET['lang'] ?? 'en';

// Sadece desteklenen dillere izin ver
$supported_languages = ['en', 'ru', 'tr', 'ka'];
if (!in_array($lang, $supported_languages)) {
    $lang = 'en';
}

// Çeviri dosyasının yolu - dosya yolunu düzgün şekilde kontrol edin
$translations_file = __DIR__ . '/../lang/game_' . $lang . '.json';

if (file_exists($translations_file)) {
    // Çeviri dosyasını doğrudan çıktıla
    echo file_get_contents($translations_file);
} else {
    // Varsayılan çevirileri döndür
    echo json_encode([
        'daily_game' => [
            'modals' => [
                'timeout' => [
                    'title' => 'Time\'s Up!',
                    'message1' => 'You didn\'t select a card within the time limit.',
                    'message2' => 'The lowest reward has been added to your account.',
                    'btn_ok' => 'OK'
                ],
                'result' => [
                    'title' => 'Congratulations!',
                    'message' => 'Your reward has been added to your account.',
                    'try_again_message' => 'You can try again for a better reward!',
                    'remaining_retries' => 'Your remaining retry attempts',
                    'btn_ok' => 'OK',
                    'btn_try_again' => 'Try Again'
                ],
                'error' => [
                    'title' => 'Error',
                    'generic_error' => 'An error occurred!',
                    'try_again' => 'Please try again later.',
                    'connection_error' => 'Connection Error',
                    'check_connection' => 'Please check your internet connection and try again.',
                    'no_attempts' => 'Your daily limit is reached!',
                    'try_tomorrow' => 'Come back tomorrow.',
                    'btn_ok' => 'OK'
                ],
                'confirmation' => [
                    'title' => 'Confirmation Request',
                    'try_luck_warning' => 'If you choose to try your luck, you will receive the lowest reward if you exit the game and your attempt will be counted. Do you want to continue?',
                    'btn_confirm' => 'OK',
                    'btn_cancel' => 'Cancel'
                ]
            ],
            'loading' => 'Processing your reward...'
        ]
    ]);
}
?>