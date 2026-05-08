
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="widget-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1 text-white">Jestaun Sistema Lingua (API)</h4>
                        <span class="badge bg-primary rounded-pill">
                            <i class="fa fa-language me-1"></i> Multilingual Support
                        </span>
                    </div>
                    <a href="?pntl=lingua/reset" class="btn btn-danger rounded-pill px-4 delete-confirm" 
                       data-message="Ita hakarak RESET dadus lingua hotu ba default? Ida ne'e sei halakon mudansa hotu ne'ebé ita halo ona.">
                        <i class="fa fa-undo me-2"></i> Restart / Reset
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table text-center align-middle table-bordered table-hover mb-0">
                        <thead class="premium-header">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">🚩 Bandeira</th>
                                <th scope="col">🗣️ Lian / Lingua</th>
                                <th scope="col">📁 Kódigu (JSON)</th>
                                <th scope="col">⚙️ Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- TETUN -->
                            <tr>
                                <td><span class="fw-bold">1</span></td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <img src="../img/flags/tet.png" alt="TL" class="rounded shadow-sm border border-secondary" style="height: 32px; width: 50px; object-fit: cover;">
                                    </div>
                                </td>
                                <td class="fw-bold text-white text-start ps-4">Lian Tetun</td>
                                <td><code class="bg-dark text-warning px-2 py-1 rounded">tet.json</code></td>
                                <td>
                                    <a class="btn btn-sm btn-warning action-btn px-3" href="?pntl=lingua/edit&lang=tet">
                                        <i class="fa fa-edit me-1"></i><span class="btn-label"> <?= __('Edit') ?></span>
                                    </a>
                                </td>
                            </tr>
                            <!-- PORTUGUES -->
                            <tr>
                                <td><span class="fw-bold">2</span></td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <img src="../img/flags/pt.png" alt="PT" class="rounded shadow-sm border border-secondary" style="height: 32px; width: 50px; object-fit: cover;">
                                    </div>
                                </td>
                                <td class="fw-bold text-white text-start ps-4">Lingua Portuguesa</td>
                                <td><code class="bg-dark text-warning px-2 py-1 rounded">pt.json</code></td>
                                <td>
                                    <a class="btn btn-sm btn-warning action-btn px-3" href="?pntl=lingua/edit&lang=pt">
                                        <i class="fa fa-edit me-1"></i><span class="btn-label"> <?= __('Edit') ?></span>
                                    </a>
                                </td>
                            </tr>
                            <!-- ENGLISH -->
                            <tr>
                                <td><span class="fw-bold">3</span></td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <img src="../img/flags/en.png" alt="EN" class="rounded shadow-sm border border-secondary" style="height: 32px; width: 50px; object-fit: cover;">
                                    </div>
                                </td>
                                <td class="fw-bold text-white text-start ps-4">Lian English</td>
                                <td><code class="bg-dark text-warning px-2 py-1 rounded">en.json</code></td>
                                <td>
                                    <a class="btn btn-sm btn-warning action-btn px-3" href="?pntl=lingua/edit&lang=en">
                                        <i class="fa fa-edit me-1"></i><span class="btn-label"> <?= __('Edit') ?></span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
