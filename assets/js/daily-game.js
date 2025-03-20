// Game state
let gameState = {
    reward: gameConfig.reward || 5.0,
    cardRewards: gameConfig.cardRewards || {
        low: 3.0,
        medium: 7.0,
        high: 10.0
    },
    inProgress: false,
    timerInterval: null,
    shakeInterval: null,  // Added for random card shaking
    selectedCard: null,
    timeLeft: 30,
    gameStarted: false,  // Track if game has started for auto-reward on leave
    exitAttempt: false,  // Track if user attempted to exit
    currentPrize: 0,     // For storing current prize when retrys offered
    gameAttemptId: 0,    // Store the game attempt ID for updating
    vipLevel: gameConfig.vipLevel || 0,
    remainingRetries: 0,  // Remaining retry attempts
    currentLanguage: gameConfig.currentLang || 'en' // Store current language
};

// Translation function that uses pre-fetched translations
function translate(key, replacements = {}) {
    console.log("Translating key:", key);
    
    if (!key || typeof key !== 'string') {
        console.error("Invalid translation key:", key);
        return key;
    }
    
    // If gameConfig.translations doesn't exist, warn and return the key
    if (!gameConfig.translations) {
        console.error("Translations not loaded! gameConfig:", gameConfig);
        return key;
    }
    
    const parts = key.split('.');
    let value = gameConfig.translations;
    
    // Navigate the translations object
    for (const part of parts) {
        if (value && value[part]) {
            value = value[part];
        } else {
            console.warn(`Translation key not found: ${key}`);
            return key; // Return key if translation not found
        }
    }
    
    // Replace placeholders with actual values
    if (typeof value === 'string' && Object.keys(replacements).length > 0) {
        for (const [search, replace] of Object.entries(replacements)) {
            value = value.replace(`{${search}}`, replace);
        }
    }
    
    return value;
}

// DOM yüklendikten sonra dil algılama ve çeviri için
document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;
    let lang = 'en';
    
    if (path.includes('/ru/')) {
        lang = 'ru';
    } else if (path.includes('/tr/')) {
        lang = 'tr';
    } else if (path.includes('/ka/')) {
        lang = 'ka';
    }
    
    console.log("Language detected from URL path: " + lang);
    gameState.currentLanguage = lang;
    
    // Add no-translate class to elements that shouldn't be translated
    document.querySelectorAll('.modal, .modal-header h3, .modal-body, #resultModalTitle, .card-result, .game-card, .message-box-header h3, .message-box-body p')
        .forEach(el => {
            el.classList.add('no-translate');
            el.setAttribute('data-no-translate', 'true');
        });
    
    // Check previous game state
    const gameWasInProgress = sessionStorage.getItem('gameInProgress');
    
    if (gameWasInProgress) {
        // Clear previous game state
        sessionStorage.removeItem('gameInProgress');
        
        // Force page refresh - not from cache
        if (performance.navigation.type !== 1) { // 1 = Refresh
            forceRefresh();
        }
    }
    
    // Assign random gradients to cards
    const gradients = [
        'linear-gradient(135deg, #7367F0, #4839EB)', // Purple
        'linear-gradient(135deg, #FF9F43, #FF8412)', // Orange
        'linear-gradient(135deg, #28C76F, #1F9D57)', // Green
        'linear-gradient(135deg, #EA5455, #D43A3A)', // Red
        'linear-gradient(135deg, #00CFE8, #00A1B5)', // Blue
        'linear-gradient(135deg, #FCCE54, #E8B10D)'  // Yellow
    ];
    
    // Shuffle the gradients
    const shuffled = [...gradients].sort(() => 0.5 - Math.random());
    
    // Assign to cards
    const cardFronts = document.querySelectorAll('.card-front');
    cardFronts.forEach((front, index) => {
        front.style.background = shuffled[index % shuffled.length];
    });
    
    // Preload all sounds
    const sounds = [
        document.getElementById('cardFlipSound'),
        document.getElementById('winSound'),
        document.getElementById('tensionSound'),
        document.getElementById('dealerSound')
    ];
    
    sounds.forEach(sound => {
        if (sound) {
            sound.load();
        }
    });
    
    // EN SON olarak çevirileri yükle!
    loadTranslations();
});

// Çevirileri asenkron olarak en son yükleyen fonksiyon
function loadTranslations() {
    const currentLang = gameState.currentLanguage || 'en';
    
    // Ajax ile çeviri dosyasını yükle
    fetch(`ajax/get_translations.php?lang=${currentLang}&v=${new Date().getTime()}`)
        .then(response => response.json())
        .then(data => {
            // gameConfig.translations'ı güncellenmiş çevirilerle güncelle
            gameConfig.translations = data.daily_game || {};
            console.log("Çeviriler en son yüklendi:", gameConfig.translations);
            
            // Tüm modalleri yeni çevirilerle güncelle
            updateAllModalsWithTranslations();
        })
        .catch(error => {
            console.error("Çeviri yükleme hatası:", error);
            // Hata durumunda varsayılan çevirileri kullan (zaten yüklendi)
        });
}

// AJAX isteklerinde dil bilgisini gönder
function sendGameRequest(url, params) {
    // Add language information
    params.lang = gameState.currentLanguage;
    
    // Prepare request body
    const body = Object.entries(params)
        .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
        .join('&');
    
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cache-Control': 'no-cache, no-store'
        },
        body: body
    });
}

// Show custom message box
function showMessageBox(title, message, onConfirm, onCancel = null) {
    // Get message box container
    const messageBox = document.getElementById('custom-message-box');
    
    // Update title and message
    document.getElementById('message-box-title').textContent = title;
    document.getElementById('message-box-message').textContent = message;
    
    const confirmBtn = document.getElementById('message-box-confirm');
    const cancelBtn = document.getElementById('message-box-cancel');
    
    // Clear previous event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn = cancelBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
    
    // Add new event listeners
    newConfirmBtn.addEventListener('click', function() {
        messageBox.style.display = 'none';
        if (typeof onConfirm === 'function') onConfirm();
    });
    
    if (onCancel) {
        newCancelBtn.style.display = 'inline-flex';
        newCancelBtn.addEventListener('click', function() {
            messageBox.style.display = 'none';
            onCancel();
        });
    } else {
        newCancelBtn.style.display = 'none';
    }
    
    // Show message box
    messageBox.style.display = 'flex';
}

// Prevent page leaving when game is in progress
window.addEventListener('beforeunload', function(e) {
    if (gameState.inProgress && gameState.gameStarted && !gameState.selectedCard && gameState.gameAttemptId > 0) {
        // Kullanıcı oyundayken sayfa kapatılırsa, deneme hakkını düşür
        navigator.sendBeacon('ajax/play_game.php', new URLSearchParams({
            stage: 2,
            action: 'exit',
            attempt_id: gameState.gameAttemptId
        }));
        
        e.preventDefault();
        e.returnValue = translate('modals.confirmation.try_luck_warning');
        return e.returnValue;
    }
    
    // If game completed and reload needed
    if (gameState.selectedCard && !gameState.inProgress) {
        // Clear user game state
        sessionStorage.removeItem('gameInProgress');
    } else if (gameState.inProgress && gameState.gameStarted) {
        // If game is in progress, save state
        sessionStorage.setItem('gameInProgress', 'true');
    }
});

// Force refresh the page (not from cache)
function forceRefresh() {
    window.location.href = window.location.href.split('?')[0] + '?refresh=' + new Date().getTime();
}

// Take the prize directly
function takePrize() {
    if (gameState.inProgress) return;
    
    // Show loading overlay
    showLoading();
    
    // Send request with language information
    sendGameRequest('ajax/play_game.php', {
        stage: 1, 
        action: 'take', 
        amount: gameState.reward
    })
    .then(response => response.json())
    .then(data => {
        console.log('Take prize response:', data);
        
        // Hide loading overlay
        hideLoading();
        
        if (data.status === 'success') {
            // Play win sound
            playSound('winSound');
            
            // Get translations (server or local)
            const title = translate('modals.result.title');
            const message = translate('modals.result.message');
            
            // Show success modal with translated content
            showResultModal(title, `
                <i class="fas fa-trophy" style="font-size: 3rem; color: gold; margin-bottom: 15px;"></i>
                <h3>${gameState.reward} USDT</h3>
                <p>${message}</p>
            `);
            
            // Reload page after modal is closed
            document.getElementById('resultModal').addEventListener('hidden', function() {
                forceRefresh();
            }, { once: true });
        } else {
            // Error handling with translations
            const errorTitle = translate('modals.error.title');
            const errorMessage = translate('modals.error.try_again');
            
            showResultModal(errorTitle, `
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                <h3>${errorTitle}</h3>
                <p>${errorMessage}</p>
            `);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoading();
        
        const errorTitle = translate('modals.error.title');
        const connectionError = translate('modals.error.connection_error');
        const checkConnection = translate('modals.error.check_connection');
        
        showResultModal(errorTitle, `
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${connectionError}</h3>
            <p>${checkConnection}</p>
        `);
        
        // Reload page
        setTimeout(() => {
            forceRefresh();
        }, 2000);
    });
}

// Go to cards selection
function doubleOrNothing() {
    if (gameState.inProgress) return;
    
    // Show confirmation modal
    showResultModal(
        translate('modals.confirmation.title'),
        `<i class="fas fa-question-circle" style="font-size: 3rem; color: #ff9f43; margin-bottom: 15px;"></i>
        <p>${translate('modals.confirmation.try_luck_warning')}</p>`
    );
    
    // Add custom buttons
    const footerElement = document.getElementById('resultModalFooter');
    if (footerElement) {
        footerElement.innerHTML = `
            <button onclick="confirmDoubleOrNothing()" class="btn btn-warning mr-2">
                ${translate('modals.confirmation.btn_confirm')}
            </button>
            <button onclick="closeModal()" class="btn btn-outline">
                ${translate('modals.confirmation.btn_cancel')}
            </button>
        `;
    }
}

// Confirm double or nothing
function confirmDoubleOrNothing() {
    // Close modal
    closeModal();
    
    // Create initial record
    createInitialRecord().then(result => {
        // Store attempt ID
        if (result && result.attempt_id) {
            gameState.gameAttemptId = result.attempt_id;
        }
        // Start game
        startDoubleOrNothingGame();
    }).catch(error => {
        console.error("Error creating initial record:", error);
        
        // Check if daily limit exceeded
        if (error.message && error.message.includes("Daily attempt limit")) {
            showResultModal(
                translate('modals.error.title'), 
                `<i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                <h3>${translate('modals.error.no_attempts')}</h3>
                <p>${translate('modals.error.try_tomorrow')}</p>`
            );
            return;
        }
        
        // Start game anyway for better user experience
        startDoubleOrNothingGame();
    });
}


// Create initial record with lowest prize
function createInitialRecord() {
    return new Promise((resolve, reject) => {
        // Show loading temporarily
        showLoading();
        
        // Create initial record
        sendGameRequest('ajax/play_game.php', {
            stage: 2,
            card: 'initial',
            record_only: 1
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            console.log('Initial record created:', data);
            
            if (data.status === 'success') {
                resolve(data);
            } else {
                reject(new Error(data.message || "Failed to create initial record"));
                
                // If error is daily limit
                if (data.message && data.message.includes("Daily attempt limit")) {
                    showResultModal(
                        translate('modals.error.title'), 
                        `<i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                        <h3>${translate('modals.error.no_attempts')}</h3>
                        <p>${translate('modals.error.try_tomorrow')}</p>`
                    );
                    
                    // Reload page
                    setTimeout(() => {
                        forceRefresh();
                    }, 2000);
                }
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error creating initial record:', error);
            reject(error);
        });
    });
}

// Start the double or nothing game
function startDoubleOrNothingGame() {
    // Set game in progress
    gameState.inProgress = true;
    gameState.gameStarted = true;
    
    // Hide direct reward, show card selection
    document.getElementById('direct-reward').style.display = 'none';
    document.getElementById('card-selection').style.display = 'block';
    
    // Show dealer animation
    const dealerAnimation = document.getElementById('dealerAnimation');
    dealerAnimation.style.display = 'block';
    
    // Play dealer shuffling sound
    playSound('dealerSound');
    
    // Start dealer animation
    setTimeout(() => {
        // Animate cards being dealt
        const card1 = document.querySelector('.dealing-card.card1');
        const card2 = document.querySelector('.dealing-card.card2');
        const card3 = document.querySelector('.dealing-card.card3');
        
        card1.style.animation = 'dealCard1 0.6s ease-out forwards';
        
        setTimeout(() => {
            card2.style.animation = 'dealCard2 0.6s ease-out forwards';
        }, 200);
        
        setTimeout(() => {
            card3.style.animation = 'dealCard3 0.6s ease-out forwards';
        }, 400);
        
        // After dealing, show actual cards
        setTimeout(() => {
            // Show the game cards
            const gameCards = document.querySelectorAll('.game-card');
            gameCards.forEach(card => {
                card.style.display = 'block';
            });
            
            // Fade out dealer animation
            dealerAnimation.style.opacity = '0';
            dealerAnimation.style.transition = 'opacity 0.5s';
            
            // Start tension music
            playSound('tensionSound');
            
            // Start random card shaking
            startRandomCardShaking();
            
            // Hide dealer animation
            setTimeout(() => {
                dealerAnimation.style.display = 'none';
                
                // Start timer
                startTimer();
            }, 500);
        }, 1500);
    }, 1000);
}

// Start random card shaking animation
function startRandomCardShaking() {
    const gameCards = document.querySelectorAll('.game-card');
    let currentCard = null;
    let originalBackground = '';
    const highlightColor = 'linear-gradient(135deg, #FF9F43, #FF8412)'; // Orange highlight
    
    // Remove any existing shake classes
    gameCards.forEach(card => {
        card.classList.remove('shake');
    });
    
    // Function to shake a random card
    const shakeRandomCard = () => {
        // Remove shake from current card and restore color if it exists
        if (currentCard) {
            currentCard.classList.remove('shake');
            const front = currentCard.querySelector('.card-front');
            if (front && originalBackground) {
                front.style.background = originalBackground;
            }
        }
        
        // Select a random card
        const randomIndex = Math.floor(Math.random() * gameCards.length);
        currentCard = gameCards[randomIndex];
        
        // Store original background
        const front = currentCard.querySelector('.card-front');
        if (front) {
            originalBackground = front.style.background;
            front.style.background = highlightColor;
        }
        
        // Add shake to selected card
        currentCard.classList.add('shake');
    };
    
    // Initial shake
    shakeRandomCard();
    
    // Initialize shaking interval
    gameState.shakeInterval = setInterval(shakeRandomCard, 1000);
}

// Stop card shaking
function stopCardShaking() {
    if (gameState.shakeInterval) {
        clearInterval(gameState.shakeInterval);
        
        // Remove shake from all cards
        const gameCards = document.querySelectorAll('.game-card');
        gameCards.forEach(card => {
            card.classList.remove('shake');
        });
    }
}

// Start countdown timer
function startTimer() {
    // Show timer container
    const timerContainer = document.getElementById('timer-container');
    timerContainer.style.display = 'block';
    
    // Initialize time
    gameState.timeLeft = 30;
    document.getElementById('timer-seconds').textContent = gameState.timeLeft;
    
    // Start timer interval
    gameState.timerInterval = setInterval(() => {
        gameState.timeLeft--;
        
        // Update timer display
        document.getElementById('timer-seconds').textContent = gameState.timeLeft;
        
        // Update progress bar
        const progress = document.getElementById('timer-progress');
        progress.style.width = (gameState.timeLeft / 30 * 100) + '%';
        
        // Change colors based on time left
        if (gameState.timeLeft <= 10) {
            progress.style.backgroundColor = '#EA5455';
        } else if (gameState.timeLeft <= 20) {
            progress.style.backgroundColor = '#FF9F43';
        }
        
        // Time's up
        if (gameState.timeLeft <= 0) {
            clearInterval(gameState.timerInterval);
            timeUp();
        }
    }, 1000);
}

// Handle time up (no card selected)
function timeUp() {
    if (gameState.selectedCard) return; // Card already selected
    
    // Stop timer and music
    clearInterval(gameState.timerInterval);
    stopCardShaking();
    stopSound('tensionSound');
    
    // Make automatic selection
    makeAutomaticSelection();
}

// Make automatic selection (timeout)
function makeAutomaticSelection() {
    console.log("Making automatic selection for timeout");
    
    showLoading();
    
    // Attempt_id kontrolü ekle
    if (!gameState.gameAttemptId) {
        console.error("Missing attempt_id in timeout");
        // Sayfayı yeniden yükle
        forceRefresh();
        return;
    }
    
    sendGameRequest('ajax/play_game.php', {
        stage: 2,
        card: 'timeout',
        attempt_id: gameState.gameAttemptId
    })
    .then(response => response.json())
    .then(data => {
        console.log("Timeout selection response:", data);
        
        hideLoading();
        
        if (data.status === 'success') {
            const prize = parseFloat(data.prize || gameState.cardRewards.low);
            
            // Get translations
            const title = translate('modals.timeout.title');
            const message1 = translate('modals.timeout.message1');
            const message2 = translate('modals.timeout.message2');
            
            showResultModal(
                title,
                `<i class="fas fa-clock" style="font-size: 3rem; color: #ff9f43; margin-bottom: 15px;"></i>
                <p>${message1}</p>
                <p>${message2}</p>`
            );
            
            // Reload page when modal is closed
            document.getElementById('resultModal').addEventListener('hidden', function() {
                forceRefresh();
            }, { once: true });
        }
        
        // Game completed
        gameState.inProgress = false;
    })
    .catch(error => {
        console.error("Error in timeout selection:", error);
        hideLoading();
        
        // Error messages with translations
        const errorTitle = translate('modals.error.title');
        const connectionError = translate('modals.error.connection_error');
        const checkConnection = translate('modals.error.check_connection');
        
        showResultModal(
            errorTitle, 
            `<i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${connectionError}</h3>
            <p>${checkConnection}</p>`
        );
        
        // Game completed
        gameState.inProgress = false;
    });
}

// Select card and handle result
function selectCard(cardId) {
    // If card already selected or game not in progress, do nothing
    if (gameState.selectedCard || !gameState.inProgress) return;
    
    console.log("Card selected: " + cardId);
    gameState.selectedCard = cardId;
    
    // Clear timer
    clearInterval(gameState.timerInterval);
    document.getElementById('timer-container').style.display = 'none';
    
    // Stop shaking and tension music
    stopCardShaking();
    stopSound('tensionSound');
    
    // Disable card selection
    const gameCards = document.querySelectorAll('.game-card');
    gameCards.forEach(card => {
        card.style.pointerEvents = 'none';
    });
    
    // Flip the selected card
    const selectedCard = document.getElementById(cardId);
    if (selectedCard) {
        selectedCard.classList.add('flipped');
        
        // Play card flip sound
        playSound('cardFlipSound');
    }
    
    // Show loading overlay
    showLoading();
    
    // Attempt_id kontrolü ekle
    if (!gameState.gameAttemptId) {
        console.error("Missing attempt_id in card selection");
        hideLoading();
        
        // Hata durumunda
        const errorTitle = translate('modals.error.title');
        const genericError = translate('modals.error.generic_error');
        
        showResultModal(
            errorTitle, 
            `<i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${genericError}</h3>
            <p>Game session error. Please try again.</p>`
        );
        
        // Sayfayı yeniden yükle
        setTimeout(() => {
            forceRefresh();
        }, 2000);
        return;
    }
    
    // Send card selection request
    sendGameRequest('ajax/play_game.php', {
        stage: 2,
        card: cardId,
        attempt_id: gameState.gameAttemptId
    })
    .then(response => response.json())
    .then(data => {
        console.log("Card selection response:", data);
        
        // Hide loading overlay
        hideLoading();
        
        // Get retry info if available
        if (data.status === 'success') {
            if (data.hasOwnProperty('can_try_again')) {
                gameState.canTryAgain = data.can_try_again;
            }
            
            if (data.hasOwnProperty('remaining_retries')) {
                gameState.remainingRetries = data.remaining_retries;
            }
        }
        
        // Show result on selected card
        setTimeout(() => {
            const resultElement = document.getElementById(cardId + '-result');
            
            if (data.status === 'success') {
                const prize = parseFloat(data.prize || 0);
                const prizeType = data.prize_type || 'low';
                
                // Create result content
                let resultHTML;
                if (prizeType === 'low') {
                    resultHTML = `
                        <i class="fas fa-coins" style="font-size: 3rem; color: #6c757d; margin-bottom: 10px;"></i>
                        <h3>${prize.toFixed(2)} USDT </h3>
                    `;
                } else if (prizeType === 'medium') {
                    resultHTML = `
                        <i class="fas fa-coins" style="font-size: 3rem; color: #4a00e0; margin-bottom: 10px;"></i>
                        <h3>${prize.toFixed(2)} USDT</h3>
                    `;
                } else {
                    resultHTML = `
                        <i class="fas fa-crown" style="font-size: 3rem; color: gold; margin-bottom: 10px;"></i>
                        <h3>${prize.toFixed(2)} USDT</h3>
                    `;
                    
                    // Create confetti for high prizes
                    createConfetti();
                }
                
                if (resultElement) {
                    resultElement.innerHTML = resultHTML;
                }
                
                // Play win sound
                playSound('winSound');
                
                // Reveal other cards
                setTimeout(() => {
                    // Get all card IDs except selected one
                    const otherCardIds = ['card1', 'card2', 'card3'].filter(id => id !== cardId);
                    
                    // Reveal other cards with sequential animation
                    otherCardIds.forEach((otherId, index) => {
                        setTimeout(() => {
                            revealOtherCard(otherId, data.all_cards[otherId]);
                        }, index * 500);
                    });
                    
                    // Check if we should offer retry option for low rewards
                    if (data.can_try_again && prizeType === 'low') {
                        // Show retry option after all cards revealed
                        setTimeout(() => {
                            // Store current prize for retry handling
                            gameState.currentPrize = prize;
                            
                            // Get translations
                            const title = translate('modals.result.title');
                            const tryAgainMessage = translate('modals.result.try_again_message');
                            
                            // Show result modal with retry option
                            showResultModalWithRetry(
                                title, 
                                `<i class="fas fa-coins" style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                                <h3>${prize.toFixed(2)} USDT</h3>
                                <p class="no-translate" data-no-translate="true">Add your reward to your account or</p>
                                <p class="mt-3 no-translate" data-no-translate="true">${tryAgainMessage}</p>`,
                                prize
                            );
                        }, otherCardIds.length * 500 + 1000);
                    } else {
                        // Regular result modal after all cards revealed
                        setTimeout(() => {
                            let modalIcon, modalColor;
                            
                            if (prizeType === 'low') {
                                modalIcon = 'fa-coins';
                                modalColor = '#6c757d';
                            } else if (prizeType === 'medium') {
                                modalIcon = 'fa-coins';
                                modalColor = '#4a00e0';
                            } else {
                                modalIcon = 'fa-crown';
                                modalColor = 'gold';
                            }
                            
                            // Get translations
                            const title = translate('modals.result.title');
                            const message = translate('modals.result.message');
                            
                            showResultModal(
                                title, 
                                `<i class="fas ${modalIcon}" style="font-size: 3rem; color: ${modalColor}; margin-bottom: 15px;"></i>
                                <h3>${prize.toFixed(2)} USDT</h3>
                                <p class="no-translate" data-no-translate="true">${message}</p>`
                            );
                            
                            // Reload page after closing modal
                            document.getElementById('resultModal').addEventListener('hidden', function() {
                                forceRefresh();
                            }, { once: true });
                        }, otherCardIds.length * 500 + 1000);
                    }
                    
                    // Game completed
                    gameState.inProgress = false;
                }, 1000);
            } else {
                // Error handling
                const errorTitle = translate('modals.error.title');
                const genericError = translate('modals.error.generic_error');
                const tryAgain = translate('modals.error.try_again');
                
                if (resultElement) {
                    resultElement.innerHTML = `
                        <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ea5455; margin-bottom: 10px;"></i>
                        <h3>${genericError}</h3>
                        <p>${data.message || tryAgain}</p>
                    `;
                }
                
                // Show error modal
                showResultModal(
                    errorTitle, 
                    `<i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                    <h3>${genericError}</h3>
                    <p class="no-translate" data-no-translate="true">${data.message || tryAgain}</p>`
                );
                
                // Reload page
                setTimeout(() => {
                    forceRefresh();
                }, 3000);
                
                // Game completed
                gameState.inProgress = false;
            }
        }, 500);
    })
    .catch(error => {
        console.error("Error caught:", error);
        hideLoading();
        
        // Error messages with translations
        const errorTitle = translate('modals.error.title');
        const genericError = translate('modals.error.generic_error');
        const connectionError = translate('modals.error.connection_error');
        const checkConnection = translate('modals.error.check_connection');
        
        // Show error in card
        const resultElement = document.getElementById(cardId + '-result');
        if (resultElement) {
            resultElement.innerHTML = `
                <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ea5455; margin-bottom: 10px;"></i>
                <h3>${genericError}</h3>
                <p>${error.message || checkConnection}</p>
            `;
        }
        
        // Show error modal
        showResultModal(
            errorTitle, 
            `<i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${connectionError}</h3>
            <p class="no-translate" data-no-translate="true">${checkConnection}</p>`
        );
        
        // Reload page
        setTimeout(() => {
            forceRefresh();
        }, 3000);
        
        // Game completed
        gameState.inProgress = false;
    });
}

// Show result modal with retry option
function showResultModalWithRetry(title, content, currentPrize) {
    const modal = document.getElementById('resultModal');
    const backdrop = document.getElementById('backdrop');
    const titleElement = document.getElementById('resultModalTitle');
    const contentElement = document.getElementById('resultContent');
    const footerElement = document.getElementById('resultModalFooter');
    
    if (titleElement) titleElement.textContent = title;
    if (contentElement) contentElement.innerHTML = content;
    
    // Add remaining retries info
    if (contentElement && gameState.hasOwnProperty('remainingRetries')) {
        const retriesInfo = document.createElement('p');
        retriesInfo.className = 'mt-2 text-muted no-translate';
        retriesInfo.setAttribute('data-no-translate', 'true');
        retriesInfo.innerHTML = `<small>${translate('modals.result.remaining_retries')}: <strong>${gameState.remainingRetries}</strong></small>`;
        contentElement.appendChild(retriesInfo);
    }
    
    // Add retry button to footer
    if (footerElement) {
        footerElement.innerHTML = `
            <button onclick="tryAgain()" class="btn btn-warning mr-2">
                <i class="fas fa-dice"></i> ${translate('modals.result.btn_try_again')}
            </button>
            <button onclick="closeModalR()" class="btn btn-primary">
                ${translate('modals.result.btn_ok')}
            </button>
        `;
    }
    
    // Store current prize in game state for retry
    gameState.currentPrize = currentPrize;
    
    // Show modal and backdrop with animation
    modal.style.display = 'block';
    backdrop.style.display = 'block';
    
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
    }, 10);
    
    // Add hidden event for later use
    modal.addEventListener('hidden', function() {
        // Custom event that can be listened for
    }, { once: true });
}

// Try again function
function tryAgain() {
    // Close current result modal
    closeModal();
    
    // Show loading screen
    showLoading();
    
    // Send retry request
    sendGameRequest('ajax/play_game.php', {
        stage: 2,
        action: 'retry',
        current_prize: gameState.currentPrize,
        attempt_id: gameState.gameAttemptId
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        console.log('Retry response:', data);
        
        if (data.status === 'success') {
            // Store remaining retry attempts
            if (data.hasOwnProperty('remaining_retries')) {
                gameState.remainingRetries = data.remaining_retries;
            }
            
            // Reset game state
            gameState.selectedCard = null;
            gameState.inProgress = false;
            gameState.gameStarted = false;
            
            // Get new attempt ID if available
            if (data.attempt_id) {
                gameState.gameAttemptId = data.attempt_id;
            }
            
            // Reset flipped cards
            const gameCards = document.querySelectorAll('.game-card');
            gameCards.forEach(card => {
                card.classList.remove('flipped');
                card.style.display = 'none';
                card.style.pointerEvents = 'auto';
                
                // Reset card results
                const resultElement = card.querySelector('.card-result');
                if (resultElement) {
                    resultElement.innerHTML = '';
                }
            });
            
            // Reset card selection area
            document.getElementById('direct-reward').style.display = 'none';
            document.getElementById('card-selection').style.display = 'block';
            
            // Reset dealer animation
            const dealerAnimation = document.getElementById('dealerAnimation');
            if (dealerAnimation) {
                dealerAnimation.style.display = 'none';
                dealerAnimation.style.opacity = '1';
            }
            
            // Clear timer UI
            const timerContainer = document.getElementById('timer-container');
            if (timerContainer) {
                timerContainer.style.display = 'none';
            }
            
            // Reset animation states
            const dealingCards = document.querySelectorAll('.dealing-card');
            dealingCards.forEach(card => {
                card.style.animation = 'none';
                card.offsetHeight; // Force reflow
                card.style.opacity = '0';
            });
            
            // Restart the game
            setTimeout(() => {
                startDoubleOrNothingGame();
            }, 100);
        } else {
            // Error messages with translations
            const errorTitle = translate('modals.error.title');
            const genericError = translate('modals.error.generic_error');
            const tryAgain = translate('modals.error.try_again');
            
            showResultModal(
                errorTitle, 
                `<i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                <h3 class="no-translate" data-no-translate="true">${genericError}</h3>
                <p class="no-translate" data-no-translate="true">${data.message || tryAgain}</p>`
            );
            
            // Reload after error
            setTimeout(() => {
                forceRefresh();
            }, 2000);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        
        // Error messages with translations
        const errorTitle = translate('modals.error.title');
        const connectionError = translate('modals.error.connection_error');
        const checkConnection = translate('modals.error.check_connection');
        
        showResultModal(
            errorTitle, 
            `<i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3 class="no-translate" data-no-translate="true">${connectionError}</h3>
            <p class="no-translate" data-no-translate="true">${checkConnection}</p>`
        );
        
        // Reload after error
        setTimeout(() => {
            forceRefresh();
        }, 2000);
    });
}

// Reveal other cards function
function revealOtherCard(cardId, cardInfo) {
    const otherCard = document.getElementById(cardId);
    const resultElement = document.getElementById(cardId + '-result');
    
    if (!otherCard || !resultElement) {
        console.error("Card or result element not found: " + cardId);
        return;
    }
    
    // Play card flip sound
    playSound('cardFlipSound');
    
    // Flip the card
    otherCard.classList.add('flipped');
    
    // Get prize info
    const prize = parseFloat(cardInfo.prize || 0);
    const prizeType = cardInfo.type || 'low';
    
    // Create content based on card result
    let resultHTML;
    if (prizeType === 'low') {
        resultHTML = `
            <i class="fas fa-coins" style="font-size: 3rem; color: #6c757d; margin-bottom: 10px;"></i>
            <h3>${prize.toFixed(2)} USDT</h3>
        `;
    } else if (prizeType === 'medium') {
        resultHTML = `
            <i class="fas fa-coins" style="font-size: 3rem; color: #4a00e0; margin-bottom: 10px;"></i>
            <h3>${prize.toFixed(2)} USDT</h3>
        `;
    } else {
        resultHTML = `
            <i class="fas fa-crown" style="font-size: 3rem; color: gold; margin-bottom: 10px;"></i>
            <h3>${prize.toFixed(2)} USDT</h3>
        `;
    }
    
    // Show result with a slight delay for animation
    setTimeout(() => {
        resultElement.innerHTML = resultHTML;
    }, 300);
}

// Create confetti effect for big wins
function createConfetti() {
    const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#00ffff', '#ff00ff'];
    const confettiCount = 100;
    const container = document.querySelector('body');
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
        confetti.style.animationDelay = Math.random() * 2 + 's';
        
        container.appendChild(confetti);
        
        // Remove confetti after animation
        setTimeout(() => {
            confetti.remove();
        }, 5000);
    }
}

// Show loading overlay
function showLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.classList.add('show');
    
    // Update loading text with translation
    const loadingText = loadingOverlay.querySelector('p');
    if (loadingText) {
        loadingText.textContent = translate('loading');
    }
}

// Hide loading overlay
function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.classList.remove('show');
}

// Play a sound
function playSound(soundId) {
    const sound = document.getElementById(soundId);
    if (sound) {
        sound.currentTime = 0;
        sound.play().catch(error => {
            console.log("Sound play error:", error);
        });
    }
}

// Stop a sound
function stopSound(soundId) {
    const sound = document.getElementById(soundId);
    if (sound) {
        sound.pause();
        sound.currentTime = 0;
    }
}

// Show result modal with translations
function showResultModal(title, content) {
    const modal = document.getElementById('resultModal');
    const backdrop = document.getElementById('backdrop');
    const titleElement = document.getElementById('resultModalTitle');
    const contentElement = document.getElementById('resultContent');
    const footerElement = document.getElementById('resultModalFooter');
    
    if (titleElement) {
        // Check if this is a translation key
        if (title.startsWith('modals.') || 
            title === 'congratulations' || 
            title === 'error' || 
            title === 'connection_error') {
            titleElement.textContent = translate(title);
        } else {
            titleElement.textContent = title;
        }
    }
    
    // For content, we need to be more careful since it contains HTML
    if (contentElement) {
        // Insert content as is since it's HTML
        contentElement.innerHTML = content;
        
        // Find any elements with translation keys and translate them
        contentElement.querySelectorAll('[data-translate-key]').forEach(el => {
            const key = el.getAttribute('data-translate-key');
            el.textContent = translate(key);
        });
    }
    
    // Reset footer to default with translated button text
    if (footerElement) {
        footerElement.innerHTML = `
            <button onclick="closeModal()" class="btn btn-primary">
                ${translate('modals.result.btn_ok')}
            </button>
        `;
    }
    
    // Show modal and backdrop with animation
    modal.style.display = 'block';
    backdrop.style.display = 'block';
    
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
    }, 10);
    
    // Add hidden event for later use
    modal.addEventListener('hidden', function() {
        // Custom event that can be listened for
    }, { once: true });
}

// Close modal
function closeModal() {
    const modal = document.getElementById('resultModal');
    const backdrop = document.getElementById('backdrop');
    
    if (!modal || !backdrop) return;
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
        
        // Dispatch custom event
        modal.dispatchEvent(new Event('hidden'));
    }, 300);
}

// Close modal and reload page
function closeModalR() {
    const modal = document.getElementById('resultModal');
    const backdrop = document.getElementById('backdrop');
    
    if (!modal || !backdrop) return;
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
        
        // Dispatch custom event
        modal.dispatchEvent(new Event('hidden'));
        forceRefresh();
    }, 300);
}

// Update all modals with translations
function updateAllModalsWithTranslations() {
    // Update timeout modal
    const timeoutModal = document.getElementById('countdownModal');
    if (timeoutModal) {
        const timeoutTitle = timeoutModal.querySelector('.modal-header h3');
        if (timeoutTitle) {
            timeoutTitle.textContent = translate('modals.timeout.title');
        }
        
        const timeoutMessages = timeoutModal.querySelectorAll('.modal-body p');
        if (timeoutMessages.length >= 2) {
            timeoutMessages[0].textContent = translate('modals.timeout.message1');
            timeoutMessages[1].textContent = translate('modals.timeout.message2');
        }
        
        const timeoutButton = timeoutModal.querySelector('.modal-footer button');
        if (timeoutButton) {
            timeoutButton.textContent = translate('modals.timeout.btn_ok');
        }
    }
    
    // Update message box
    const messageBox = document.getElementById('custom-message-box');
    if (messageBox) {
        const messageTitle = messageBox.querySelector('.message-box-header h3');
        if (messageTitle) {
            messageTitle.textContent = translate('modals.confirmation.title');
        }
        
        const messageText = messageBox.querySelector('.message-box-body p');
        if (messageText) {
            messageText.textContent = translate('modals.confirmation.try_luck_warning');
        }
        
        const confirmButton = messageBox.querySelector('#message-box-confirm');
        if (confirmButton) {
            confirmButton.textContent = translate('modals.confirmation.btn_confirm');
        }
        
        const cancelButton = messageBox.querySelector('#message-box-cancel');
        if (cancelButton) {
            cancelButton.textContent = translate('modals.confirmation.btn_cancel');
        }
    }
    
    // Update loading overlay
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        const loadingText = loadingOverlay.querySelector('p');
        if (loadingText) {
            loadingText.textContent = translate('loading');
        }
    }
}