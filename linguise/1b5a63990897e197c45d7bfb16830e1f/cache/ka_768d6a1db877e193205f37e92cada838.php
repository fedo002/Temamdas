<?php die(); ?><!DOCTYPE html><html lang="ka" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10D2;&#x10D4;&#x10D1;&#x10D4;&#x10D1;&#x10D8; | Digiminex &#x10DB;&#x10DD;&#x10D1;&#x10D8;&#x10DA;&#x10E3;&#x10E0;&#x10D8;</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
    
    
    <!-- Prevent phone number detection -->
    <meta name="format-detection" content="telephone=no">
    
    <script defer src="assets/js/all.js"></script>
    <link href="assets/css/fontawesome.css" rel="stylesheet">
    <link href="assets/css/brands.css" rel="stylesheet">
    <link href="assets/css/solid.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    
    <!-- Mobile CSS -->
    <link rel="stylesheet" href="assets/css/mobile.css">
    <link rel="stylesheet" href="assets/css/translation-system.css">

	
	<script async src="https://static.linguise.com/script-js/switcher.bundle.js?d=pk_YNgK025ZNYdLQVprpwbLmTm0DljVi7ht"></script>
	
    <!-- Page-specific CSS -->
        <link rel="stylesheet" href="assets/css/mobile-transactions.css">
    <style>
	.linguise_switcher .linguise_switcher_popup {
		background: #121212;
		color: #3a86ff;
}
	</style></head>
	
		
<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <div class="header-container">
            <a class="header-logo" href="/ka/index.php">
                <img src="assets/images/logo.png" alt="Digiminex" height="60">
            </a>
        </div>

    </header>

    <!-- Main Content Container -->
    <main class="mobile-content">
        <!-- Page content will be here -->
<div class="transactions-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 data-i18n="transactions.title">&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10D2;&#x10D4;&#x10D1;&#x10D8;&#x10E1; &#x10D8;&#x10E1;&#x10E2;&#x10DD;&#x10E0;&#x10D8;&#x10D0;</h1>
        
        <!-- Filter Button (opens filter modal) -->
        <button id="showFilterBtn" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-filter"></i>
            <span data-i18n="transactions.filter">&#x10E4;&#x10D8;&#x10DA;&#x10E2;&#x10E0;&#x10D8;</span>
        </button>
    </div>
    
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <div class="scroll-container">
            <a href="/ka/transactions.php?filter=all" class="filter-tab active" data-i18n="transactions.all">&#x10E7;&#x10D5;&#x10D4;&#x10DA;&#x10D0;</a>
             <a href="/ka/transactions.php?filter=deposit" class="filter-tab " data-i18n="transactions.deposit">&#x10D3;&#x10D4;&#x10DE;&#x10DD;&#x10D6;&#x10D8;&#x10E2;&#x10D4;&#x10D1;&#x10D8;</a>
             <a href="/ka/transactions.php?filter=withdraw" class="filter-tab " data-i18n="transactions.withdraw">&#x10D0;&#x10DB;&#x10DD;&#x10E6;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;&#x10D0;</a>
             <a href="/ka/transactions.php?filter=game" class="filter-tab " data-i18n="transactions.game">&#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1;</a>
             <a href="/ka/transactions.php?filter=mining" class="filter-tab " data-i18n="transactions.mining">&#x10E1;&#x10D0;&#x10DB;&#x10D7;&#x10DD;</a>
             <a href="/ka/transactions.php?filter=miningdeposit" class="filter-tab " data-i18n="transactions.mining_income">&#x10E1;&#x10D0;&#x10DB;&#x10D7;&#x10DD; &#x10E8;&#x10D4;&#x10DB;&#x10DD;&#x10E1;&#x10D0;&#x10D5;&#x10DA;&#x10D8;&#x10E1;</a>
             <a href="/ka/transactions.php?filter=referral" class="filter-tab " data-i18n="transactions.referral">&#x10DB;&#x10D8;&#x10DB;&#x10D0;&#x10E0;&#x10D7;&#x10D5;&#x10D0;</a>
        </div>
    </div>
    
    <!-- Date Filter Indicator -->
        
    <!-- Transactions List -->
    <div class="transactions-list">
                                    <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +30.00 USDT
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                19 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 01:06                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +29.00 &#x10D0;&#x10E8;&#x10E8; &#x10D3;&#x10DD;&#x10DA;&#x10D0;&#x10E0;&#x10D8;
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 22:54                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +29.00 &#x10D0;&#x10E8;&#x10E8; &#x10D3;&#x10DD;&#x10DA;&#x10D0;&#x10E0;&#x10D8;
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 22:54                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +40.00 &#x10D0;&#x10E8;&#x10E8; &#x10D3;&#x10DD;&#x10DA;&#x10D0;&#x10E0;&#x10D8;
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 22:53                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +29.00 &#x10D0;&#x10E8;&#x10E8; &#x10D3;&#x10DD;&#x10DA;&#x10D0;&#x10E0;&#x10D8;
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 21:14                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +30.00 USDT
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 21:14                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +20.00 USDT
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 21:14                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount negative">
                                -20.00 &#x10D0;&#x10E8;&#x10E8; &#x10D3;&#x10DD;&#x10DA;&#x10D0;&#x10E0;&#x10D8;
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 21:14                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EE;&#x10D4;&#x10DA;&#x10D0;&#x10EE;&#x10DA;&#x10D0; &#x10D9;&#x10DD;&#x10E0;&#x10D4;&#x10E5;&#x10E2;&#x10D8;&#x10E0;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +20.00 USDT
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                2025 &#x10EC;&#x10DA;&#x10D8;&#x10E1; 18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8;, 21:13                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                            <div class="transaction-item">
                    <div class="transaction-icon 
                        tx-game">
                        <i class="fas 
                        fa-gamepad"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;                            </div>
                            <div class="tx-amount positive">
                                +30.00 USDT
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                18 &#x10DB;&#x10D0;&#x10E0;&#x10E2;&#x10D8; 2025, 21:00                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-completed">
                                    &#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8;                                </span>
                            </div>
                        </div>
                        
                                                <div class="tx-description">
                            &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10E1; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D0;                        </div>
                                            </div>
                </div>
                        
            <!-- Pagination (if multiple pages) -->
                        <div class="pagination-container">
                <div class="pagination">
                                        
                    <span class="pagination-info">
                        1 / 2                    </span>
                    
                                        <a href="?page=2" class="pagination-item">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?page=2" class="pagination-item">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                                    </div>
            </div>
                        </div>
</div>

<!-- Filter Modal -->
<div class="filter-modal" id="filterModal">
    <div class="filter-modal-content">
        <div class="filter-modal-header">
            <h3 data-i18n="transactions.advanced_filters">&#x10DB;&#x10DD;&#x10EC;&#x10D8;&#x10DC;&#x10D0;&#x10D5;&#x10D4; &#x10E4;&#x10D8;&#x10DA;&#x10E2;&#x10E0;&#x10D4;&#x10D1;&#x10D8;</h3>
            <button class="close-btn" id="closeFilterModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="filter-modal-body">
            <form action="/ka/transactions.php" method="GET">
                                
                <div class="form-group">
                    <label for="start_date" data-i18n="transactions.start_date">&#x10D3;&#x10D0;&#x10EC;&#x10E7;&#x10D4;&#x10D1;&#x10D8;&#x10E1; &#x10D7;&#x10D0;&#x10E0;&#x10D8;&#x10E6;&#x10D8;</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value>
                </div>
                
                <div class="form-group">
                    <label for="end_date" data-i18n="transactions.end_date">&#x10D3;&#x10D0;&#x10E1;&#x10E0;&#x10E3;&#x10DA;&#x10D4;&#x10D1;&#x10D8;&#x10E1; &#x10D7;&#x10D0;&#x10E0;&#x10D8;&#x10E6;&#x10D8;</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary" data-i18n="transactions.apply_filters">&#x10EC;&#x10D0;&#x10D8;&#x10E1;&#x10D5;&#x10D8;&#x10D7; &#x10E4;&#x10D8;&#x10DA;&#x10E2;&#x10E0;&#x10D4;&#x10D1;&#x10D8;&#x10E1;</button>
                     <a href="?" class="btn btn-outline-secondary" data-i18n="transactions.clear_filters">&#x10DB;&#x10D9;&#x10D0;&#x10E4;&#x10D8;&#x10DD; &#x10E4;&#x10D8;&#x10DA;&#x10E2;&#x10E0;&#x10D4;&#x10D1;&#x10D8;</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop" id="modalBackdrop"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter modal functionality
    const showFilterBtn = document.getElementById('showFilterBtn');
    const filterModal = document.getElementById('filterModal');
    const closeFilterModal = document.getElementById('closeFilterModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    
    // Show filter modal
    if (showFilterBtn) {
        showFilterBtn.addEventListener('click', function() {
            filterModal.classList.add('active');
            modalBackdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close filter modal
    if (closeFilterModal) {
        closeFilterModal.addEventListener('click', function() {
            filterModal.classList.remove('active');
            modalBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Close modal on backdrop click
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', function() {
            filterModal.classList.remove('active');
            modalBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Make transaction items expandable
    const transactionItems = document.querySelectorAll('.transaction-item');
    transactionItems.forEach(function(item) {
        item.addEventListener('click', function() {
            this.classList.toggle('expanded');
        });
    });
});
</script>

</main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="/ka/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x10E1;&#x10D0;&#x10EE;&#x10DA;&#x10D8;</span>
        </a>
        
        <a href="/ka/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x10DE;&#x10D0;&#x10D9;&#x10D4;&#x10E2;&#x10D4;&#x10D1;&#x10D8;</span>
        </a>
        
                    <a href="/ka/daily-game.php" class="nav-item nav-item-main ">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">&#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;</span>
            </a>
            
            <a href="/ka/notifications.php" class="nav-item ">
                <i class="fas fa-bell"></i>
                                <span data-i18n="buttons.notifications">&#x10E8;&#x10D4;&#x10E2;&#x10E7;&#x10DD;&#x10D1;&#x10D8;&#x10DC;&#x10D4;&#x10D1;&#x10D4;&#x10D1;&#x10D8;</span>
            </a>
            
            <a href="/ka/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">&#x10D0;&#x10DC;&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10E8;&#x10D8;</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>