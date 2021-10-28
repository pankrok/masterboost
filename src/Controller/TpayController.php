<?php

namespace App\Controller;

use tpayLibs\src\_class_tpay\PaymentForms\PaymentBasicForms;
use tpayLibs\src\_class_tpay\Utilities\Util;
use Symfony\Component\Yaml\Yaml;

class TpayController extends PaymentBasicForms
{
    public function __construct($dir, $baseUrl, $locale)
    {
        $cfg = Yaml::parseFile($dir . DIRECTORY_SEPARATOR  . 'config' . DIRECTORY_SEPARATOR  .'tpay.yaml');
        Util::$libraryPath =  $baseUrl . 'assets'.DIRECTORY_SEPARATOR .'tpay' . DIRECTORY_SEPARATOR ;
        Util::$lang  =  $locale === 'pl' ? 'pl' : 'en';
        $this->merchantSecret = $cfg['merchantSecret'];
        $this->merchantId = $cfg['merchantId'];
        parent::__construct();
    }

    public function generateForm(array $config) : string
    {

        // $config = array(
            // 'amount' => 999.99,
            // 'description' => 'Transaction description',
            // 'crc' => '100020003000',
            // 'return_url' => 'https://www.foo.com,
            // 'result_url' => 'http://example.pl/examples/paymentBasic.php',
            // 'result_email' => 'shop@example.com',
            // 'email' => 'customer@example.com',
            // 'name' => 'John Doe',
        // );
        return $this->getBankSelectionForm($config, false, true, null, true);
    }
    
    public function getCode(){
            return $this->merchantSecret;
    }
}