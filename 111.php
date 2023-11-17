<?
    $ch = curl_init();

    /*$data=Array(
        "messages" => Array(
            "recipient" => "79841411260",
            "recipientType" => "recipient",
            "id" => "string",
            "source" => "string",
            "timeout" => 3600,
            "shortenUrl" => true,
            "text" => "СМС-код: 1234"
        ),
        "validate" => true
    );*/

$data='{"messages": [
{
"recipient": "79841411260",
"recipientType": "recipient",
"id": "string",
"source": "string",
"timeout": 3600,
"shortenUrl": true,
"text": "СМС-код: 1234"
}
],
"validate": false
}';

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://lcab.smsprofi.ru/json/v1.0/sms/send/text",
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "X-Token: 25wfgh3xqkql3tof2dmivul9smv6x8uvnawj99i6bvj0vr1u4csicbfy8pmpbrsv",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $result = curl_exec($ch);

    echo $result."\n";

    var_dump(json_decode($result, true));
?>