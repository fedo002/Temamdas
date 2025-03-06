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

$daily_attempts = getUserDailyAttempts($user_id);
$vip_details = getVipDetails($vip_level);
$max_attempts = $vip_details['daily_game_limit'];

$remaining_attempts = max(0, $max_attempts - $daily_attempts['used_attempts']);



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
                            <i class="fas fa-trophy me-2"></i> Kazanma Şansı: <strong><?= ($vip_details['game_max_win_chance'] * 100) ?>%</strong>
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
            <p>5 USDT'yi çekebilir veya daha fazla kazanmak için şansını deneyebilirsin!</p>
        </div>
        
        <div class="col-md-6 mx-auto">
            <div class="card text-center mb-4">
                <div class="card-body">
                    <h4 class="mb-3">Ne yapmak istersin?</h4>
                    <div class="d-flex gap-3 justify-content-center">
                        <button class="btn btn-success" onclick="takePrize()" id="take-prize-btn">
                            <i class="fas fa-check-circle me-2"></i> <?= $gameState['stageOneReward'] ?> USDT'yi Al
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
    <div class="row" id="stage3" style="display: none;">
        <div class="col-12 mb-4 text-center">
            <h3>Aşama 3: Şansını Dene!</h3>
            <p>Üç karttan birini seç ve kazancını katla!</p>
        </div>
        
        <div class="col-md-4">
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
        
        <div class="col-md-4">
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
        
        <div class="col-md-4">
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
let gameState = {
    stage: 1,
    selectedCard: null,
    stageOneReward: 10, // USDT
    reward: 10 // İlk aşamada kazanılan miktar
};

function selectCard(cardId) {
    if (gameState.stage !== 1) return;
    
    gameState.selectedCard = cardId;
    let card = document.getElementById(cardId);
    card.classList.add('flipped');
    
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'stage=1&card=' + cardId
    })
    .then(response => response.json())
    .then(data => {
        console.log('Stage 1 Response:', data);
        
        if (data.status === 'win') {
            document.getElementById(cardId + '-result').innerHTML = `
                <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                <h3 class="text-success">${data.win_amount} USDT Kazandın!</h3>
                <p>Sonuç: ${data.result}</p>
            `;
            
            setTimeout(() => {
                document.getElementById('stage1').style.display = 'none';
                document.getElementById('stage2').style.display = 'flex';
                gameState.stage = 2;
                gameState.reward = data.win_amount;
                gameState.stageOneReward = data.win_amount;
            }, 1500);
        } else {
            document.getElementById(cardId + '-result').innerHTML = `
                <i class="fas fa-redo fa-3x text-danger mb-3"></i>
                <h3 class="text-danger">Tekrar Dene!</h3>
                <p>Sonuç: ${data.result}</p>
            `;
        }
    })
    .catch(error => console.error('Error:', error));
}

function takePrize() {
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'stage=2&action=take&amount=' + gameState.reward
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('resultModalTitle').textContent = 'Tebrikler!';
        document.getElementById('resultContent').innerHTML = `
            <i class="fas fa-trophy fa-4x text-warning mb-3"></i>
            <h3>${gameState.reward} USDT Kazandın!</h3>
            <p>Kazancın hesabına eklendi.</p>
        `;
        new bootstrap.Modal(document.getElementById('resultModal')).show();
        setTimeout(() => { window.location.reload(); }, 3000);
    });
}

function doubleOrNothing() {
    if (gameState.stage !== 2) return;
    document.getElementById('stage2').style.display = 'none';
    document.getElementById('stage3').style.display = 'flex';
    gameState.stage = 3;
}

function selectFinalCard(cardId) {
    if (gameState.stage !== 3) return;
    
    let selectedCard = document.getElementById(cardId);
    selectedCard.classList.add('flipped');
    
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'stage=3&card=' + cardId
    })
    .then(response => response.json())
    .then(data => {
        const resultContent = {
            3: `<i class="fas fa-coins fa-3x text-warning mb-3"></i><h3>3 USDT</h3>`,
            7: `<i class="fas fa-coins fa-3x text-warning mb-3"></i><h3>7 USDT</h3>`,
            10: `<i class="fas fa-crown fa-3x text-warning mb-3"></i><h3 class="text-success">10 USDT</h3>`
        };
        
        document.getElementById(cardId + '-result').innerHTML = resultContent[data.prize];
        
        setTimeout(() => {
            document.getElementById('resultModalTitle').textContent = 'Tebrikler!';
            document.getElementById('resultContent').innerHTML = `
                <i class="fas fa-trophy fa-4x text-warning mb-3"></i>
                <h3>${data.prize} USDT Kazandın!</h3>
                <p>Kazancın hesabına eklendi.</p>
            `;
            new bootstrap.Modal(document.getElementById('resultModal')).show();
            setTimeout(() => { window.location.reload(); }, 3000);
        }, 2000);
    });
}
</script>



<?php include 'includes/footer.php'; ?>