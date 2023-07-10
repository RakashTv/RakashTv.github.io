<?php
require_once '../modules/Logic.php';
global $logic_instance;

//idiot checking mechanism
if(!isset($_GET['id'])){
    header("Location: ./index.html", true, 301);
}

if(isset($_POST['note'], $_POST['id']) && trim($_POST['note']) != ""){
    $query = "INSERT INTO `lspd_poszukiwani` (`lspd_id`, `steam`, `powod`, `data`) VALUES (NULL, ?, ?, NULL)";
    $stmt = $logic_instance->conn->prepare($query);
    $stmt->bind_param("ss", $_POST['id'], $_POST['note']);
    $logic_instance->execute_query($stmt);
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
    <link rel="stylesheet" href="../assets/css/kartoteka.css">
    <title>Document</title>
</head>
<body>
<?php include '../modules/header.html'?>
<main>
    <p>Dodaj poszukiwanie</p>
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
            <label for="note"><input type="text" name="note" placeholder="Powód poszukiwania..."></label>
            <?="<input type=\"hidden\" name=\"id\" value=\"".$id."\">"?>
            <button type="submit" class="note-submit-btn">Poszukuj</button>
        </form>
    </section>
</main>
</body>
</html>