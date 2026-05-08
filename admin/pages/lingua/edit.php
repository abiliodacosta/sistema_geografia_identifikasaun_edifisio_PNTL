<?php
$lang = isset($_GET['lang']) ? $_GET['lang'] : '';
if (!in_array($lang, ['tet', 'pt', 'en'])) {
    echo "<script>window.location='?pntl=lingua/view';</script>";
    exit;
}

$file_path = "../lang/{$lang}.json";
if (!file_exists($file_path)) {
    $_SESSION['error'] = "Faile lingua la eziste!";
    echo "<script>window.location='?pntl=lingua/view';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['json_data'])) {
    $json_data = $_POST['json_data'];
    // Validate JSON
    $decoded = json_decode($json_data, true);
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        $_SESSION['error'] = "Formatu JSON la loos! Favór hadi'a erru iha kódigu laran.";
    } else {
        if (file_put_contents($file_path, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $_SESSION['success'] = "Dadus tradusaun atualiza ho susesu!";
            echo "<script>window.location='?pntl=lingua/view';</script>";
            exit;
        } else {
            $_SESSION['error'] = "Faila atu salva faile lingua!";
        }
    }
}

$current_json = file_get_contents($file_path);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0">Edit Tradusaun: <span class="text-uppercase text-primary"><?= htmlspecialchars($lang) ?></span></h6>
                    <a href="?pntl=lingua/view" class="btn btn-sm btn-outline-light"><i class="fa fa-arrow-left"></i> Fila</a>
                </div>
                
                <div class="alert alert-info border-0 rounded-3" style="background: rgba(25, 135, 84, 0.1); color: #75b798;">
                    <i class="fa fa-info-circle me-2"></i> <strong>Atensaun:</strong> Favór edit de'it testu iha parte loos (<em>value</em>). Keta muda liafuan xave iha parte karuk (<em>key</em>) no keta halakon aspas dupulu (<code>"</code>).
                </div>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="json_data" class="form-label">API Kódigu (JSON Formatu)</label>
                        <textarea class="form-control bg-dark text-white" id="json_data" name="json_data" rows="15" style="font-family: monospace; font-size: 14px; letter-spacing: 0.5px;"><?= htmlspecialchars($current_json) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i> Salva Mudansa</button>
                </form>
            </div>
        </div>
    </div>
</div>
