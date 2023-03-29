<?php

require __DIR__ . '/vendor/autoload.php';

use \App\Pix\Payload;

$obPayload = (new Payload) ->setPixKey('richard.deus@gmail.com')
                           ->setDescription('Pedido referente a nota 123456')
                           ->setMerchantName('Rosa Leitao de Deus')
                           ->setMerchantCity('Rio de Janeiro')
                           ->setAmount(100.00)
                           ->setTxid('WDEV1234') 
                           ->setTxtMessage('Aguardo com ansiedade');


$payloadQrCode = $obPayload->getPayLoad();

echo "<pre>";
print_r($obPayload);
echo "</pre>";
exit;

