<?php
/**
 * Session more security to the basic PHP sessions.
 * Session are validated before beiing used to prevent session theft.
 * Data stored in session are encrypted to ensure nobody except the session owner can read them.
 *
 * TODO : Handle generating new session ID and AJAX
 */

class Session
{
    public static $key;
    public static $age;

    //Cipher informations
    public static $cipher = 'aes-256-ctr';
    public static $hash = 'sha256';
    public static $ivLength = 16;
    public static $iv = "56897ngq8907345f";


    /**
     * Init the session
     */
    public static function open()
    {
        session_set_cookie_params(0, '/', null, false, true);
        session_start();

        if(!self::keyIsValid())
        {
            //The session is not valid. Empty everything stored
            $_SESSION = array();

            //Set users infos
            $userInfos = ["IPadress" => $_SERVER['REMOTE_ADDR'],
                          "userAgent" => $_SERVER['HTTP_USER_AGENT']];

            $_SESSION['USER_INFOS'] = self::encrypt(json_encode($userInfos));

            //And renew the current key for more safety
            self::renewKey();
        }
        else
        {
            self::$key = session_id();
        }
    }






    /**
     * Create a new key for the current session
     */
    public static function renewKey($destroyOldSession = false)
    {
        //Create a new key
        session_regenerate_id($destroyOldSession);

        self::$key = session_id();
    }

    /**
     * Confirm the given key belong to the current user
     */
    private static function keyIsValid()
    {
        if(empty($_SESSION['USER_INFOS']))
            return false;

        //First, we try to decode the user infos with the given key
        $userInfos = self::decrypt($_SESSION['USER_INFOS'], true);

        if($userInfos === false)
            return false;

        $userInfos = json_decode($userInfos, true);

        if($userInfos['IPadress'] != $_SERVER['REMOTE_ADDR'])
            return false;

        if($userInfos['userAgent'] != $_SERVER['HTTP_USER_AGENT'])
            return false;

        return true;
    }






    /**
     * Encrypt the given value with the current key
     *
     * @param String $value What to encrypt
     */
    public static function encrypt($string, $silent = false)
    {
        //Get vector
        //$iv = mcrypt_create_iv(self::$ivLength);

        //Hash the key
        $keyHash = openssl_digest(self::$key, self::$hash, true);

        //Crypt the string
        $opts =  OPENSSL_RAW_DATA;
        $encrypted = openssl_encrypt($string, self::$cipher, $keyHash, $opts, self::$iv);

        if($encrypted === false)
        {
            if($silent)
                return false;

            throw new \Exception('Session::encrypt() - Encryption failed: ' . openssl_error_string());
        }

        //Join the iv to the encrypted string
        $res = self::$iv.$encrypted;

        //Set as a hex string
        $res = unpack('H*', $res)[1];

        return $res;
    }

    /**
     * Decrypt the given value with the current key
     *
     * @param String $vName What to decrypt
     */
    public static function decrypt($string, $silent = false)
    {
        //Get raw data from hex
        $raw = pack('H*', $string);

        if (strlen($raw) < self::$ivLength)
        {
            if($silent)
                return false;

            throw new \Exception('Session::decrypt() - '.'data length ' . strlen($raw) . " is less than iv length {$this->iv_num_bytes}");
        }

        // Extract the initialisation vector and encrypted data
        $iv = substr($raw, 0, self::$ivLength);
        $raw = substr($raw, self::$ivLength);

        // Hash the key
        $keyHash = openssl_digest(self::$key, self::$hash, true);

        // and decrypt.the string
        $opts = OPENSSL_RAW_DATA;
        $res = openssl_decrypt($raw, self::$cipher, $keyHash, $opts, $iv);

        if ($res === false)
        {
            if($silent)
                return false;

            throw new \Exception('Session::decrypt() - decryption failed: ' . openssl_error_string());
        }

        return $res;
    }







    /**
     * Read a value from the session
     *
     * @param String $vName Name of the value to read
     */
    public static function read($vName)
    {
        //USER_INFOS is a restricted value
        if($vName == 'USER_INFOS')
            return;

        $encName = self::encrypt($vName);

        if(!array_key_exists($encName, $_SESSION))
            return;

        $value = self::decrypt($_SESSION[$encName]);

        return $value;
    }

    /**
     * Write to the session
     *
     * @param String $vName Name of the value to write to
     * @param mixed $value Value of the key
     */
    public static function write($vName, $value)
    {
        //USER_INFOS is a restricted value
        if($vName == 'USER_INFOS')
            return;

        $encName = self::encrypt($vName);
        $encValue = self::encrypt($value);

        $_SESSION[$encName] = $encValue;
    }

    /**
     * Remove a key from the session
     *
     * @param String $vName Name of the key to be removed
     */
    public static function remove($vName) {}

    /**
     * Empty the session
     */
    public static function destroy()
    {
        //Destroy the session cookie
        $params = session_get_cookie_params();

        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

        //and finally, destroy the session
        session_destroy();
    }
}

