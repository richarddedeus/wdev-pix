<?php

require __DIR__ . '/vendor/autoload.php';

use \App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;


//Instancia principal do payload pix 
$obPayload = (new Payload) ->setPixKey('richard.deus@gmail.com')
                           ->setDescription('Pedido referente a nota 123456')
                           ->setMerchantName('Rosa Leitao de Deus')
                           ->setMerchantCity('Rio de Janeiro')
                           ->setAmount(100.00)
                           ->setTxid('WDEV1234') 
                           ->setTxtMessage('Aguardo com ansiedade');

//Código de pagamento pix
$payloadQrCode = $obPayload->getPayLoad();

//Instancia do qrcode
$obQrCode = new QrCode($payloadQrCode);


$image = (new Output\Png)->output($obQrCode, 400, [0,50,0]);
//var_dump($image);die;

//header('Content-Type: image/png');
//echo $image;

//print_r($payloadQrCode);

?>

<h1>Qrcode Pix</h1>
<br>
<?//=$image;?>
<!--<img src="data:image/png;base64, <?//=base64_encode($image);?>">-->
<img src="data:image/png;base64, <?=base64_encode($image);?>">
<br>
<p>Código Pix (Copia e cola)</p>
<strong><?=$payloadQrCode;?></strong>

