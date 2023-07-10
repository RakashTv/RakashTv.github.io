<?php
include '../config/config.php';

class Logic{
    public $conn;

    public function __construct($host, $username, $password, $database){
        $this->conn = new mysqli($host, $username, $password, $database);
        if($this->conn->error){
            exit("Wystąpił problem z połaczeniem bazy danych!");
        }
    }

	public static function get_ip(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}



	//Funkcje systemu licencji

	public function execute_query($stmt){
        $stmt->execute();
        return $stmt->get_result();
	}

	public function get_all_users(){
		$query = "SELECT `firstname`, `lastname`, `dateofbirth`, `sex`, `height`, `identifier`, `avatar` FROM `users`";
        $stmt = $this->conn->prepare($query);
		$result = $this->execute_query($stmt);

		$counter = 1;
		$message = "";

		while ($citizen = $result->fetch_assoc()) {
			if ($citizen['firstname'] != ""){
                $citizen['sex'] = $citizen['sex'] == 1 ? "Kobieta" : "Mężczyzna";

				$message .= '
						<tr>
							<td>'.$counter.'</td>
							<td>'.$citizen['firstname'].' '.$citizen['lastname'].'</td>
							<td>'.$citizen['dateofbirth'].'</td>
							<td>'.$citizen['sex'].'</td>
							<td>'.$citizen['height'].'</td>
							<td>
							    <a href="../view/kartoteka.php?id='.$citizen['identifier'].'" class="id-btn">📝&nbsp;Szczegóły</a>
							    <a href="../view/poszukiwanie_obywatela.php?id='.$citizen['identifier'].'" class="id-btn">☠️</a>
							</td>
						</tr>
						';
			}
			$counter++;
		}
		echo $message;
	}

    public function add_post($content, $type, $id = null){
        if($type == "note"){
            $query = "INSERT INTO lspd_panel(note, identifier) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $content, $id);
        } else {
            $query = "INSERT INTO lspd_ogloszenia(info) VALUES (?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $content);
        }
        return $stmt->execute();
    }

	public function get_all_vehicles(){
		$query = "SELECT owned_vehicles.id, owned_vehicles.plate, owned_vehicles.label, owned_vehicles.time_ocac, owned_vehicles.time_przeglad, users.firstname, users.lastname, users.identifier FROM `owned_vehicles` INNER JOIN `users` ON owned_vehicles.owner = users.identifier";
        $stmt = $this->conn->prepare($query);
        $result = $this->execute_query($stmt);

		$counter = 1;
		$message = "";

		while ($row = $result->fetch_assoc()) {
			if ($row['time_przeglad'] > 1) {
				$przeglad = "<span style='color:green'>Tak</span>/";
			}else{
				$przeglad = "<span style='color:red'>Nie</span>/";
			}

            if ($row['time_ocac'] > 1) {
                $oc = "<span style='color:green'>Tak</span>";
            }else{
                $oc = "<span style='color:red'>Nie</span>";
            }

				$message .= '
						<tr>
							<th scope="row">'.$counter.'</th>
							<td>'.$row['firstname'].' '.$row['lastname'].'</td>
							<td>'.$row['plate'].'</td>
							<td>'.$row['label'].'</td>
							<td>'.$przeglad.$oc.'</td>
							<td>
							    <a href="../view/kartoteka.php?id='.$row['identifier'].'" class="id-btn">O&nbsp;Właścicielu</a> 
							    <a href="../view/poszukiwanie_pojazdu.php?id='.$row['id'].'" class="id-btn">☠️</a>
							</td>
						</tr>
						';
            $counter++;
		}
		echo $message;
	}

	public function get_all_prices(){
		$query = "SELECT `category`, `label`, `min`, `max` FROM `billing_types` WHERE `society` = 'society_police'";
        $stmt = $this->conn->prepare($query);
        $result = $this->execute_query($stmt);

		$counter = 1;
		$message = "";

		foreach ($result as $value) {
				$message .= '
						<tr>
							<td >'.$counter.'</td>
							<td>'.$value['category'].'</td>
							<td>'.$value['label'].'</td>
							<td>'.$value['min'].'</td>
							<td>'.$value['max'].'</td>
						</tr>
						';
            $counter++;
		}
		echo $message;
	}
}

$logic_instance = new Logic(DB_HOST, DB_USER, DB_PASS, DB_NAME);