<?php
// cleanup_initial_records.php
// Bu dosyayı oluşturun ve işlemi manuel olarak çalıştırın

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Veritabanı bağlantısı
$conn = dbConnect();

// 24 saatten eski 'initial' durumundaki kayıtları temizle
$cleanup_query = "UPDATE game_attempts 
                 SET attempt_result = 'exit' 
                 WHERE attempt_result = 'initial' 
                 AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 24";

$result = $conn->query($cleanup_query);

if ($result) {
    $affected_rows = $conn->affected_rows;
    echo "Temizleme işlemi tamamlandı. $affected_rows kayıt güncellendi.";
} else {
    echo "Hata: " . $conn->error;
}

// Ayrıca, son 24 saat içindeki 'initial' durumunda kalan kayıtları listele
$list_query = "SELECT id, user_id, created_at FROM game_attempts 
              WHERE attempt_result = 'initial' 
              AND TIMESTAMPDIFF(HOUR, created_at, NOW()) <= 24 
              ORDER BY created_at DESC";

$list_result = $conn->query($list_query);

if ($list_result->num_rows > 0) {
    echo "<h2>Son 24 saatteki 'initial' durumunda kalan kayıtlar:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>User ID</th><th>Created At</th></tr>";
    
    while ($row = $list_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Son 24 saatte 'initial' durumunda kalan kayıt bulunamadı.</p>";
}

$conn->close();
?>