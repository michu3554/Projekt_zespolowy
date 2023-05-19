<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function connection($server_host, $db, $db_user, $db_password){
	$conn = new mysqli($server_host, $db_user, $db_password, $db) or die("Connection failed: " . $conn->connect_error);
	return $conn;
}

function db_select($conn, $table_name){
	$sql = "SELECT * FROM $table_name WHERE 1";
	$answer = $conn -> query($sql);
	return $answer;
}

$conn_rabbit = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $conn_rabbit -> channel();

$channel -> queue_declare('data_queue', false, true, false, false);

$conn = connection('127.0.0.1', 'Baza_danych_projekt', 'michu', '12345678');
$select = db_select($conn, 'Dane_wygenerowane');

if (mysqli_num_rows($select) > 0) {
    while ($record = mysqli_fetch_array($select)) {
        $msg = new AMQPMessage(json_encode($record), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel -> basic_publish($msg, '', 'data_queue');
    }
}
$channel -> close();
$conn_rabbit -> close();
$conn -> close();
?>
