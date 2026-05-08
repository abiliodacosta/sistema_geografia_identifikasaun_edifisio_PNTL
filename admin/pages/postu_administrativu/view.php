
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <h3 class="mb-4"><?= __('Tabela_Postu_Administrativu') ?></h3>

                <a href="?pntl=postu_administrativu/add">
                    <button class="btn btn-primary mb-3">
                        <i class="fas fa-plus"></i> <?= __('Rejistu_Postu_Foun') ?>
                    </button>
                </a>
                
                <div class="table-responsive">
                    <table class="table text-center table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('No') ?></th>
                                <th scope="col"><?= __('Munisipiu') ?></th>
                                <th scope="col"><?= __('Naran_Postu') ?></th>
                                <th scope="col"><?= __('Asaun') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nu = 1;
                            $query = mysqli_query($conn, "SELECT p.*, m.naran_munisipio FROM tb_postu_administrativu p JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio ORDER BY m.naran_munisipio ASC, p.naran_posto ASC");
                            while ($data = mysqli_fetch_array($query)) {
                            ?>
                            <tr>
                                <td><?php echo $nu++; ?></td>
                                <td><?php echo htmlspecialchars($data['naran_munisipio']); ?></td>
                                <td><?php echo htmlspecialchars($data['naran_posto']); ?></td>
                                <td>
                                    <a href="?pntl=postu_administrativu/edit&id=<?php echo $data['id_posto']; ?>" class="btn btn-sm btn-warning action-btn">
                                        <i class="fas fa-pen"></i><span class="btn-label"> <?= __('Edit') ?></span>
                                    </a>
                                    <a href="?pntl=postu_administrativu/delete&id=<?php echo $data['id_posto']; ?>" class="btn btn-sm btn-danger action-btn delete-confirm" data-message="<?= __('Konfirma_Hamos') ?> <?php echo htmlspecialchars($data['naran_posto']); ?>?">
                                         <i class="fas fa-trash"></i><span class="btn-label"> <?= __('Hamos') ?></span>
                                     </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
