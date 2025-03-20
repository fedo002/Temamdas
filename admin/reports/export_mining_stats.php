<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Bağlantı değişkenini al
$conn = $db->getConnection();

// Tarih filtresi
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$package_filter = isset($_GET['package_id']) ? (int)$_GET['package_id'] : 0;
$user_filter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Mining istatistiklerini getir
$stats = getMiningStats($start_date, $end_date, $package_filter, $user_filter);

// Günlük istatistikler
$daily_stats = [];

// Toplam değerler
$total_revenue = 0;
$total_electricity_cost = 0;
$total_net_revenue = 0;

// Son 30 günlük istatistikleri hesapla
for ($i = 0; $i < 30; $i++) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $daily = getDailyMiningStats($date, $package_filter, $user_filter);
    
    $daily_stats[$date] = $daily;
    
    if ($date >= $start_date && $date <= $end_date) {
        $total_revenue += $daily['total_revenue'];
        $total_electricity_cost += $daily['total_electricity_cost'];
        $total_net_revenue += $daily['total_net_revenue'];
    }
}

// Mining paketlerine göre istatistikler
$package_stats = getPackageMiningStats($start_date, $end_date);

// En çok kazanç sağlayan kullanıcılar
$top_users = getTopMiningUsers($start_date, $end_date, 10);

// Dosya adını oluştur
$filename = "mining_stats_" . date('Ymd') . ".csv";

// CSV başlıkları ve veriler
$headers = [
    'Tarih',
    'Toplam Hash Rate (MH/s)',
    'Brüt Kazanç (USDT)',
    'Elektrik Maliyeti (USDT)',
    'Net Kazanç (USDT)',
    'Kullanıcı Sayısı'
];

// HTTP başlıklarını ayarla
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// CSV Dosyasına yazmak için çıktı tamponu oluştur
$output = fopen('php://output', 'w');

// BOM (Byte Order Mark) ekle - UTF-8 encoding için
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV başlıklarını yaz
fputcsv($output, $headers);

// Tarihleri yeni tarihten eskiye sırala
$dates = array_keys($daily_stats);
rsort($dates);

// Günlük istatistikleri CSV'ye ekle
foreach ($dates as $date) {
    if ($date >= $start_date && $date <= $end_date) {
        $day_stats = $daily_stats[$date];
        
        $row = [
            date('d.m.Y', strtotime($date)),
            number_format($day_stats['total_hash_rate'], 2, '.', ''),
            number_format($day_stats['total_revenue'], 6, '.', ''),
            number_format($day_stats['total_electricity_cost'], 6, '.', ''),
            number_format($day_stats['total_net_revenue'], 6, '.', ''),
            $day_stats['user_count']
        ];
        
        fputcsv($output, $row);
    }
}

// Boş satır ekle
fputcsv($output, []);

// Özet istatistikleri ekle
fputcsv($output, ['Özet İstatistikler']);
fputcsv($output, ['Toplam Brüt Kazanç', number_format($total_revenue, 6, '.', '')]);
fputcsv($output, ['Toplam Elektrik Maliyeti', number_format($total_electricity_cost, 6, '.', '')]);
fputcsv($output, ['Toplam Net Kazanç', number_format($total_net_revenue, 6, '.', '')]);

// Boş satır ekle
fputcsv($output, []);

// Paket bazlı istatistikler
fputcsv($output, ['Paket Bazlı İstatistikler']);
fputcsv($output, [
    'Paket',
    'Aktif Kullanıcı',
    'Toplam Hash Rate (MH/s)',
    'Günlük Kazanç (USDT)',
    'Elektrik Maliyeti (USDT)',
    'Net Kazanç (USDT)',
    'Toplam Dönem (USDT)'
]);

foreach ($package_stats as $stat) {
    $row = [
        $stat['name'],
        $stat['user_count'],
        number_format($stat['total_hash_rate'], 2, '.', ''),
        number_format($stat['daily_revenue'], 6, '.', ''),
        number_format($stat['daily_electricity_cost'], 6, '.', ''),
        number_format($stat['daily_net_revenue'], 6, '.', ''),
        number_format($stat['period_net_revenue'], 6, '.', '')
    ];
    
    fputcsv($output, $row);
}

// Boş satır ekle
fputcsv($output, []);

// En çok kazanan kullanıcılar
fputcsv($output, ['En Çok Kazanan Kullanıcılar']);
fputcsv($output, [
    'Kullanıcı',
    'Hash Rate (MH/s)',
    'Net Kazanç (USDT)'
]);

foreach ($top_users as $user) {
    $row = [
        $user['username'],
        number_format($user['hash_rate'], 2, '.', ''),
        number_format($user['net_revenue'], 6, '.', '')
    ];
    
    fputcsv($output, $row);
}

// Admin log kaydı oluştur
addActionLog($_SESSION['admin_id'], 'export_mining_stats', "Mining istatistikleri dışa aktarıldı: $start_date - $end_date");

// Çıktı tamponunu kapat
fclose($output);
exit;