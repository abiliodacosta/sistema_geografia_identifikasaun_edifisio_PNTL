
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="widget-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="mb-1 text-white"><?= __('Jestaun_Ajenda') ?></h3>
                        <span class="badge bg-primary rounded-pill">
                            <?php 
                            $count_res = mysqli_query($conn, "SELECT id_ajenda FROM tb_ajenda");
                            echo $count_res ? mysqli_num_rows($count_res) : 0;
                            ?> Total Ajenda
                        </span>
                    </div>
                    <a href="?pntl=ajenda/add" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-plus me-2"></i> <?= __('Rejistu_Ajenda_Foun') ?>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table text-center align-middle table-bordered table-hover mb-0">
                        <thead class="premium-header">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">📝 <?= __('Titulu_Ajenda') ?></th>
                                <th scope="col">📅 <?= __('Data') ?></th>
                                <th scope="col">⏰ <?= __('Oras') ?></th>
                                <th scope="col">📄 <?= __('Diskrisaun') ?></th>
                                <th scope="col">⚙️ <?= __('Asaun') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nu = 1;
                            $query = mysqli_query($conn, "SELECT * FROM tb_ajenda ORDER BY data_ajenda DESC, ora_ajenda DESC");
                            if ($query && mysqli_num_rows($query) > 0) {
                                while ($data = mysqli_fetch_array($query)) {
                            ?>
                            <tr>
                                <td><span class="fw-bold"><?php echo $nu++; ?></span></td>
                                <td class="text-start"><?php echo htmlspecialchars($data['titulu']); ?></td>
                                <td><span class="badge bg-dark"><?php echo date('d-m-Y', strtotime($data['data_ajenda'])); ?></span></td>
                                <td><span class="text-warning fw-bold"><?php echo date('H:i', strtotime($data['ora_ajenda'])); ?></span></td>
                                <td class="text-start small opacity-75"><?php echo htmlspecialchars($data['diskrisaun']); ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="?pntl=ajenda/edit&id=<?php echo $data['id_ajenda']; ?>" class="btn btn-sm btn-warning action-btn" title="<?= __('Edit') ?>">
                                            <i class="fas fa-pen"></i><span class="btn-label"> <?= __('Edit') ?></span>
                                        </a>
                                        <a href="?pntl=ajenda/delete&id=<?php echo $data['id_ajenda']; ?>" class="btn btn-sm btn-danger action-btn delete-confirm" 
                                           data-message="<?= __('Konfirma_Hamos') ?> <?php echo htmlspecialchars($data['titulu']); ?>?" title="<?= __('Delete') ?>">
                                            <i class="fas fa-trash"></i><span class="btn-label"> <?= __('Delete') ?></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                } 
                            } else {
                                echo '<tr><td colspan="6" class="text-center py-4 text-white-50">Seidauk iha ajenda rejistadu.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
