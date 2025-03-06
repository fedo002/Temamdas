<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Kullanıcı oturum kontrolü
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kullanıcı ve VIP bilgilerini al
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);
$vip_level = $user['vip_level'];
$vip_details = getVipDetails($vip_level);

// Oyun ayarlarını veritabanından al
$game_settings = getGameSettings();

// Oyun aktif mi kontrol et
if ($game_settings['daily_game_active'] != '1') {
    header('Location: dashboard.php?error=game_disabled');
    exit;
}



// Günlük kalan deneme hakkını kontrol et
$attempts_info = getUserDailyAttempts($user_id);
$max_attempts = $vip_details['daily_game_limit'];

// Eğer hata varsa varsayılan olarak 5 hak verelim
if ($attempts_info['error']) {
    $remaining_attempts = $max_attempts;
} else {
    $remaining_attempts = $attempts_info['remaining_attempts'];
}


// VIP seviyesine göre ödül ve şans değerlerini hesapla
$vip_bonus_multiplier = floatval($game_settings['vip_bonus_multiplier']);
$stage1_base_reward = floatval($game_settings['stage1_base_reward']); 
$stage1_win_chance = $vip_details['game_max_win_chance']; // VIP seviyesine göre kazanma şansı

// VIP seviyesine göre ödülleri hesapla
$stage2_rewards = [
    'low' => floatval($game_settings['stage2_low_reward']) + ($vip_level * $vip_bonus_multiplier),
    'medium' => floatval($game_settings['stage2_medium_reward']) + ($vip_level * $vip_bonus_multiplier * 2),
    'high' => floatval($game_settings['stage2_high_reward']) + ($vip_level * $vip_bonus_multiplier * 4)
];

// VIP seviyesine göre şansları hesapla (VIP seviyesi arttıkça yüksek ödül şansı artar)
$vip_chance_adjustment = $vip_level * 0.05; // Her VIP seviyesi için %5 şans kaydırması
$stage2_chances = [
    'low' => max(0.1, floatval($game_settings['stage2_low_chance']) - $vip_chance_adjustment),
    'medium' => floatval($game_settings['stage2_medium_chance']),
    'high' => min(0.9, floatval($game_settings['stage2_high_chance']) + $vip_chance_adjustment)
];

// Şansların toplamının 1.0 olduğundan emin ol
$total_chance = $stage2_chances['low'] + $stage2_chances['medium'] + $stage2_chances['high'];
if (abs($total_chance - 1.0) > 0.01) { // Küçük bir hata payı bırak
    // Şansları normalize et
    $stage2_chances['low'] /= $total_chance;
    $stage2_chances['medium'] /= $total_chance;
    $stage2_chances['high'] /= $total_chance;
}

$page_title = 'Günlük Ödül Oyunu';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="mb-3">Günlük Ödül Oyunu</h1>
                    <p class="lead">Şansını dene, USDT kazan! VIP seviyeniz: <span class="badge bg-primary"><?= $vip_details['name'] ?></span></p>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                        <div class="stat-badge">
                            <i class="fas fa-gamepad me-2"></i> Kalan Hak: <strong><?= $remaining_attempts ?></strong>
                        </div>
                        <div class="stat-badge">
                            <i class="fas fa-trophy me-2"></i> Kazanma Şansı: <strong><?= number_format($stage1_win_chance * 100, 1) ?>%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($remaining_attempts > 0): ?>
    <!-- Stage 1: İlk Aşama Kartları -->
    <div class="row mb-5" id="stage1">
        <div class="col-12 mb-4 text-center">
            <h3>Aşama 1: Kartını Seç</h3>
            <p>İki karttan birini seç. Şansını dene!</p>
        </div>
        
        <div class="col-md-6">
            <div class="game-card" id="card1" onclick="selectCard('card1')">
                <div class="game-card-inner">
                    <div class="game-card-front">
                        <div class="text-center">
                            <i class="fas fa-question-circle fa-4x mb-3"></i>
                            <h4>Kart 1</h4>
                        </div>
                    </div>
                    <div class="game-card-back">
                        <div class="text-center card-result" id="card1-result">
                            <!-- Kart sonucu burada gösterilecek -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="game-card" id="card2" onclick="selectCard('card2')">
                <div class="game-card-inner">
                    <div class="game-card-front">
                        <div class="text-center">
                            <i class="fas fa-question-circle fa-4x mb-3"></i>
                            <h4>Kart 2</h4>
                        </div>
                    </div>
                    <div class="game-card-back">
                        <div class="text-center card-result" id="card2-result">
                            <!-- Kart sonucu burada gösterilecek -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stage 2: İkinci Aşama (gizli, JavaScript ile gösterilecek) -->
    <div class="row mb-5" id="stage2" style="display: none;">
        <div class="col-12 mb-4 text-center">
            <h3>Aşama 2: Ödülünü Kat!</h3>
            <p><span id="stage1-reward"><?= $stage1_base_reward ?></span> USDT'yi çekebilir veya daha fazla kazanmak için şansını deneyebilirsin!</p>
        </div>
        
        <div class="col-md-6 mx-auto">
            <div class="card text-center mb-4">
                <div class="card-body">
                    <h4 class="mb-3">Ne yapmak istersin?</h4>
                    <div class="d-flex gap-3 justify-content-center">
                        <button class="btn btn-success" onclick="takePrize()">
                            <i class="fas fa-check-circle me-2"></i> <span id="stage1-reward-btn"><?= $stage1_base_reward ?></span> USDT'yi Al
                        </button>
                        <button class="btn btn-warning" onclick="doubleOrNothing()">
                            <i class="fas fa-dice me-2"></i> Şansını Dene
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stage 3: Üçüncü Aşama Kartları (gizli, JavaScript ile gösterilecek) -->
    <!-- HTML yapısını düzenleyelim, daily-game.php dosyasında: -->
<!-- Stage 3: Üçüncü Aşama Kartları (gizli, JavaScript ile gösterilecek) -->
<div class="row game-cards-row" id="stage3" style="display: none;">
    <div class="col-12 mb-4 text-center">
        <h3>Aşama 3: Şansını Dene!</h3>
        <p>Üç karttan birini seç ve kazancını katla!</p>
        <p>Olası Ödüller: 
            <span class="badge bg-secondary"><?= number_format($stage2_rewards['low'], 2) ?> USDT</span>
            <span class="badge bg-primary"><?= number_format($stage2_rewards['medium'], 2) ?> USDT</span>
            <span class="badge bg-warning"><?= number_format($stage2_rewards['high'], 2) ?> USDT</span>
        </p>
    </div>
    
    <div class="game-card-col">
        <div class="game-card" id="final-card1" onclick="selectFinalCard('final-card1')">
            <div class="game-card-inner">
                <div class="game-card-front">
                    <div class="text-center">
                        <i class="fas fa-question-circle fa-4x mb-3"></i>
                        <h4>Kart 1</h4>
                    </div>
                </div>
                <div class="game-card-back">
                    <div class="text-center card-result" id="final-card1-result">
                        <!-- Kart sonucu burada gösterilecek -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="game-card-col">
        <div class="game-card" id="final-card2" onclick="selectFinalCard('final-card2')">
            <div class="game-card-inner">
                <div class="game-card-front">
                    <div class="text-center">
                        <i class="fas fa-question-circle fa-4x mb-3"></i>
                        <h4>Kart 2</h4>
                    </div>
                </div>
                <div class="game-card-back">
                    <div class="text-center card-result" id="final-card2-result">
                        <!-- Kart sonucu burada gösterilecek -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="game-card-col">
        <div class="game-card" id="final-card3" onclick="selectFinalCard('final-card3')">
            <div class="game-card-inner">
                <div class="game-card-front">
                    <div class="text-center">
                        <i class="fas fa-question-circle fa-4x mb-3"></i>
                        <h4>Kart 3</h4>
                    </div>
                </div>
                <div class="game-card-back">
                    <div class="text-center card-result" id="final-card3-result">
                        <!-- Kart sonucu burada gösterilecek -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
    <!-- Sonuç Modalı -->
    <div class="modal fade" id="resultModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="resultModalTitle">Oyun Sonucu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="resultContent"></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tamam</button>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-hourglass-end fa-4x mb-3 text-warning"></i>
                    <h3>Günlük oyun hakkınız doldu!</h3>
                    <p class="lead">Yarın tekrar oynamak için gelin veya VIP seviyenizi yükselterek daha fazla oyun hakkı kazanın.</p>
                    <a href="vip-packages.php" class="btn btn-primary mt-3">
                        <i class="fas fa-crown me-2"></i> VIP Paketleri İncele
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Oyun Mekanizması JavaScript Kodları
let gameState = {
    stage: 1,
    selectedCard: null,
    stageOneReward: <?= $stage1_base_reward ?>, // USDT
    reward: 0,
    vipLevel: <?= $vip_level ?>,
    stageRewards: {
        low: <?= $stage2_rewards['low'] ?>,
        medium: <?= $stage2_rewards['medium'] ?>,
        high: <?= $stage2_rewards['high'] ?>
    },
    stageChances: {
        low: <?= $stage2_chances['low'] ?>,
        medium: <?= $stage2_chances['medium'] ?>,
        high: <?= $stage2_chances['high'] ?>
    }
};

function selectCard(cardId) {
    if (gameState.stage !== 1 || gameState.selectedCard !== null) return;
    
    gameState.selectedCard = cardId;
    
    // Kartı çevir
    document.getElementById(cardId).classList.add('flipped');
    
    // AJAX ile sunucudan sonuç al
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'stage=1&card=' + cardId
    })
    .then(response => response.json())
    .then(data => {
        console.log("Server response:", data); // Debugging
        
        if (data.status === 'win') {
            // Kazanç göster
            document.getElementById(cardId + '-result').innerHTML = `
                <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                <h3 class="text-success">${gameState.stageOneReward} USDT Kazandın!</h3>
            `;
            
            // İkinci aşamayı göster
            setTimeout(() => {
                document.getElementById('stage1').style.display = 'none';
                document.getElementById('stage2').style.display = 'block';
                gameState.stage = 2;
                gameState.reward = gameState.stageOneReward;
            }, 1500);
        } else {
            // Kaybetme mesajı
            document.getElementById(cardId + '-result').innerHTML = `
                <i class="fas fa-redo fa-3x text-danger mb-3"></i>
                <h3 class="text-danger">Tekrar Dene!</h3>
            `;
            
            // Modal göster
            setTimeout(() => {
                document.getElementById('resultModalTitle').textContent = 'Üzgünüz!';
                document.getElementById('resultContent').innerHTML = `
                    <i class="fas fa-redo fa-4x text-danger mb-3"></i>
                    <h3>Bu sefer olmadı!</h3>
                    <p>Şansını tekrar deneyebilirsin. Kalan hakkın: ${data.remaining_attempts}</p>
                `;
                
                // Modal göster
                new bootstrap.Modal(document.getElementById('resultModal')).show();
                
                // Sayfa yenileme
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu. Lütfen tekrar deneyin.');
    });
}
function simulateCardResult(cardId) {
    // Random sonuç - gerçek uygulamada sunucudan gelecek
    const winProbability = <?= $stage1_win_chance ?>; // VIP seviyesine göre değişir
    const isWinner = Math.random() < winProbability;
    
    // Kartı çevir
    document.getElementById(cardId).classList.add('flipped');
    
    setTimeout(() => {
        if (isWinner) {
            // Kazanç göster
            document.getElementById(cardId + '-result').innerHTML = `
                <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                <h3 class="text-success">${gameState.stageOneReward} USDT Kazandın!</h3>
            `;
            
            // İkinci aşamayı göster
            setTimeout(() => {
                document.getElementById('stage1').style.display = 'none';
                document.getElementById('stage2').style.display = 'block';
                gameState.stage = 2;
                gameState.reward = gameState.stageOneReward;
                
                // Update the reward text in stage 2
                document.getElementById('stage1-reward').textContent = gameState.stageOneReward;
                document.getElementById('stage1-reward-btn').textContent = gameState.stageOneReward;
            }, 1500);
        } else {
            // Kaybetme mesajı
            document.getElementById(cardId + '-result').innerHTML = `
                <i class="fas fa-redo fa-3x text-danger mb-3"></i>
                <h3 class="text-danger">Tekrar Dene!</h3>
            `;
            
            // Modal göster
            setTimeout(() => {
                document.getElementById('resultModalTitle').textContent = 'Üzgünüz!';
                document.getElementById('resultContent').innerHTML = `
                    <i class="fas fa-redo fa-4x text-danger mb-3"></i>
                    <h3>Bu sefer olmadı!</h3>
                    <p>Şansını tekrar deneyebilirsin. Kalan hakkın: ${<?= $remaining_attempts ?> - 1}</p>
                `;
                
                // Modal göster
                new bootstrap.Modal(document.getElementById('resultModal')).show();
                
                // Sayfa yenileme
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }, 1500);
        }
    }, 500); // Kart dönme efekti için biraz gecikme
}


function doubleOrNothing() {
    // 3. aşamaya geç
    document.getElementById('stage2').style.display = 'none';
    document.getElementById('stage3').style.display = 'block';
    gameState.stage = 3;
    
    // Konsola debugging bilgisi
    console.log("Moving to stage 3. Game state:", gameState);
}

function takePrize() {
    // Ödülü al
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'stage=2&action=take&amount=' + gameState.reward
    })
    .then(response => response.json())
    .then(data => {
        console.log("Take prize response:", data); // Debug için

        if (data.status === 'success') {
            // Modal göster
            document.getElementById('resultModalTitle').textContent = 'Tebrikler!';
            document.getElementById('resultContent').innerHTML = `
                <i class="fas fa-trophy fa-4x text-warning mb-3"></i>
                <h3>${gameState.reward} USDT Kazandın!</h3>
                <p>Kazancın hesabına eklendi.</p>
            `;
            
            // Modal göster
            new bootstrap.Modal(document.getElementById('resultModal')).show();
            
            // Sayfa yenileme
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen bir hata oluştu.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu. Lütfen tekrar deneyin.');
    });
}


function selectFinalCard(cardId) {
    // Debug için log ekleyelim
    console.log("selectFinalCard called with:", cardId, "Current stage:", gameState.stage);
    
    if (gameState.stage !== 3) {
        console.error("Wrong game stage! Expected 3, got:", gameState.stage);
        return;
    }
    
    // Tüm kartları devre dışı bırak
    document.querySelectorAll('#stage3 .game-card').forEach(card => {
        card.style.pointerEvents = 'none';
    });
    
    // Seçilen kartı çevir
    document.getElementById(cardId).classList.add('flipped');
    
    // Debug için konsola yazdırma
    console.log("Selecting final card:", cardId);
    
    // AJAX ile sunucudan sonuç al
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'stage=3&card=' + cardId
    })
    .then(response => {
        console.log("Raw response:", response);
        return response.json();
    })
    .then(data => {
        console.log("Final card response:", data); // Debug için
        
        if (data.status === 'error') {
            alert('Hata: ' + data.message);
            return;
        }
        
        setTimeout(() => {
            // Seçilen kartın sonucunu göster
            let resultHTML;
            const prize = parseFloat(data.prize);
            
            if (prize <= gameState.stageRewards.low) {
                resultHTML = `
                    <i class="fas fa-coins fa-3x text-secondary mb-3"></i>
                    <h3>${prize.toFixed(2)} USDT</h3>
                `;
            } else if (prize <= gameState.stageRewards.medium) {
                resultHTML = `
                    <i class="fas fa-coins fa-3x text-primary mb-3"></i>
                    <h3>${prize.toFixed(2)} USDT</h3>
                `;
            } else {
                resultHTML = `
                    <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                    <h3 class="text-success">${prize.toFixed(2)} USDT</h3>
                `;
            }
            
            // Sonucu göster
            document.getElementById(cardId + '-result').innerHTML = resultHTML;
            
            // Diğer kartları da çevir ve içeriklerini göster
            setTimeout(() => {
                // Diğer kartları da çevir
                document.querySelectorAll('#stage3 .game-card').forEach(card => {
                    if (!card.classList.contains('flipped')) {
                        card.classList.add('flipped');
                        
                        // Rastgele değerler göster
                        const otherCardResult = card.querySelector('.card-result');
                        if (Math.random() > 0.5) {
                            otherCardResult.innerHTML = `
                                <i class="fas fa-coins fa-3x text-secondary mb-3"></i>
                                <h3>${gameState.stageRewards.low.toFixed(2)} USDT</h3>
                            `;
                        } else {
                            otherCardResult.innerHTML = `
                                <i class="fas fa-coins fa-3x text-primary mb-3"></i>
                                <h3>${gameState.stageRewards.medium.toFixed(2)} USDT</h3>
                            `;
                        }
                    }
                });
                
                // Modal göster
                setTimeout(() => {
                    document.getElementById('resultModalTitle').textContent = 'Tebrikler!';
                    document.getElementById('resultContent').innerHTML = `
                        <i class="fas fa-trophy fa-4x text-warning mb-3"></i>
                        <h3>${prize.toFixed(2)} USDT Kazandın!</h3>
                        <p>Kazancın hesabına eklendi.</p>
                    `;
                    
                    new bootstrap.Modal(document.getElementById('resultModal')).show();
                    
                    // Sayfa yenileme
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                }, 1500);
            }, 1000);
        }, 500);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu. Lütfen tekrar deneyin.');
    });
}
</script>

<?php include 'includes/footer.php'; ?>