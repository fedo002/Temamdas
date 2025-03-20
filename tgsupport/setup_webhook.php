<?php
// setup_webhook.php - Telegram Webhook'u ayarlama
require_once 'config.php';

// Webhook URL (https olmalı!)
$webhook_url = "https://digiminex.com/tgsupport/webhook.php"; // Kendi URL'niz ile değiştirin
$bot_token = BOT_TOKEN;

// Telegram API'sine webhook ayarla
$telegram_api_url = "https://api.telegram.org/bot{$bot_token}/setWebhook?url={$webhook_url}";

$ch = curl_init($telegram_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result && isset($result['ok']) && $result['ok'] === true) {
    echo "<h2>Webhook başarıyla ayarlandı!</h2>";
    echo "<p>URL: {$webhook_url}</p>";
    if (isset($result['description'])) {
        echo "<p>Açıklama: {$result['description']}</p>";
    }
} else {
    echo "<h2>Webhook ayarlanırken hata oluştu!</h2>";
    echo "<pre>" . print_r($result, true) . "</pre>";
}

// Mevcut webhook bilgisini kontrol et
$info_url = "https://api.telegram.org/bot{$bot_token}/getWebhookInfo";
$ch = curl_init($info_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$info_response = curl_exec($ch);
curl_close($ch);

$info = json_decode($info_response, true);

echo "<h2>Mevcut Webhook Bilgisi:</h2>";
echo "<pre>" . print_r($info, true) . "</pre>";
?>