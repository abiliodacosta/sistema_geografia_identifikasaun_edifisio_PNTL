<?php
// DB Connection and Auth are already included by home.php

// Load PHPMailer
$phpmailer_path = __DIR__ . '/../../lib/PHPMailer-master/src/';
require_once $phpmailer_path . 'Exception.php';
require_once $phpmailer_path . 'PHPMailer.php';
require_once $phpmailer_path . 'SMTP.php';
require_once __DIR__ . '/../../mail_config.php';

// Mark as read
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    mysqli_query($conn, "UPDATE tb_mensajen SET status = 'Read' WHERE id_mensajen = $id");
}

// Delete message
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM tb_mensajen WHERE id_mensajen = $id");
    $_SESSION['success'] = "Mensajen hamosu ho susesu!";
    echo "<script>window.location.href='?pntl=mensajen/view';</script>";
    exit();
}

// ===== SEND REPLY via PHPMailer (no new tab, no local mail server needed) =====
    // AJAX Unread Count
    if (isset($_GET['ajax']) && $_GET['ajax'] === 'unread') {
        if (ob_get_length()) ob_end_clean(); // Prevent HTML leakage from home.php
        header('Content-Type: application/json');
        $unread_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM tb_mensajen WHERE status='Unread'");
        $unread_count = $unread_q ? mysqli_fetch_assoc($unread_q)['count'] : 0;
        echo json_encode(['count' => (int)$unread_count]);
        exit();
    }

    $reply_success = false;
    $reply_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_to_email'])) {
    $to       = filter_var(trim($_POST['reply_to_email']), FILTER_VALIDATE_EMAIL);
    $subject  = 'Re: ' . trim($_POST['reply_subject']);
    $bodyText = trim($_POST['reply_body']);

    if ($to && !empty($bodyText)) {
        $html_body = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;background:#f5f5f5;border-radius:12px;overflow:hidden;'>
                <div style='background:linear-gradient(135deg,#002366,#0044aa);padding:24px 32px;'>
                    <h2 style='color:#FFCC00;margin:0;font-size:1.3rem;'>PNTL — Resposta Ofisial</h2>
                </div>
                <div style='padding:28px 32px;background:#ffffff;'>
                    <p style='color:#222;font-size:15px;line-height:1.8;white-space:pre-wrap;'>" . htmlspecialchars($bodyText) . "</p>
                </div>
                <div style='background:#002366;padding:14px 32px;text-align:center;'>
                    <small style='color:rgba(255,255,255,0.55);font-size:12px;'>Polícia Nasionál Timor-Leste — Sistema Informasaun Edifísiu</small>
                </div>
            </div>";

        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = MAIL_SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_FROM_EMAIL;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_SMTP_SECURE;
            $mail->Port       = MAIL_SMTP_PORT;
            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html_body;
            $mail->AltBody = strip_tags($bodyText);
            $mail->send();

            $reply_success = true;
            $mid = (int)$_POST['msg_id'];
            mysqli_query($conn, "UPDATE tb_mensajen SET status = 'Read' WHERE id_mensajen = $mid");
        } catch (\Exception $e) {
            $err_msg = isset($mail) ? $mail->ErrorInfo : $e->getMessage();
            $reply_error = "Falha atu haruka email: " . htmlspecialchars($err_msg);
            
            // Proactive advice for Timeout errors (Common in Timor-Leste)
            if (strpos($err_msg, '10060') !== false || strpos($err_msg, 'Timed out') !== false) {
                $reply_error .= "<br><br><small><b>Dika:</b> Erro 10060 (Timeout) baibain tanba ISP blokia portu 587 ka 465. Favor tenta troka Portu iha <a href='?pntl=settings/smtp' class='text-white border-bottom'>Configurasaun SMTP</a>.</small>";
            }
        }
    } else {
        $reply_error = "Preenche kampu hotu antes haruka.";
    }
}

$query  = "SELECT * FROM tb_mensajen ORDER BY data_manda DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container-fluid pt-4 px-4">

    <?php if ($reply_success): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i> <strong>Susesu!</strong> Resposta haruka ona ba email sira ne'e.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($reply_error): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> <?= $reply_error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <style>
        /* Fix for modals being squashed inside table cells */
        .msg-modal-fixed {
            background: rgba(0, 0, 0, 0.4) !important;
            backdrop-filter: blur(8px) !important;
        }
        .msg-modal-fixed .modal-dialog {
            max-width: 800px;
            margin: 1.75rem auto;
            z-index: 1100;
        }
        .msg-modal-fixed .modal-content {
            border-radius: 24px !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            overflow: hidden;
        }
        /* Ensure table doesn't constrain modals */
        .table-responsive {
            overflow: visible !important;
        }
        .widget-card {
            background: rgba(13, 33, 55, 0.95);
            padding: 30px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.05);
        }
    </style>

    <div class="widget-card">
        <!-- Header with Search -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-white me-3">
                    <i class="fa fa-envelope me-2" style="color:var(--pntl-gold,#FFCC00);"></i>
                    Mensajen husi Vizitante
                </h5>
                <span class="badge bg-danger rounded-pill unread-badge" id="unreadCount">
                    <?php
                    $unread_res = mysqli_query($conn, "SELECT id_mensajen FROM tb_mensajen WHERE status='Unread'");
                    $unread = $unread_res ? mysqli_num_rows($unread_res) : 0;
                    echo $unread . ' La Lee';
                    ?>
                </span>
            </div>
            <!-- Search & Filter -->
            <div class="d-flex gap-2 w-100 w-md-auto">
                <div class="input-group input-group-sm flex-grow-1" style="max-width:400px;">
                    <span class="input-group-text bg-transparent border-0 text-white-50">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" class="form-control bg-dark border-0 text-white fw-semibold" id="searchMensajen"
                        placeholder="Buka naran, email, asuntu..." style="border-radius:0 12px 12px 0 !important;">
                </div>
                <select class="form-select form-select-sm bg-dark border-0 text-white fw-semibold" id="filterStatus" style="max-width:140px;border-radius:12px;">
                    <option value="">Hotu Status</option>
                    <option value="Unread">La Lee</option>
                    <option value="Read">Lee Ona</option>
                </select>
                <button class="btn btn-sm btn-outline-light fw-bold" onclick="refreshUnreadCount()" style="border-radius:12px;padding:6px 16px;">
                    <i class="fa fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- Results Info -->
        <div class="mb-3 d-flex justify-content-between flex-wrap gap-2 small text-white-50">
            <span id="resultsInfo">Haree <strong id="totalResults"><?= $result ? mysqli_num_rows($result) : 0 ?></strong> mensajen</span>
            <button class="btn btn-sm btn-outline-warning fw-bold export-csv" onclick="exportCSV()" style="border-radius:8px;font-size:0.8rem;">
                <i class="fa fa-download me-1"></i> Export CSV
            </button>
        </div>

        <div class="table-responsive" id="mensajenTableContainer">
            <table class="table text-center align-middle table-bordered table-hover mb-0" id="mensajenTable">
                <thead>
                    <tr>
                        <th style="cursor:pointer;" onclick="sortTable(0)">📅 Data <i class="fa fa-sort ms-1 small opacity-75"></i></th>
                        <th style="cursor:pointer;" onclick="sortTable(1)">👤 Naran <i class="fa fa-sort ms-1 small opacity-75"></i></th>
                        <th style="cursor:pointer;" onclick="sortTable(2)">📧 Email <i class="fa fa-sort ms-1 small opacity-75"></i></th>
                        <th style="cursor:pointer;" onclick="sortTable(3)">📝 Asuntu <i class="fa fa-sort ms-1 small opacity-75"></i></th>
                        <th style="cursor:pointer;" onclick="sortTable(4)">🏷️ Status <i class="fa fa-sort ms-1 small opacity-75"></i></th>
                        <th class="text-center">⚙️ Asaun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="<?= $row['status'] == 'Unread' ? 'fw-bold' : '' ?>">
                            <td><?= date('d/m/Y H:i', strtotime($row['data_manda'])) ?></td>
                            <td><?= htmlspecialchars($row['naran']) ?></td>
                            <td><small><?= htmlspecialchars($row['email']) ?></small></td>
                            <td><?= htmlspecialchars($row['asuntu']) ?></td>
                            <td>
                                <span class="badge rounded-pill <?= $row['status'] == 'Unread' ? 'bg-danger' : 'bg-success' ?>">
                                    <?= $row['status'] == 'Unread' ? 'La Lee' : 'Lee Ona' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- View + Reply Button (Styled as Edit) -->
                                    <button type="button" class="btn btn-sm btn-warning action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#msgModal<?= $row['id_mensajen'] ?>"
                                        onclick="window.history.pushState({}, '', '?pntl=mensajen/view&read=<?= $row['id_mensajen'] ?>');">
                                        <i class="fa fa-pen"></i>
                                        <span class="btn-label"> Haree</span>
                                    </button>
                                    <!-- Delete -->
                                    <a class="btn btn-sm btn-danger action-btn delete-confirm"
                                        href="?pntl=mensajen/view&delete=<?= $row['id_mensajen'] ?>">
                                        <i class="fa fa-trash"></i>
                                        <span class="btn-label"> Hamos</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Render Modals Outside Table to avoid clipping -->
<?php 
if ($result) {
    mysqli_data_seek($result, 0); // Reset pointer
    while($row = mysqli_fetch_assoc($result)): 
?>
    <div class="modal fade msg-modal-fixed" id="msgModal<?= $row['id_mensajen'] ?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1060 !important;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 glass-card" style="background:rgba(10,25,49,0.95);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.1);border-radius:24px;overflow:hidden;box-shadow:0 25px 50px rgba(0,0,0,0.5);">

                <!-- Header -->
                <div class="modal-header border-0 px-4 pt-4 pb-2" style="background:linear-gradient(135deg,#002366,#0044aa);">
                    <div>
                        <h5 class="modal-title text-white fw-bold">
                            <i class="fa fa-envelope me-2" style="color:#FFCC00;"></i>
                            Mensajen husi <?= htmlspecialchars($row['naran']) ?>
                        </h5>
                        <small class="text-white-50"><?= date('d/m/Y H:i', strtotime($row['data_manda'])) ?></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body text-start px-4 py-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="text-white-50 small mb-1 fw-semibold">📧 Email:</label>
                            <div class="text-white fw-semibold fs-6"><?= htmlspecialchars($row['email']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-white-50 small mb-1 fw-semibold">📝 Asuntu:</label>
                            <div class="text-white fw-semibold fs-6"><?= htmlspecialchars($row['asuntu']) ?></div>
                        </div>
                    </div>
                    <hr class="border-secondary mb-4">
                    <label class="text-white-50 small mb-3 fw-semibold">💬 Mensajen:</label>
                    <div class="text-white lh-lg p-4 rounded-3 glass-card-message" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.08);min-height:120px;">
                        <?= nl2br(htmlspecialchars($row['mensajen'])) ?>
                    </div>

                    <!-- Reply Section -->
                    <hr class="border-secondary my-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 p-2 shadow-sm"
                            style="width:44px;height:44px;background:linear-gradient(135deg,#FFCC00,#ff9800);">
                            <i class="fa fa-reply text-dark fs-6"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-0 fw-bold">Resposta Rapida</h6>
                            <small class="text-white-50">Haruka email resposta diretamente</small>
                        </div>
                    </div>
                    <form method="POST" action="?pntl=mensajen/view" id="replyForm<?= $row['id_mensajen'] ?>">
                        <input type="hidden" name="reply_to_email" value="<?= htmlspecialchars($row['email']) ?>">
                        <input type="hidden" name="msg_id" value="<?= $row['id_mensajen'] ?>">
                        <div class="mb-3">
                            <label class="text-white-50 small mb-1 fw-semibold">📨 Destinatáriu:</label>
                            <input type="email" class="form-control glass-input" readonly
                                value="<?= htmlspecialchars($row['email']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="text-white-50 small mb-1 fw-semibold">📋 Asuntu:</label>
                            <input type="text" name="reply_subject" class="form-control glass-input" required
                                value="Re: <?= htmlspecialchars($row['asuntu']) ?>"
                                placeholder="Re: [Asuntu original]">
                        </div>
                        <div class="mb-4">
                            <label class="text-white-50 small mb-1 fw-semibold">✍️ Mensajen Resposta:</label>
                            <textarea name="reply_body" rows="5" class="form-control glass-input" required
                                placeholder="Hakerek resposta ida ne'e... Obrigadu ba kontaktu ami..."
                                style="resize:vertical; font-family: inherit;"></textarea>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-light btn-sm px-4 py-2" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i> Feza
                            </button>
                            <button type="submit" class="btn btn-warning btn-sm fw-bold px-4 py-2 shadow-lg">
                                <i class="fa fa-paper-plane me-2"></i> Haruka Resposta
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
<?php 
    endwhile; 
} 
?>

<!-- Enhanced Mensajen JS: Search, Filter, Sort, Export -->
<script>
    (function() {
        'use strict';

        // Elements
        const searchInput = document.getElementById('searchMensajen');
        const filterStatus = document.getElementById('filterStatus');
        const table = document.getElementById('mensajenTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const unreadBadge = document.getElementById('unreadCount');
        const resultsInfo = document.getElementById('resultsInfo');
        const totalResults = document.getElementById('totalResults');

        let sortDir = {},
            currentSortCol = -1;

        // 1. Live Search & Filter
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const statusFilter = filterStatus.value;

            let visibleCount = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let matchesSearch = !searchTerm;
                let matchesStatus = !statusFilter;

                // Search in name, email, asuntu (cols 1,2,3)
                if (searchTerm) {
                    const searchText = Array.from(cells).slice(1, 4)
                        .map(cell => cell.textContent.toLowerCase())
                        .join(' ');
                    matchesSearch = searchText.includes(searchTerm);
                }

                // Status filter (col 4)
                if (statusFilter) {
                    const statusCell = cells[4]?.querySelector('.badge')?.textContent.trim() || '';
                    matchesStatus = statusCell.includes(statusFilter === 'Unread' ? 'La Lee' : 'Lee Ona');
                }

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            totalResults.textContent = visibleCount;
            resultsInfo.textContent = `Haree ${visibleCount} mensajen ${visibleCount !== rows.length ? '(filtradu)' : ''}`;
        }

        // 2. Sort Table
        function sortTable(colIndex) {
            if (currentSortCol === colIndex) {
                sortDir[colIndex] = !sortDir[colIndex]; // Toggle direction
            } else {
                Object.keys(sortDir).forEach(key => sortDir[key] = false);
                sortDir[colIndex] = true; // Ascending first
                currentSortCol = colIndex;
            }

            const direction = sortDir[colIndex] ? 1 : -1;

            rows.sort((a, b) => {
                let aVal = a.cells[colIndex].textContent.trim();
                let bVal = b.cells[colIndex].textContent.trim();

                // Date col (0): parse DD/MM/YYYY HH:mm
                if (colIndex === 0) {
                    const parseDate = (s) => {
                        const [d, t] = s.split(' ');
                        const [day, month, year] = d.split('/');
                        return new Date(`${year}-${month}-${day}T${t || '00:00'}`);
                    };
                    aVal = parseDate(aVal);
                    bVal = parseDate(bVal);
                }

                // Status col (4): Unread first
                if (colIndex === 4) {
                    aVal = aVal.includes('La Lee') ? 0 : 1;
                    bVal = bVal.includes('La Lee') ? 0 : 1;
                }

                return (aVal > bVal ? 1 : -1) * direction;
            });

            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));

            // Update sort icons
            document.querySelectorAll('th i.fa-sort').forEach((icon, idx) => {
                icon.className = idx === colIndex ?
                    `fa fa-sort-${sortDir[idx] ? 'up' : 'down'} ms-1 small` :
                    'fa fa-sort ms-1 small opacity-75';
            });

            filterTable(); // Re-filter after sort
        }

        // 3. Refresh Unread Count (AJAX)
        function refreshUnreadCount() {
            fetch('?pntl=mensajen/view&ajax=unread')
                .then(res => res.json())
                .then(data => {
                    unreadBadge.textContent = `${data.count} ${data.count === 1 ? 'La Lee' : 'La Lee'}`;
                    unreadBadge.className = `badge ${data.count > 0 ? 'bg-danger' : 'bg-success'} rounded-pill`;
                })
                .catch(() => location.reload());
        }

        // 4. Export CSV
        function exportCSV() {
            let csv = 'Data,Naran,Email,Asuntu,Status\n';
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    const rowData = Array.from(cells).slice(0, 5).map(cell =>
                        '"' + cell.textContent.trim().replace(/"/g, '""') + '"'
                    ).join(',');
                    csv += rowData + '\n';
                }
            });

            const blob = new Blob([csv], {
                type: 'text/csv'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `mensajen_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            URL.revokeObjectURL(url);
        }

        // Event Listeners
        if (searchInput) {
            ['keyup', 'search', 'input'].forEach(event => {
                searchInput.addEventListener(event, filterTable);
            });
        }

        if (filterStatus) {
            filterStatus.addEventListener('change', filterTable);
        }

        // Initial filter
        filterTable();

        // Auto-refresh unread every 30s
        setInterval(refreshUnreadCount, 30000);

    })();
</script>