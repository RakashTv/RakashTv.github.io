<?php
require_once '../modules/Logic.php';
global $logic_instance;
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
        <p>Taryfikator</p>
        <section class="table-section">
            <table id="tableID" class="table">
                <thead>
                <tr class="table-header">
                    <th data-sortas="numeric">#</th>
                    <th data-sortas="case-insensitive">Kategoria</th>
                    <th data-sortas="case-insensitive">Nazwa usługi</th>
                    <th data-sortas="numeric">min</th>
                    <th data-sortas="numeric">max</th>
                </tr>
                </thead>
                <tbody>
                <?php $logic_instance->get_all_prices();?>
                </tbody>
            </table>
            <section id="pagination-section"></section>
        </section>
    </main>
    <?php include '../modules/table-scripts.html' ?>
</body>
</html>