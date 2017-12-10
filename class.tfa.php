<?php

require_once("../../common.php");
require_once("lib/GoogleAuthenticator.php");

define('LIMIT', 300); //5 Minuten
define('CONFIG', 'tfa.php');

class TFA {

    public $username    = '';
    public $password    = '';
    public $token       = '';
    
    public $password_encrypted = '';
    public $returnMessage = '';
    
    function __construct() {
        if (!file_exists(DATA . '/' . CONFIG)) {
            saveJSON(CONFIG, array());
        }
    }

    public function Authenticate(){

        $this->EncryptPassword();
        $users = getJSON('users.php');
        $tfa = getJSON(CONFIG);
        foreach($users as $user){
            if($user['username']==$this->username && $user['password']==$this->password_encrypted){

                $_SESSION['codiad_tfa'] = false;
                if (isset($tfa[$this->username])) {
                    $_SESSION['codiad_tfa'] = true;
                    if ($this->token == '') {
                        $this->returnMessage = json_encode(array('status' => "tfa", 'message' => "Please enter Token"));
                        return;
                    }
                    //TFA validate token
                    if (!$this->CheckToken($this->token)) {
                        $this->returnMessage = json_encode(array('status' => "error", 'message' => "Incorrect Token"));
                        return;
                    }
                }

                $_SESSION['user'] = $this->username;
                if($user['project']!=''){ $_SESSION['project'] = $user['project']; }

                $this->returnMessage = json_encode(array('status' => "success"));
                return true;
            }
        }

        $this->returnMessage = json_encode(array('status' => "error", 'message' => "Incorrect Username or Password"));
        return false;
    }
    
    public function EnableTFA() {
        if ($this->Authenticate()) {
            $authenticator = new GoogleAuthenticator();
            $result = $authenticator->verifyCode($_SESSION['codiad_tfa_secret'], $this->token, 4);
            if ($result) {
                $secret = $this->EncryptSecret($_SESSION['codiad_tfa_secret']);
                unset($_SESSION['codiad_tfa_secret']);
                if ($secret === false) {
                    $this->returnMessage = json_encode(array('status' => "error", 'message' => "Unable to encrypt secret"));
                    return;
                }
                $this->SaveSecret($secret);
                $this->SetTokenAsUsed($this->token);

                $_SESSION['codiad_tfa'] = true;
                $this->returnMessage = json_encode(array('status' => "success", 'message' => "Two Factor Authentication enabled"));
            } else {
                print_r($_POST);
                print_r("false");
                die();
                $this->returnMessage = json_encode(array('status' => "error", 'message' => "Token is wrong"));
            }
        }
    }
    
    public function DisableTFA() {
        if ($this->Authenticate()) {
            //Delete TFA
            $tfa = getJSON(CONFIG);
            unset($tfa[$this->username]);
            saveJSON(CONFIG, $tfa);
            $_SESSION['codiad_tfa'] = false;
            $this->returnMessage = json_encode(array('status' => "success", 'message' => "Two Factor Authentication disabled"));
            return true;
        }
        $this->returnMessage = json_encode(array('status' => "error", 'message' => "Wrong Password or Token"));
        return false;
    }
    
    public function IsTFAEnabled() {
        if (isset($_SESSION['codiad_tfa'])) {
            return $_SESSION['codiad_tfa'];
        }
        return false;
    }
    
    public function GenerateSecret() {
        $authenticator = new GoogleAuthenticator();
        $secret = $authenticator->createSecret();
        $_SESSION['codiad_tfa_secret'] = $secret;
        return $authenticator->getQRCodeGoogleUrl($this->username, $secret, "Codiad");
    }

    private function EncryptPassword(){
        $this->password_encrypted = sha1(md5($this->password));
    }
    
    //////////////////////////////////////////////////////////////////
    // Set token as used
    //////////////////////////////////////////////////////////////////
    
    private function SetTokenAsUsed($token) {
        $tokens = $this->GetUsedTokens();
        array_push($tokens, array("token" => $token, "time" => time()));
        return $this->SaveUsedTokens($tokens);
    }
    
    //////////////////////////////////////////////////////////////////
    // Check if Token is used
    //
    //  True if used
    //  False if unused
    //////////////////////////////////////////////////////////////////
    
    private function IsTokenUsed($token) {
        $tokens = $this->GetUsedTokens();
        //Check Token
        $result = false;
        $limit  = time() - LIMIT;
        foreach($tokens as $index => $element) {
            if ($element['token'] == $token) {
                if ($element['time'] > $limit) {
                    $result = true;
                    break;
                }
                
            }
            //Delete older tokens
            if ($element['time'] < $limit) {
                unset($tokens[$index]);
            }
        }
        //Save Tokens again
        $this->SaveUsedTokens($tokens);
        return $result;
    }
    
    //////////////////////////////////////////////////////////////////
    // Get Tokens
    //////////////////////////////////////////////////////////////////
    
    private function GetUsedTokens() {
        $tfa = getJSON(CONFIG);
        if (isset($tfa[$this->username]) && isset($tfa[$this->username]['tokens'])) {
            return $tfa[$this->username]['tokens'];
        } else {
            return array();
        }
    }
    
    //////////////////////////////////////////////////////////////////
    // Save Tokens
    //////////////////////////////////////////////////////////////////
    
    private function SaveUsedTokens($tokens) {
        $tfa = getJSON(CONFIG);
        if (!isset($tfa[$this->username])) {
            $tfa[$this->username] = array();
        }
        $tfa[$this->username]['tokens'] = $tokens;
        saveJSON(CONFIG, $tfa);
    }
    
    //////////////////////////////////////////////////////////////////
    // Save Secret
    //////////////////////////////////////////////////////////////////
    
    private function SaveSecret($secret) {
        $tfa = getJSON(CONFIG);
        if (!isset($tfa[$this->username])) {
            $tfa[$this->username] = array();
        }
        $tfa[$this->username]['secret'] = $secret;
        saveJSON(CONFIG, $tfa);
    }
    
    //////////////////////////////////////////////////////////////////
    // Get Secret
    //////////////////////////////////////////////////////////////////
    
    private function GetSecret() {
        $tfa = getJSON(CONFIG);
        if (isset($tfa[$this->username]) && isset($tfa[$this->username]['secret'])) {
            return $tfa[$this->username]['secret'];
        } else {
            return false;
        }
    }
    
    //////////////////////////////////////////////////////////////////
    // Check token
    //////////////////////////////////////////////////////////////////
    
    private function CheckToken($token) {
        if ($this->password == '') {
            $this->returnMessage = json_encode(array('status' => "error", 'message' => "Missing Password"));
            return false;
        }
        //Check if token is used
        if ($this->IsTokenUsed($token)) {
            $this->returnMessage = json_encode(array('status' => "error", 'message' => "Token already used"));
            return false;
        }
        //Decrypt secret
        $secret = $this->DecryptSecret($this->GetSecret());
        $authenticator = new GoogleAuthenticator();
        $result = $authenticator->verifyCode($secret, $token, 4);
        //save Token as used
        if ($result) {
            $this->SetTokenAsUsed($token);
        }
        return $result;
    }
    
    //////////////////////////////////////////////////////////////////
    // Encrypt Secret
    //////////////////////////////////////////////////////////////////
    
    private function EncryptSecret($secret) {
        if ($this->password == '') {
            return false;
        }
        $iv = $this->GetIV();
        $key = str_pad($this->password,32,'\0');
        return base64_encode($iv) .'||'. base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $secret, MCRYPT_MODE_ECB, $iv));
    }
    
    //////////////////////////////////////////////////////////////////
    // Decrypt Secret
    //////////////////////////////////////////////////////////////////
    
    private function DecryptSecret($secret) {
        if ($this->password == '') {
            return false;
        }
        $key = str_pad($this->password,32,'\0');
        $position   = strpos($secret, '||');
        $iv         = base64_decode(substr($secret, 0, $position));
        $coded      = base64_decode(substr($secret, $position+2, strlen($secret)));
        $decrypted  = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $coded, MCRYPT_MODE_ECB, $iv);
        //trim the nulls at the END (Tipp von php.net)
        return rtrim($decrypted, "\0");
    }
    
    /////////////////////////////////////////////////////////////////
    // Get initialization vector
    //////////////////////////////////////////////////////////////////
    
    private function GetIV() {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        return random_bytes($iv_size);
    }
}