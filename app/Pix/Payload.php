<?php

namespace App\Pix;

class Payload{
      /**
   * IDs do Payload do Pix
   * @var string
   */
  const ID_PAYLOAD_FORMAT_INDICATOR = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
  const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
  const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
  const ID_MERCHANT_CATEGORY_CODE = '52';
  const ID_TRANSACTION_CURRENCY = '53';
  const ID_TRANSACTION_AMOUNT = '54';
  const ID_COUNTRY_CODE = '58';
  const ID_MERCHANT_NAME = '59';
  const ID_MERCHANT_CITY = '60';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
  const ID_CRC16 = '63';

  /**
   * Chave Pix
   *
   * @var string
   */
  private $pixKey;

  /**
   * Descrição do pagamento
   *
   * @var string
   */
  private $description;

  /**
   * Nome do titular da conta
   *
   * @var string
   */
  private $merchantName;

  /**
   * Cidade do tutular da conta
   *
   * @var string
   */
  private $merchantCity;

  /**
   * ID da transação Pix
   *
   * @var string
   */
  private $txid;

  /**
   * Valor da transação Pix
   *
   * @var string
   */
  private $amount;

  /**
   * Texto da mensagem
   *
   * @var string
   */
  private $txtmessage;


  /**
   * Método responsável por definir o valor do $pixKey
   *
   * @param string $pixKey
   * @return void
   */  
  public function setPixKey($pixKey){
    $this->pixKey = $pixKey;
    return $this;
  }

  /**
   * Método responsável por definir o valor do $description
   *
   * @param string $description
   * @return void
   */  
  public function setDescription($description){
    $this->description = $description;
    return $this;
  }

  /**
   * Método responsável por definir o valor do $merchantName
   *
   * @param string $merchantName
   * @return void
   */  
  public function setMerchantName($merchantName){
    $this->merchantName = $merchantName;
    return $this;
  }

  /**
   * Método responsável por definir o valor do $merchantCity
   *
   * @param string $merchantCity
   * @return void
   */  
  public function setMerchantCity($merchantCity){
    $this->merchantCity = $merchantCity;
    return $this;
  }

  /**
   * Método responsável por definir o valor do $txid
   *
   * @param string $txid
   * @return void
   */  
  public function setTxid($txid){
    $this->txid = $txid;
    return $this;
  }

  /**
   * Método responsável por definir o valor do $amount
   *
   * @param float $amount
   * @return void
   */  
  public function setAmount($amount){
    $this->amount = (string) number_format($amount, 2, '.', '');
    return $this;
  }

  /**
   * Método responsável por definir o valor do $txtmessage
   *
   * @param string $txtmessage
   * @return void
   */  
  public function setTxtMessage($txtmessage){
    $this->txtmessage = $txtmessage;
    return $this;
  }

  /**
   * Responsável por retornar o valor completo de um objeto do payload
   *
   * @param string $id
   * @param string $value
   * @return string $id.$value
   */
  private function getValue($id,$value){
    $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
    return $id.$size.$value;
  }

  /**
   * Método responsável por retornar os valores completos da informação da conta
   *
   * @return string
   */
  private function getMerchantAccountInformation(){
    //Domínio do Banco
    $gui = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');
    //Chave pix
    $key = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->pixKey);
    //Descrição do pagamento
    $description = strlen($this->description) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';
    //valor compelto da conta
    return $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION,$gui.$key.$description);
  }

  /**
   * Método responsável por retornar os valores do campo adicional do pix(TXID)
   *
   * @return string
   */
  private function getAdditionalDataFieldTemplate(){
    //TXID
    $txid = $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);

    //Retorna o valor completo
    return $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);

  }
  
  /**
   * Método responsável por gerar o código completo do payload pix
   *
   * @return string
   */
  public function getPayLoad() {
    //Cria o payload
    $payload = $this->getValue(self::ID_PAYLOAD_FORMAT_INDICATOR, '01').
               $this->getMerchantAccountInformation().
               $this->getValue(self::ID_MERCHANT_CATEGORY_CODE, '0000').
               $this->getValue(self::ID_TRANSACTION_CURRENCY, '986').
               $this->getValue(self::ID_TRANSACTION_AMOUNT, $this->amount).
               $this->getValue(self::ID_COUNTRY_CODE, 'BR').
               $this->getValue(self::ID_MERCHANT_NAME, $this->merchantName).
               $this->getValue(self::ID_MERCHANT_CITY,$this->merchantCity).
               $this->getAdditionalDataFieldTemplate();

    //Retorna o payload + CRC16
    return $payload.$this->getCRC16($payload);
  }

  /**
   * Método responsável por calcular o valor da hash de validação do código pix
   * @return string
   */
  private function getCRC16($payload) {
    //ADICIONA DADOS GERAIS NO PAYLOAD
    $payload .= self::ID_CRC16.'04';

    //DADOS DEFINIDOS PELO BACEN
    $polinomio = 0x1021;
    $resultado = 0xFFFF;

    //CHECKSUM
    if (($length = strlen($payload)) > 0) {
        for ($offset = 0; $offset < $length; $offset++) {
            $resultado ^= (ord($payload[$offset]) << 8);
            for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                $resultado &= 0xFFFF;
            }
        }
    }

    //RETORNA CÓDIGO CRC16 DE 4 CARACTERES
    return self::ID_CRC16.'04'.strtoupper(dechex($resultado));
}
}