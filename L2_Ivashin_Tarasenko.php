<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L2_Ivashin_Tarasenko</title>
</head>
<body>
    <form action="" method="GET">
        Введите сообщение <input type="text" name="message"><br/>
        <input type="submit" value="Зашифровать" name="encrypt"><br/>
        <input type="submit" value="Расшифровать" name="decrypt"><br/>
    </form>
</body>
</html>
<?php
/*
$i = rand(1, 100);
//openssl_random_pseudo_bytes — Генерирует псевдослучайную последовательность байт
$bytes = openssl_random_pseudo_bytes($i, $cstrong);
$key = bin2hex($bytes);
$fp = fopen("key.txt", "w");
fwrite($fp, $key);
fclose($fp);
*/

// $key должен быть сгенерирован заранее криптографически безопасным образом
// например, с помощью openssl_random_pseudo_bytes

//Считываем ключ из файла
$fp = fopen("key.txt", "r"); // Открываем файл в режиме чтения
if ($fp) {
    while (!feof($fp)) {
        $key = fgets($fp, 999);
    }
} else echo "Ошибка при открытии файла";
fclose($fp);

// $key должен быть сгенерирован заранее криптографически безопасным образом
// например, с помощью openssl_random_pseudo_bytes
if ($_GET["encrypt"]) {
    // $key должен быть сгенерирован заранее криптографически безопасным образом
    // например, с помощью openssl_random_pseudo_bytes
    $plaintext = (string)$_GET["message"];
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
    echo $ciphertext."\n";
} elseif ($_GET["decrypt"]) {
    // расшифровка....
    $ciphertext = (string)$_GET["message"];
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    //if (hash_equals($hmac, $calcmac))// с PHP 5.6+ сравнение, не подверженное атаке по времени
    //{
        echo $original_plaintext."\n";
    //}
}
?>