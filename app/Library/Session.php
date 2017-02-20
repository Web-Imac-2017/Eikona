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

    //Track action
    public static $action;

    //Cipher informations
    public static $cipher = 'aes-256-cbc';
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

        self::$key = session_id();

<<<<<<< HEAD
            //And renew the current key for more safety
            self::renewKey(false);
=======
        if(self::sessionIsValid())
        {
            if(!self::keyIsValid())
            {
                self::initSession();

                self::$action = "INVALID KEY";

                //And renew the current key for more safety
                self::renewKey();
            }
            else if(rand(1, 100) <= 5)
            {
                //Has 5% of chance of renewing the key at anytime
                self::renewKey();
                self::$action = "RANDOM RENEW";
            }
>>>>>>> origin/sessions
        }
        else
        {
            //The session is obsolete, let's destroy it
            $_SESSION = array();
            session_destroy();
            session_start();

            self::$action = "INVALID SESSION";
        }

        self::$key = session_id();
    }

    public static function initSession()
    {
        //The session is not valid. Empty everything stored
        $_SESSION = array();

        //Set users infos
        $userInfos = ["IPadress" => $_SERVER['REMOTE_ADDR'],
                      "userAgent" => $_SERVER['HTTP_USER_AGENT']];

        $_SESSION['USER_INFOS'] = self::encrypt(json_encode($userInfos));
    }






    /**
     * Create a new key for the current session
     */
    public static function renewKey()
    {
        // If this session is obsolete it means there already is a new id
	    if(isset($_SESSION['OBSOLETE']))
            return;

        if(!self::$action)
            self::$action = "FORCED RENEW";


        // Set current session to expire in 10 seconds
	    $_SESSION['OBSOLETE'] = true;
	    $_SESSION['EXPIRES'] = time() + 10;

        //Keep old key for later
        $oldKey = session_id();

        session_regenerate_id(false);

        // Grab current session ID and close both sessions to allow other scripts to use them
	    self::$key = session_id();
        session_write_close();

	    // Set session ID to the new one, and start it back up again
	    session_id(self::$key);
	    session_start();

	    // Now we unset the obsolete and expiration values for the session we want to keep
	    unset($_SESSION['OBSOLETE']);
	    unset($_SESSION['EXPIRES']);

        $newSession = [];

        //And we re-encode all stored data in the session
        foreach($_SESSION as $var => $value)
        {
            if($var == 'USER_INFOS')
            {
                $newVar = $var;
                $uncryptedVar = $var;
            }
            else
            {
                //Use old key to decrypt data and reencode them with the new key
                $newVar = self::encrypt(self::decrypt($var, false, $oldKey), false);
            }

            $newValue = self::encrypt(self::decrypt($value, false, $oldKey), false);

            //Assign newly encrypted value
            $newSession[$newVar] = $newValue;

        }

        $_SESSION = $newSession;

    }

    /**
     * Confirm the current session is not obsolete
     */
    private static function sessionIsValid()
    {
        if(isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES']) )
            return false;

        if(isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time())
            return false;

	   return true;
    }

    /**
     * Confirm the current session belong to the user
     */
    private static function keyIsValid()
    {
        if(empty($_SESSION['USER_INFOS']))
            return false;

        //First, we try to decode the user infos with the current key
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
    public static function encrypt($string, $silent = false, $key = false)
    {
        //Get vector
        //$iv = mcrypt_create_iv(self::$ivLength);

        if($key == false)
            $key = self::$key;

        //Hash the key
        $keyHash = openssl_digest($key, self::$hash, true);

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
    public static function decrypt($string, $silent = false, $key = false)
    {
        if($key == false)
            $key = self::$key;

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
        $keyHash = openssl_digest($key, self::$hash, true);

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
        if($vName == 'USER_INFOS' || $vName == 'OBSOLETE' || $vName == 'EXPIRES')
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
        if($vName == 'USER_INFOS' || $vName == 'OBSOLETE' || $vName == 'EXPIRES')
            return;

        $encName = self::encrypt($vName);
        $encValue = self::encrypt($value);

        $_SESSION[$encName] = $encValue;
    }
}

/*header('Content-Type: application/json');
Session::open();
echo json_encode(["SESSION" => $_SESSION,
                  "TEST" => Session::read("TEST")]);*/

