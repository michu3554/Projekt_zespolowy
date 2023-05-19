<?php
function connection($server_host, $db, $db_user, $db_password){
	$conn = new mysqli($server_host, $db_user, $db_password, $db) or die("Connection failed: " . $conn->connect_error);
	return $conn;
}

function connection_close($conn){
	$conn->close();
}

function db_insert($conn, $table_name, $id, $number1, $number2, $text, $date, $time){
    $timestamp = microtime(true);
    $sql = "INSERT INTO $table_name (id, number1, number2, text, date, time, timestamp) VALUES ($id, $number1, $number2, '$text', '$date', '$time', $timestamp)";
    if ($conn->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn -> error;
    }
}

function db_insert_select($conn, $table_name, $record){
    $id = intval($record['id']);
    $number1 = intval($record['number1']);
    $number2 = floatval($record['number2']);
    $text = $record['text'];
    $date = date('Y-m-d', strtotime($record['date']));
    $time = date('H:i:s', strtotime($record['time']));

    db_insert($conn, $table_name, $id, $number1, $number2, $text, $date, $time);
}

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$conn_rabbit = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $conn_rabbit->channel();

$channel->queue_declare('data_queue', false, true, false, false);

$callback = function (AMQPMessage $msg) {
    $record = json_decode($msg->body, true);

    $conn = connection('127.0.0.1', 'Baza_danych_projekt', 'michu', '12345678');
    db_insert_select($conn, 'Dane_transfer_rabbitmq', $record);
	
    $msg->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('data_queue', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$conn_rabbit->close();
?>
