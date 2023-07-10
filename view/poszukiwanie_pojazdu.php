<?php
require_once '../modules/Logic.php';
global $logic_instance;

//idiot checking mechanism
if(!isset($_GET['id'])){
    header("Location: ./index.html", true, 301);
}

if(isset($_POST['note'], $_POST['user-id'], $_POST['plate']) && trim($_POST['note']) != ""){
    $query = "INSERT INTO `lspd_poszukiwane_pojazdy` (`lspd_plate`, `lspd_owner`, `powod`, `date`) VALUES (?, ?, ?, current_timestamp());";
    $stmt = $logic_instance->conn->prepare($query);
    $stmt->bind_param("sss", $_POST['plate'], $_POST['user-id'], $_POST['note']);
    $logic_instance->execute_query($stmt);
    header("Location: ./index.html", true, 301);
}

$id = $_GET['id'];

$query = "SELECT owned_vehicles.id, owned_vehicles.plate, owned_vehicles.label, owned_vehicles.time_ocac, owned_vehicles.time_przeglad, users.firstname, users.lastname, users.identifier FROM `owned_vehicles` INNER JOIN `users` ON owned_vehicles.owner = users.identifier WHERE owned_vehicles.id = ?";
$stmt = $logic_instance->conn->prepare($query);
$stmt->bind_param("s", $id);
$result = $logic_instance->execute_query($stmt);
if($result->num_rows < 0){
    die("Nie ma takiego samochodu");
}

$vehicle = $result->fetch_assoc();
$vehicle['time_ocac'] = $vehicle['time_ocac'] == 0 ? "BRAK" : "TAK";
$vehicle['time_przeglad'] = $vehicle['time_przeglad'] == 0 ? "BRAK" : "TAK";

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
        <section class="specifics">
            <div class="row">
                <p class="index">Imię i Nazwisko</p>
                <p class="data"><?=$vehicle['firstname']." ".$vehicle['lastname']?></p>
            </div>
            <div class="row">
                <p class="index">nr. rejestracyjny</p>
                <p class="data"><?=$vehicle['plate']?></p>
            </div>
            <div class="row">
                <p class="index">Model</p>
                <p class="data"><?=$vehicle['label']?></p>
            </div>
            <div class="row">
                <p class="index">OC/AC</p>
                <p class="data"><?=$vehicle['time_ocac']?></p>
            </div>
            <div class="row">
                <p class="index">Przeglad</p>
                <p class="data"><?=$vehicle['time_przeglad']?></p>
            </div>

        </section>
    </section>
    <section class="note-form">
        <form method="post">
            <label for="note"><input type="text" name="note" placeholder="Powód poszukiwania..."></label>
            <?="<input type=\"hidden\" name=\"user-id\" value=\"".$vehicle['identifier']."\">"?>
            <?="<input type=\"hidden\" name=\"plate\" value=\"".$vehicle['plate']."\">"?>
            <button type="submit" class="note-submit-btn">Poszukuj</button>
        </form>
    </section>
</main>
</body>
</html>