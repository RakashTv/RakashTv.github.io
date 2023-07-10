<?php
require_once '../modules/Logic.php';
global $logic_instance;

if(isset($_POST['announcement']) && trim($_POST['announcement']) != ""){
    $logic_instance->add_post(trim($_POST['announcement']), "announcement");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../modules/head.html'?>
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/ogloszenia.css">
</head>
<body>
    <?php include '../modules/header.html'?>
    <main>
        <p>Ogłoszenia</p>
        <section class="announcement-form">
            <form method="post">
                <label for="announcement"><input type="text" name="announcement" placeholder="Treść ogłoszenia..."></label>
                <button type="submit" class="note-submit-btn">Dodaj Ogłoszenie</button>
            </form>
        </section>
        <section class="announcements">
            <ul>
                <?php
                $query = "SELECT `info`, `date` FROM `lspd_ogloszenia` ORDER BY `date` DESC";
                $stmt = $logic_instance->conn->prepare($query);
                $result = $logic_instance->execute_query($stmt);

                if($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                ?>
                <li>
                    <div class="bullet"></div>
                    <div class="time"><?=$row['date']?></div>
                    <div class="desc">
                        <?=$row['info']?>
                    </div>
                </li>
                <?php
                endwhile;
                else: ?>
                    <li>
                        <div class="bullet"></div>
                        <div class="desc">Brak</div>
                    </li>
                <?php endif?>
            </ul> 
        </section>
    </main>
</body>
</html>