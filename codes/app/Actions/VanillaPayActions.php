<?php

use phpseclib\Crypt\TripleDES;

class VanillaPayActions
{
    protected string $public_key;
    protected string $private_key;
    protected string $site_url;
    protected string $ip;
    protected TripleDES $des;
    protected $id;
    protected $total;
    protected string $username;
    protected $transactionnable_id;

    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->public_key =  env('VANILLA_PAY_PUBLIC_KEY');
        $this->private_key = env('VANILLA_PAY_PRIVATE_KEY');
        $this->site_url = env('FRONTEND_URL');
        $this->ip = env('APP_IP_ADDRESS');
        $this->des = new TripleDES();
    }

    public function setTotal($total): static
    {
        $this->total = $total;
        return $this;
    }

    public function setUsername($username): static
    {
        $this->username = $username;
        return $this;
    }

    public function setTransactionnableId($transactionnable_id): static
    {
        $this->transactionnable_id = $transactionnable_id;
        return $this;
    }

    protected function get_access_token()
    {
        $param = array(
            'client_id' => '',
            'client_secret' => '',
            'grant_type' => 'client_credentials'
        );

        $curl = curl_init();
        $url = 'https://pro.ariarynet.com/oauth/v2/token'; // NE PAS CHANGER

        curl_setopt($curl, CURLOPT_HTTPHEADER, array());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        curl_close($curl);

        // on obtient le token à utiliser pour les transactions
        $json = json_decode($result);
        $token = $json->access_token;

        return $token;
    }

    public function decrypt(string $data)
    {
        $this->des->setKey($this->private_key);
        return $this->des->decrypt($data);
    }

    public function encrypt(string $plaintext)
    {
        $this->des->setKey($this->public_key);
        return $this->des->encrypt($plaintext);
    }

    protected function get_payment_id()
    {
        $token = self::get_access_token();
        $headers = array("Authorization:Bearer " . $token);
        $now = new DateTime(); // DateTime // Date du paiement  (now)
        $daty = $now->format('Y-m-d'); // formattage de date

        $params_to_send = array(
            "unitemonetaire" => "Ar",
            "adresseip"      => $this->ip,
            "date"           => $daty,
            "idpanier"       => $this->transactionnable_id,
            "montant"        => $this->total,
            "nom"            => $this->username,
            "reference"      => ''
        );

        $params_crypt = $this->encrypt(json_encode($params_to_send));

        //initialisation paiement
        $params = array(
            "site_url" => $this->site_url,
            "params"   => $params_crypt
        );
        // executer un curl pour obtenir un id_paiement qu'on utilisera plus tard
        $curl = curl_init();
        $url = 'https://pro.ariarynet.com/api/paiements'; // NE PAS CHANGER

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        curl_close($curl);

        $id = $this->decrypt($result);
        $this->id = $id;

        return $id;
    }

    public function get_payment_url()
    {
        $id = $this->get_payment_id();
        return "https://moncompte.ariarynet.com/payer/$id";
    }

    public function get_orange_money_payment_url()
    {
        $id = $this->get_payment_id();
        return "https://moncompte.ariarynet.com/redirect?paiement=$id&orange=true";
    }

    public function get_airtel_money_payment_url()
    {
        $id = $this->get_payment_id();
        return "https://moncompte.ariarynet.com/redirect?paiement=$id&airtel=true";
    }

    public function get_mvola_payment_url()
    {
        $id = $this->get_payment_id();
        return "https://moncompte.ariarynet.com/redirect?paiement=$id&mvola=true";
    }
}
