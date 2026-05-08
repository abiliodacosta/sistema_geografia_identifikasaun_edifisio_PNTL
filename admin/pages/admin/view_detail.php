<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM tb_admin WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4 text-white"><i class="fas fa-eye me-2 text-primary"></i>Detalla Admin</h4>
                <div class="mb-3 text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa fa-user text-white fa-3x"></i>
                    </div>
                    <h5 class="text-white"><?php echo htmlspecialchars($data['naran_konpletu']); ?></h5>
                </div>
                <table class="table table-borderless text-white-50">
                    <tr>
                        <td width="30%">ID Admin</td>
                        <td width="5%">:</td>
                        <td><?php echo $data['id']; ?></td>
                    </tr>
                    <tr>
                        <td>Naran Kompletu</td>
                        <td>:</td>
                        <td><?php echo htmlspecialchars($data['naran_konpletu']); ?></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td>:</td>
                        <td><?php echo htmlspecialchars($data['username']); ?></td>
                    </tr>
                    <tr>
                        <td>Password (Hash)</td>
                        <td>:</td>
                        <td style="word-break: break-all;"><small><?php echo $data['password']; ?></small></td>
                    </tr>
                </table>
                <div class="mt-4">
                    <a href="?pntl=admin/edit&id=<?php echo $id; ?>" class="btn btn-warning"><i class="fas fa-edit me-2"></i>Edit</a>
                    <a href="?pntl=admin/view" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Fila</a>
                </div>
            </div>
        </div>
    </div>
</div>
