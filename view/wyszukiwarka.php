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
        <p>Wyszukiwarka Obywateli</p>
        <section class="table-section">
            <table id="tableID" class="table">
                <thead>
                    <tr class="table-header">
                        <th data-sortas="numeric">#</th>
                        <th data-sortas="case-insensitive">Imię i Nazwisko</th>
                        <th>Data Urodzenia</th>
                        <th data-sortas="case-insensitive">Płeć</th>
                        <th data-sortas="numeric">Wzrost</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $logic_instance->get_all_users()?>
                </tbody>
            </table>
            <section id="pagination-section"></section>
        </section>
    </main>
    <?php include '../modules/table-scripts.html' ?>
</body>
</html>