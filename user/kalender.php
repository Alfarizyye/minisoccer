<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Jadwal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card p-4">
        <h3>Jadwal Lapangan</h3>

        <table class="table table-striped">
            <tr>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
            </tr>

            <?php
            $data = mysqli_query($conn, "SELECT * FROM bookings");
            while($d = mysqli_fetch_array($data)){
            ?>
            <tr>
                <td><?= $d['date'] ?></td>
                <td><?= $d['time'] ?></td>
                <td>
                    <span class="badge bg-<?= $d['status']=='pending'?'warning':'success' ?>">
                        <?= $d['status'] ?>
                    </span>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>