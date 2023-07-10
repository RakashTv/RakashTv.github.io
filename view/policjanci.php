<?php
require_once '../modules/Logic.php';
global $logic_instance;
$query  = "SELECT label FROM job_grades WHERE job_name = 'police' ORDER BY grade";
$stmt   = $logic_instance->conn->prepare($query);
$result = $logic_instance->execute_query($stmt);

$job_grade = [];
while($row = $result->fetch_array()){
    $job_grade[] = $row[0];
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
        <p>Policjanci</p>
        <section class="table-section">
            <table id="tableID" class="table">
                <thead>
                    <tr class="table-header">
                        <th data-sortas="numeric">#</th>
                        <th data-sortas="case-insensitive">Imię i Nazwisko</th>
                        <th data-sortas="case-insensitive">Odznaka</th>
                        <th data-sortas="case-insensitive">Stanowisko</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query  = "SELECT firstname, lastname, badge, job_grade, `identifier` FROM users WHERE job= 'police'";
                    $stmt   = $logic_instance->conn->prepare($query);
                    $result = $logic_instance->execute_query($stmt);

                    if($result->num_rows > 0):
                    $counter = 1;
                    while($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?=$counter?></td>
                            <td><?=$row['firstname'].' '.$row['lastname']?></td>
                            <td><?=$row['badge']?></td>
                            <td><?=$job_grade[$row['job_grade']]?></td>
                            <td><?="<a href='../view/kartoteka.php?id=".$row['identifier']."' class='id-btn'>📝&nbsp;Szczegóły</a>"?></td>
                        </tr>
                    <?php
                        $counter++;
                    endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="5">BRAK</td>
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