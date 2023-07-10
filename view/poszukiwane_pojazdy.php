<?php
require_once '../modules/Logic.php';
global $logic_instance;

if(isset($_GET['usun_poszukiwanie'])){
    $query = "DELETE FROM lspd_poszukiwane_pojazdy WHERE lspd_id = ?";
    $stmt = $logic_instance->conn->prepare($query);
    $stmt->bind_param("i", $_GET['usun_poszukiwanie']);
    $logic_instance->execute_query($stmt);
}
?>

<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <?php include '../modules/head.html' ?>
    <link rel="stylesheet" href="../assets/css/wyszukiwarka_cennik.css">
    <title>Document</title>
</head>
<body>
<?php include '../modules/header.html'?>
<main>
    <p>Poszukiwane pojazdy</p>
    <section class="table-section">
        <table id="tableID" class="table">
            <thead>
            <tr class="table-header">
                <th data-sortas="numeric">#</th>
                <th data-sortas="case-insensitive">Model (nr. rejestracyjny)</th>
                <th data-sortas="case-insensitive">Powód</th>
                <th data-sortas="case-insensitive">Właściciel</th>
                <th>Data</th>
                <th>Akcje</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $query  = "SELECT * FROM `lspd_poszukiwane_pojazdy` INNER JOIN `owned_vehicles` ON `lspd_plate` = `plate` INNER JOIN `users` ON `lspd_owner` = `identifier`";
                $stmt   = $logic_instance->conn->prepare($query);
                $result = $logic_instance->execute_query($stmt);

                if($result->num_rows > 0):
                    $counter = 1;
                    while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?=$counter?></td>
                    <td><?=$row['label']." (".$row['plate'].")"?></td>
                    <td><?=$row['powod']?></td>
                    <td><?=$row['firstname']." ".$row['lastname']?></td>
                    <td><?=$row['date']?></td>
                    <td><?="<a href='../view/kartoteka.php?id=".$row['lspd_owner']."' class='id-btn'>📝&nbsp;Szczegóły</a><a href='../view/poszukiwane_pojazdy.php?usun_poszukiwanie=".$row['lspd_id']."' class='id-btn'>❌</a>"?></td>
                </tr>
                <?php
                    $counter++;
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="6">BRAK</td>
                </tr>
                <?php endif;?>
            </tbody>
        </table>
        <section id="pagination-section"></section>
    </section>
</main>
<?php include '../modules/table-scripts.html' ?>
</body>
</html>