<?php
session_start();
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Destek taleplerini al
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$priority_filter = isset($_GET['priority']) ? $_GET['priority'] : 'all';
$tickets = getSupportTickets($status_filter, $priority_filter);

// Sayfa başlığı
$page_title = 'Destek Talepleri';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3">Destek Talepleri</h1>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-success" onclick="bulkProcess('close')">
                <i class="fas fa-check-circle me-2"></i> Seçilileri Kapat
            </button>
        </div>
    </div>
    
    <!-- Filtreleme -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Durum</label>
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>Tümü</option>
                        <option value="open" <?= $status_filter == 'open' ? 'selected' : '' ?>>Açık</option>
                        <option value="in_progress" <?= $status_filter == 'in_progress' ? 'selected' : '' ?>>İşlemde</option>
                        <option value="closed" <?= $status_filter == 'closed' ? 'selected' : '' ?>>Kapalı</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Öncelik</label>
                    <select class="form-select" name="priority" onchange="this.form.submit()">
                        <option value="all" <?= $priority_filter == 'all' ? 'selected' : '' ?>>Tümü</option>
                        <option value="low" <?= $priority_filter == 'low' ? 'selected' : '' ?>>Düşük</option>
                        <option value="medium" <?= $priority_filter == 'medium' ? 'selected' : '' ?>>Orta</option>
                        <option value="high" <?= $priority_filter == 'high' ? 'selected' : '' ?>>Yüksek</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ara</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Konu veya kullanıcı ara..." value="<?= $_GET['search'] ?? '' ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Destek Talepleri Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Kullanıcı</th>
                            <th>Konu</th>
                            <th>Durum</th>
                            <th>Öncelik</th>
                            <th>Son Güncelleme</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tickets as $ticket): ?>
                        <tr class="<?= $ticket['status'] == 'open' ? 'table-info' : '' ?> <?= $ticket['priority'] == 'high' ? 'table-danger' : '' ?>">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input ticket-check" type="checkbox" value="<?= $ticket['id'] ?>">
                                </div>
                            </td>
                            <td>#<?= $ticket['id'] ?></td>
                            <td><?= htmlspecialchars($ticket['username']) ?></td>
                            <td class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($ticket['subject']) ?></td>
                            <td>
                                <?php if($ticket['status'] == 'open'): ?>
                                    <span class="badge bg-info">Açık</span>
                                <?php elseif($ticket['status'] == 'in_progress'): ?>
                                    <span class="badge bg-warning">İşlemde</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Kapalı</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($ticket['priority'] == 'low'): ?>
                                    <span class="badge bg-secondary">Düşük</span>
                                <?php elseif($ticket['priority'] == 'medium'): ?>
                                    <span class="badge bg-primary">Orta</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Yüksek</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('d.m.Y H:i', strtotime($ticket['last_updated'] ?? $ticket['created_at'])) ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="view-ticket.php?id=<?= $ticket['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($ticket['status'] != 'closed'): ?>
                                    <button type="button" class="btn btn-sm btn-success" onclick="closeTicket(<?= $ticket['id'] ?>)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteTicket(<?= $ticket['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Destek İstatistikleri -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Destek Talebi Dağılımı</h5>
                </div>
                <div class="card-body">
                    <canvas id="ticketDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Yanıt Süreleri</h5>
                </div>
                <div class="card-body">
                    <canvas id="responseTimeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kapatma Modal -->
<div class="modal fade" id="closeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title">Talebi Kapat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu destek talebini kapatmak istediğinize emin misiniz?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-success" id="confirmCloseBtn">Kapat</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Toplu işlem seçimi
document.getElementById('selectAll').addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.ticket-check').forEach(checkbox => {
        checkbox.checked = isChecked;
    });
});

// Toplu işlem fonksiyonu
function bulkProcess(action) {
    const selectedTickets = [];
    document.querySelectorAll('.ticket-check:checked').forEach(checkbox => {
        selectedTickets.push(checkbox.value);
    });
    
    if (selectedTickets.length === 0) {
        alert('Lütfen en az bir destek talebi seçin.');
        return;
    }
    
    if (action === 'close') {
        if (confirm('Seçili destek taleplerini kapatmak istediğinize emin misiniz?')) {
            processSelectedTickets(selectedTickets, 'close');
        }
    }
}

function processSelectedTickets(ticketIds, action) {
    fetch('../ajax/bulk_ticket_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'tickets=' + JSON.stringify(ticketIds) + '&action=' + action
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    });
}

// Tek talep kapatma
function closeTicket(ticketId) {
    const closeModal = new bootstrap.Modal(document.getElementById('closeModal'));
    document.getElementById('confirmCloseBtn').onclick = function() {
        fetch('../ajax/close_ticket.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'ticket_id=' + ticketId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                closeModal.hide();
                window.location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        });
    };
    
    closeModal.show();
}

// Destek talebi silme
function deleteTicket(ticketId) {
    if (confirm('Bu destek talebini silmek istediğinize emin misiniz? Bu işlem geri alınamaz!')) {
        fetch('../ajax/delete_ticket.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'ticket_id=' + ticketId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        });
    }
}

// Grafikler
document.addEventListener('DOMContentLoaded', function() {
    // Destek Talebi Dağılımı Grafiği
    fetch('../ajax/support_stats.php?type=distribution')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('ticketDistributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Açık', 'İşlemde', 'Kapalı'],
                datasets: [{
                    data: [data.open, data.in_progress, data.closed],
                    backgroundColor: [
                        'rgba(0, 207, 232, 0.7)',
                        'rgba(255, 159, 67, 0.7)',
                        'rgba(40, 199, 111, 0.7)'
                    ],
                    borderColor: [
                        '#00cfe8',
                        '#ff9f43',
                        '#28c76f'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#d0d2d6'
                        }
                    }
                }
            }
        });
    });
    
    // Yanıt Süresi Grafiği
    fetch('../ajax/support_stats.php?type=response_time')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('responseTimeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.dates,
                datasets: [{
                    label: 'Ortalama Yanıt Süresi (Saat)',
                    data: data.response_times,
                    backgroundColor: 'rgba(115, 103, 240, 0.7)',
                    borderColor: '#7367f0',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#d0d2d6'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#d0d2d6'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#d0d2d6'
                        }
                    }
                }
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>