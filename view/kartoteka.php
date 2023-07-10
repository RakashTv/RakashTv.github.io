<?php
require_once '../modules/Logic.php';
global $logic_instance;

if(isset($_POST['note'], $_POST['id']) && trim($_POST['note']) != ""){
    $logic_instance->add_post(trim($_POST['note']), "note", $_POST['id']);
    $_GET['id'] = $_POST['id'];
}

//idiot checking mechanism
if(!isset($_GET['id'])){
    header("Location: ./index.html", true, 301);
}

$id = $_GET['id'];

$query = "SELECT `firstname`, `lastname`, `dateofbirth`, `sex`, `height`, `identifier`, `phone_number`, `avatar`, `time_ubezpieczenie` as insurance, `user_licenses` as licenses FROM `users` WHERE `identifier` = ?";
$stmt = $logic_instance->conn->prepare($query);
$stmt->bind_param("s", $id);
$result = $logic_instance->execute_query($stmt);
if($result->num_rows < 0){
    die("Nie ma takiego obywatela");
}

$citizen = $result->fetch_assoc();
$citizen['sex'] = $citizen['sex'] == 1 ? "Kobieta" : "Mężczyzna";

if($citizen['insurance'] == 0){
    $citizen['insurance'] = "<button type='button' class=\"no-insurance-info\" style='background-color: crimson;'>BRAK</button>";
}elseif($citizen['insurance'] < time()){
    $citizen['insurance'] = "<button type='button' class=\"no-insurance-info\" style='background-color: yellow; color: black'>WYGASŁO</button>";
}else{
    $citizen['insurance'] = "<button type='button' class=\"no-insurance-info\" style='background-color: greenyellow'>AKTYWNE</button>";
}

//in db there is a sort of array in string saved, so we need to get rid of these characters
$citizen['licenses'] = str_replace(['[', ']'], '', $citizen['licenses']);
$citizen['licenses'] = str_replace([',','"'], ' ', $citizen['licenses']);

if($citizen['licenses'] == ""){
    $citizen['licenses'] = "<button class=\"licence-info\">BRAK</button>";
}else{
    /* Ok, here crazy shit happens
     * So, in db and in view values should differ as user don't wanna see internal codes
     * but real descriptions, so we need to replace these codes, with descriptions
     */
    $view = ["<button type='button' class=\"licence-info\">Teoria Prawa Jazdy</button>", "<button type='button' class=\"licence-info\">Kat. C</button>", "<button type='button' class=\"licence-info\">Kat. A</button>", "<button type='button' class=\"licence-info\">Kat. B</button>", "<button type='button' class=\"licence-info\">Broń Długa</button>", "<button type='button' class=\"licence-info\">Broń Krótka</button>", "<button type='button' class=\"licence-info\">Pierwsza Pomoc</button>", "<button type='button' class=\"licence-info\">Poczytalność</button>"];
    $codes  = ["dmv", "drive_truck", "drive_bike", "drive", "weapon_long", "weapon", "firstaid", "psychic"];
    $citizen['licenses'] = str_replace($codes, $view, $citizen['licenses']);
}

$query = "SELECT COUNT(*) AS tickets FROM `billing` WHERE `target_steamid` = ? && `society_name` = 'society_police_money'";
$stmt = $logic_instance->conn->prepare($query);
$stmt->bind_param("s", $id);
$result = $logic_instance->execute_query($stmt);
$ticket_count = $result->fetch_array();

$query = "SELECT COUNT(*) FROM `billing` WHERE `target_steamid` = ? && `society_name` = 'society_police_money' && `payment` = 0 && `accepted` = 1";
$stmt = $logic_instance->conn->prepare($query);
$stmt->bind_param("s", $id);
$result = $logic_instance->execute_query($stmt);
$unpaid_ticket_count = $result->fetch_array();
?>

<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <?php include '../modules/head.html' ?>
    <title>LSPD - TERMINAL</title>
    <link rel="stylesheet" href="../assets/css/kartoteka.css">
</head>
<body>
    <header>
        <a href="../view/wyszukiwarka.php">
            <section>
                <img src="../assets/img/arrow_back_black_24dp.svg" alt="back">
                <p>Powrót</p>
            </section>
        </a>
        <img src="../assets/img/logo.png" alt="logo">
        <p>Wewnętrzny Terminal LSPD</p>
    </header>
    <main>
        <p>Kartoteka</p>
        <section class="basic-info">
            <?="<img src=\"".$citizen['avatar']."\" alt=\"profile\">" ?>
            <section class="no-specifics">
                <h2><?=$citizen['firstname']." ".$citizen['lastname']?></h2>
                <p>
                    Ubezpieczenie:<br>
                    <?=$citizen['insurance']?>
                </p>
                <p>
                    Licencje:<br>
                    <?=$citizen['licenses']?>
                </p>
            </section>
            <section class="specifics">
                <div class="row">
                    <p class="index">Data urodzenia:</p>
                    <p class="data"><?=$citizen['dateofbirth']?></p>
                </div>
                <div class="row">
                    <p class="index">Wzrost:</p>
                    <p class="data"><?=$citizen['height']?></p>
                </div>
                <div class="row">
                    <p class="index">Płeć:</p>
                    <p class="data"><?=$citizen['sex']?></p>
                </div>
                <div class="row">
                    <p class="index">Telefon:</p>
                    <p class="data"><?=$citizen['phone_number']?></p>
                </div>
                <div class="row">
                    <p class="index">Liczba mandatów: </p>
                    <p class="data"><?=$ticket_count[0]?></p>
                </div>
                <div class="row">
                    <p class="index">Nieopłacone mandaty: (liczba) </p>
                    <p class="data"><?=$unpaid_ticket_count[0]?></p>
                </div>
            </section>
        </section>
        <section class="note-form">
            <form method="post">
                <label for="note"><input type="text" name="note" placeholder="Treść notatki..."></label>
                <?="<input type=\"hidden\" name=\"id\" value=\"".$id."\">"?>
                <button type="submit" class="note-submit-btn">Dodaj notatkę</button>
            </form>
        </section>
        <section class="more-info">
            <!--Tab links-->
            <nav class="tabs">
                <button class="tab-links" onclick="openTab(event, 'lspd-notes')" id="default-open">Notatki</button>
                <button class="tab-links" onclick="openTab(event, 'lspd-sentences')">Wyroki</button>
                <button class="tab-links" onclick="openTab(event, 'residences')">Rezydencje</button>
                <button class="tab-links" onclick="openTab(event, 'vehicles')">Pojazdy</button>
            </nav>
            <!--Tab content-->
            <section id="lspd-notes" class="tab-content">
                <ul>
                    <?php
                    $query = "SELECT `note`, `date` FROM `lspd_panel` WHERE `identifier` = ? ORDER BY `date` DESC";
                    $stmt = $logic_instance->conn->prepare($query);
                    $stmt->bind_param("s", $id);
                    $result = $logic_instance->execute_query($stmt);

                    if($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <li>
                        <div class="bullet"></div>
                        <div class="time"><?=$row['date']?></div>
                        <div class="desc">
                            <?=$row['note']?>
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
            <section id="lspd-sentences" class="tab-content">
                <ul>
                    <?php
                    $query = "SELECT `sender`, `reason`, `time`, `type`, `date` FROM user_kartoteka WHERE guilty = ?";
                    $stmt = $logic_instance->conn->prepare($query);
                    $stmt->bind_param("s", $id);
                    $result = $logic_instance->execute_query($stmt);

                    if($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            ?>
                            <li>
                                <div class="bullet"></div>
                                <div class="time"><?=$row['date']?></div>
                                <div class="car-info">
                                    <div class="row">
                                        <p class="index">Wydany przez:</p>
                                        <p class="data"><?=$row['sender']?></p>
                                    </div>
                                    <div class="row">
                                        <p class="index">Powód:</p>
                                        <p class="data"><?=$row['reason']?></p>
                                    </div>
                                    <div class="row">
                                        <p class="index">Czas: (lata) </p>
                                        <p class="data"><?=$row['time']?></p>
                                    </div>
                                    <div class="row">
                                        <p class="index">Miejsce odbycia wyroku: </p>
                                        <p class="data"><?=$row['type']?></p>
                                    </div>
                                </div>
                            </li>
                        <?php
                        endwhile;
                    else: ?>
                        <li>
                            <div class="bullet"></div>
                            <div class="desc">Brak Pojazdów</div>
                        </li>
                    <?php endif?>
                </ul>
            </section>
            <section id="residences" class="tab-content">
                <ul>
                    <?php
                    $id_for_query = "%{$id}%";
                    $query = "SELECT * FROM `residences` WHERE `owner` = ? OR `key1` = ? OR `key2` = ? OR `key3` = ? OR `accesable` LIKE ?";
                    $stmt = $logic_instance->conn->prepare($query);
                    $stmt->bind_param("sssss", $id, $id, $id, $id, $id_for_query);
                    $result = $logic_instance->execute_query($stmt);

                    if($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            if ($id == $row['owner']) {
                                $text = "Właściciel:";
                            }
                            elseif($id == $row['key1'] || $id == $row['key2'] || $id == $row['key3']){
                                $text = "Dostęp do:";
                            }else{
                                $text = "Hotel:";
                            }
                            ?>
                            <li>
                                <div class="bullet"></div>
                                <div class="desc">
                                    <?=$text." ".$row['label']?>
                                </div>
                            </li>
                        <?php
                        endwhile;
                    else: ?>
                        <li>
                            <div class="bullet"></div>
                            <div class="desc">Brak Rezydencji</div>
                        </li>
                    <?php endif?>
                </ul>
            </section>
            <section id="vehicles" class="tab-content">
                <ul>
                    <?php
                    $query = "SELECT `time_przeglad`, `time_ocac`, `label`, `plate` FROM `owned_vehicles` WHERE `owner` = ?";
                    $stmt = $logic_instance->conn->prepare($query);
                    $stmt->bind_param("s", $id);
                    $result = $logic_instance->execute_query($stmt);

                    if($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $row['time_przeglad'] = $row['time_przeglad'] > 1 ? date('d/m/Y H:i:s', $row['time_przeglad']) : "BRAK";
                            $row['time_ocac'] = $row['time_ocac'] > 1 ? date('d/m/Y H:i:s', $row['time_ocac']) : "BRAK";
                            ?>
                            <li>
                                <div class="bullet"></div>
                                <div class="car-info">
                                    <div class="row">
                                        <p class="index">Pojazd:</p>
                                        <p class="data"><?=$row['label']?></p>
                                    </div>
                                    <div class="row">
                                        <p class="index">Rejestracja:</p>
                                        <p class="data"><?=$row['time_ocac']?></p>
                                    </div>
                                    <div class="row">
                                        <p class="index">OC: </p>
                                        <p class="data"><?=$row['time_ocac']?></p>
                                    </div>
                                    <div class="row">
                                        <p class="index">Przegląd</p>
                                        <p class="data"><?=$row['time_przeglad']?></p>
                                    </div>
                                </div>
                            </li>
                        <?php
                        endwhile;
                    else: ?>
                        <li>
                            <div class="bullet"></div>
                            <div class="desc">Brak Pojazdów</div>
                        </li>
                    <?php endif?>
                </ul>
            </section>
        </section>
    </main>
    <script src="../assets/js/tabs.js"></script>
</body>
</html>