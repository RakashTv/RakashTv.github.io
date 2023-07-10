<?php
require_once '../modules/Logic.php';
global $logic_instance;

if(isset($_GET['usun_poszukiwanego'])){
    $query = "DELETE FROM lspd_poszukiwani WHERE lspd_id = ?";
    $stmt = $logic_instance->conn->prepare($query);
    $stmt->bind_param("i", $_GET['usun_poszukiwanego']);
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
    <p>Poszukiwani Obywatele</p>
    <section class="table-section">
        <table id="tableID" class="table">
            <thead>
            <tr class="table-header">
                <th data-sortas="numeric">#</th>
                <th data-sortas="case-insensitive">Imię i Nazwisko</th>
                <th data-sortas="case-insensitive">Powód</th>
                <th>Akcje</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $query  = "SELECT lspd_poszukiwani.lspd_id, users.firstname, users.lastname, lspd_poszukiwani.powod, lspd_poszukiwani.steam FROM lspd_poszukiwani INNER JOIN users ON lspd_poszukiwani.steam = users.identifier";
            $stmt   = $logic_instance->conn->prepare($query);
		    $result = $logic_instance->execute_query($stmt);

            if($result->num_rows > 0):
                $counter = 1;
                while($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?=$counter?></td>
                    <td><?=$row['firstname']." ".$row['lastname']?></td>
                    <td><?=$row['powod']?></td>
                    <td><?="<a href='../view/kartoteka.php?id=".$row['steam']."' class='id-btn'>📝&nbsp;Szczegóły</a><a href='../view/poszukiwani.php?usun_poszukiwanego=".$row['lspd_id']."' class='id-btn'>❌</a>"?></td>
                </tr>
            <?php
                $counter++;
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="4">BRAK</td>
                </tr>
            </tbody>
            <?php endif;?>
        </table>
        <section id="pagination-section"></section>
    </section>
</main>
<?php include '../modules/table-scripts.html' ?>
</body>
</html>