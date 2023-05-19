<?php
function connection($server_host, $db, $db_user, $db_password){
	$conn = new mysqli($server_host, $db_user, $db_password, $db) or die("Connection failed: " . $conn->connect_error);
	return $conn;
}

function connection_close($conn){
	$conn -> close();
}

function random_string($length){
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$len_characters = strlen($characters);
	$string = '';
	for ($i = 0; $i < $length; $i++){
		$string .=$characters[random_int(0, $len_characters - 1)];
	}
	return $string;
}

function gen_insert($conn, $table_name, $n){
	$id = db_max_id($conn, $table_name);
	$a = 0;
	for ($i = 0; $i < $n; $i++){
		$number1 = $i * 1525  - $i - $a * 999900;
		$number2 = floatval(rand(0, 999999)) / 100;
		if ($number1 > 999999){
			$a++;
			$number1 -= 999900;
		}
		$text = random_string(30);
		$date = date('Y-m-d');
		$time = date('H:i:s');
		
		db1_insert($conn, $table_name, $id, $number1, $number2, $text, $date, $time);
		$id++;
	}
}

function db1_insert($conn, $table_name, $id, $number1, $number2, $text, $date, $time){
	$id++;
	$sql = "INSERT INTO $table_name (id, number1, number2, text, date, time) VALUES ('$id', '$number1', '$number2', '$text', '$date', '$time')";
	if ($conn->query($sql) === FALSE) {
		echo "Error: " . $sql . "<br>" . $conn -> error;
	}
}

function select_count($conn, $table_name){
	$sql = "SELECT COUNT(id) FROM $table_name WHERE 1";
	$answer = $conn -> query($sql);
	$row = $answer -> fetch_assoc();
	return $row["COUNT(id)"];
}

function db_max_id($conn, $table_name){
	$sql = "SELECT MAX(id) FROM $table_name WHERE 1";
	$answer = $conn -> query($sql);
	$row = $answer -> fetch_assoc();
	if ($row['MAX(id)'] === NULL){
		$row['MAX(id)'] = 0;
	}
	return $row['MAX(id)'];
}

function db_select($conn, $table_name){
	$sql = "SELECT * FROM $table_name WHERE 1";
	$answer = $conn -> query($sql);
	return $answer;
}

function t_db_insert_select($conn, $table_name, $select){
	if (mysqli_num_rows($select) > 0){
		echo "Liczba rekordów przeniesionych do bazy2: " . mysqli_num_rows($select) . "<br>";
		while($record = mysqli_fetch_array($select)){
			$id = intval($record['id']);
			$number1 = intval($record['number1']);
			$number2 = floatval($record['number2']);
			$text = $record['text'];
			$date = date('Y-m-d', strtotime($record['date']));
			$time = date('H:i:s', strtotime($record['time']));
			
			t_db_insert($conn, $table_name, $id, $number1, $number2, $text, $date, $time);
		}
	}
}

function t_db_insert($conn, $table_name, $id, $number1, $number2, $text, $date, $time){
    $timestamp = microtime(true);
    $sql = "INSERT INTO $table_name (id, number1, number2, text, date, time, timestamp) VALUES ($id, $number1, $number2, '$text', '$date', '$time', $timestamp)";
    if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn -> error;
    }
}

function w_db_row($conn, $sql){
	$result = $conn -> query($sql);
	$row = $result -> fetch_assoc();
	return $row;
}

function echo_row($row){
	echo $row['id'] . " | ";
	echo $row['number1'] . " | ";
	echo $row['number2'] . " | ";
	echo $row['text'] . " | ";
	echo $row['date'] . " | ";
	echo $row['time'] . " | ";
}

function db_select_timestamp($conn, $table_name){
	$sql = "SELECT id, timestamp FROM $table_name WHERE 1 ORDER BY timestamp ASC;";
	$answer = $conn -> query($sql);
	return $answer;
}

function differences($select, $first_row){
	$previous_row = $select -> fetch_assoc();
	$diff = array();
	$i = 0;
	while ($current_row = $select -> fetch_assoc()){
		$sub = $current_row['timestamp'] - $previous_row['timestamp'];
		array_push($diff, $sub);
		$previous_row = $current_row;
		$i++;
	}
	return $diff;
}

function average_sub($diff){
	$sum = 0;
	for ($i = 0; $i < sizeof($diff); $i++){
		$sum += $diff[$i];
	}
	return $sum / sizeof($diff);
}

function min_sub($diff){
	$min = $diff[0];
	for ($i = 0; $i < sizeof($diff); $i++){
		if ($diff[$i] < $min){
			$min = $diff[$i];
		}
	}
	return $min;
}

function max_sub($diff){
	$max = $diff[0];
	for ($i = 0; $i < sizeof($diff); $i++){
		if ($diff[$i] > $max){
			$max = $diff[$i];
		}
	}
	return $max;
}

$server = '127.0.0.1';
$db1 = 'Baza_danych_projekt';
$db2 = 'Baza_danych_projekt';
$user = 'michu';
$password = '12345678';
$t1_name = 'Dane_wygenerowane';
$t2_name = 'Dane_transfer_phpdirect';
$t3_name = 'Dane_transfer_rabbitmq';

if (isset($_POST['dodaj_dane'])){
	$conn = connection($server, $db1, $user, $password);
	$number1 = intval($_POST['number1']);
	$number2 = floatval($_POST['number2']);
	$text = $_POST['text'];
	$date = date('Y-m-d', strtotime($_POST['date']));
	$time = date('H:i:s', strtotime($_POST['time']));
	echo "number1 " . $number1 . " type=" . gettype($number1);
	echo "<br>number2 " . $number2 . " type=" . gettype($number2);
	echo "<br>text " . $text . " type=" . gettype($text);
	echo "<br>date " . $date . " type=" . gettype($date);
	echo "<br>time " . $time . " type=" . gettype($time);
	db1_insert($conn, $t1_name,db_max_id($conn, $t1_name) , $number1, $number2, $text, $date, $time);
	connection_close($conn);
}
elseif (isset($_POST['generate'])){
	$conn = connection($server, $db1, $user, $password);
	gen_insert($conn, $t1_name, $_POST['gen_value']);
	echo "Liczba rekordów w $t1_name po wygenerowaniu: " . select_count($conn, $t1_name) . "<br>";
	connection_close($conn);
}
elseif (isset($_POST['transfer'])){
	$conn1 = connection($server, $db1, $user, $password);
	$conn2 = connection($server, $db2, $user, $password);
	$select = db_select($conn1, $t1_name);
	t_db_insert_select($conn2, $t2_name, $select);
	connection_close($conn1);
	connection_close($conn2);
}
elseif (isset($_POST['verification'])){
	$conn1 = connection($server, $db1, $user, $password);
	$conn2 = connection($server, $db2, $user, $password);

	$table_name = $t1_name;
	$sql = "SELECT * FROM $table_name ORDER BY id ASC LIMIT 1";
	$row1_first = w_db_row($conn1, $sql);
	$sql = "SELECT * FROM $table_name ORDER BY id DESC LIMIT 1";
	$row1_last = w_db_row($conn1, $sql);
	
	$table_name = $t2_name;
	$sql = "SELECT * FROM $table_name ORDER BY id ASC LIMIT 1";
	$row2_first = w_db_row($conn2, $sql);
	$sql = "SELECT * FROM $table_name ORDER BY id DESC LIMIT 1";
	$row2_last = w_db_row($conn2, $sql);
	
	$table_name = $t3_name;
	$sql = "SELECT * FROM $table_name ORDER BY id ASC LIMIT 1";
	$row3_first = w_db_row($conn2, $sql);
	$sql = "SELECT * FROM $table_name ORDER BY id DESC LIMIT 1";
	$row3_last = w_db_row($conn2, $sql);

	unset($row1_first['timestamp']);
	unset($row1_last['timestamp']);
	unset($row2_first['timestamp']);
	unset($row2_last['timestamp']);
	unset($row3_first['timestamp']);
	unset($row3_last['timestamp']);

	if ($row1_first == $row2_first && $row1_last == $row2_last && $row1_first == $row3_first && $row1_last == $row3_last){
		echo "Dane zgodne ze sobą<br><br>";
	  
		$select = db_select_timestamp($conn2, $t2_name);
		$select2 = db_select_timestamp($conn2, $t2_name);

		$first_row = $select -> fetch_assoc();
		$rows = $select->fetch_all(MYSQLI_ASSOC);
		$last_row = end($rows);
		mysqli_data_seek($select, 0);
		
		echo "Czas transferu przez skrypt = " . $sub = $last_row['timestamp'] - $first_row['timestamp'] . "<br>";
		echo "<br>";
		$diff = differences($select2, $first_row);
		echo "Średnia timestamp = " . average_sub($diff) . "<br>";
		echo "Min timestamp = " . min_sub($diff) . "<br>";
		echo "Max timestamp = " . max_sub($diff) . "<br>";
		
		$select = db_select_timestamp($conn2, $t3_name);
		$select2 = db_select_timestamp($conn2, $t3_name);

		$first_row = $select -> fetch_assoc();
		$rows = $select->fetch_all(MYSQLI_ASSOC);
		$last_row = end($rows);
		mysqli_data_seek($select, 0);
		
		echo "<hr>Czas transferu przez RabbitMQ = " . $sub = $last_row['timestamp'] - $first_row['timestamp'] . "<br>";
		echo "<br>";
		$diff = differences($select2, $first_row);
		echo "Średnia timestamp = " . average_sub($diff) . "<br>";
		echo "Min timestamp = " . min_sub($diff) . "<br>";
		echo "Max timestamp = " . max_sub($diff) . "<br>";
	}
	else {
		echo "Wykryto niezgodność<br><br>";
		echo "Dane_wygenerowane: ";
		echo echo_row($row1_first) . "<br>";
		echo "Dane_transfer_phpdirect: ";
		echo echo_row($row2_first) . "<br><br>";
		echo "Dane_wygenerowane: ";
		echo echo_row($row1_last) . "<br>";
		echo "Dane_transfer_phpdirect: ";
		echo echo_row($row2_last) . "<br>";
	}
	connection_close($conn1);
	connection_close($conn2);
}
elseif (isset($_POST['truncate1'])){
	$conn = connection($server, $db1, $user, $password);
	$sql = "TRUNCATE TABLE $t1_name";
	if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn -> error;
    }
	connection_close($conn);
}
elseif (isset($_POST['truncate2'])){
	$conn = connection($server, $db2, $user, $password);
	$sql = "TRUNCATE TABLE $t2_name";
	if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn -> error;
    }
	$sql = "TRUNCATE TABLE $t3_name";
	if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn -> error;
    }
	connection_close($conn);
}
elseif (isset($_POST['transfer_rabbit'])){
	$conn = connection($server, $db1, $user, $password);
	include('gen_rabbit_send.php');
	rabbit($conn);
	connection_close($conn);
}

$conn1 = connection($server, $db1, $user, $password);
$conn2 = connection($server, $db2, $user, $password);
echo "<hr>Liczba rekordów w $t1_name = " . select_count($conn1, $t1_name) . "<br>";
echo "Liczba rekordów w $t2_name = " . select_count($conn2, $t2_name) . "<br>";
echo "Liczba rekordów w $t3_name = " . select_count($conn2, $t3_name) . "<br>";
connection_close($conn1);
connection_close($conn2);

?>
