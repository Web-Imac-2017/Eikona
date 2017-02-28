<?php
class Sanitize
{
    /**
     * Replace all 'dangerous' html characters with their & counterparts
     *
     * @param $string The string to be parsed
     * @param $removeEmojis Shall we remove emoji as well?
     */
    static public function string($string, $removeEmojis = false)
    {
        $string = htmlspecialchars($string, ENT_QUOTES);

        if($removeEmojis)
            $string = self::removeEmojis($string);

        return $string;
    }

    /**
     * Remove all emojis from the given string
     *
     * @param $string The string to be parsed
     */
    static public function removeEmojis($string)
    {
        $cleanString = "";

        // Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $cleanString = preg_replace($regexEmoticons, '', $string);

        // Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $cleanString = preg_replace($regexSymbols, '', $cleanString);

        // Transport and Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $cleanString = preg_replace($regexTransport, '', $cleanString);

        return $cleanString;


    }

    /**
     * Validate $integer to be a integer. return value as integer if true, 0 otherwise.
     *
     * @param $integer The string to be parsed
     */
    static public function int($integer)
    {
        if(!ctype_digit(strval($integer)))
           return 0;

        return intval($integer);
    }

    /**
     * Validate $boolean to be a boolean. return the value of the boolean if true, false otherwise.
     *
     * @param $boolean The string to be parsed
     */
    static public function boolean($bool)
    {
        switch (strtolower($bool))
        {
            case '1':
            case 'true':
            case 'on':
            case 'yes':
            case 'y':
                return true;
            default:
                return false;
        }
    }

    static public function booleanToInt($bool)
    {
        if(self::boolean($bool))
            return 1;

        return 0;
    }

    /**
     * Format the given string to an acceptable string for profiles
     *
     * @param $boolean The string to be parsed
     */
    static public function profileName($pName)
    {
        //replace accented characters
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $pName);

        //Remove unwanted characters
        $name = preg_replace('/[\$+!*\'(),\{\}\|\\\^~[\]`<>\#%";\/\?:@&=\s+]/', '', $name);

        $name = filter_var($name, FILTER_SANITIZE_URL);

        //make sure the string is not too long
        $name = substr($name, 0, 30);

        return strtolower($name);
    }

    /**
     * Format the given string to an acceptable string for user
     * @param  text $uName user_name
     */
    public static function userName($uName)
    {
        //replace punctuation characters
        $name = preg_replace("#[[:punct:]]#", "", $uName);

        //sanitize
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $name = substr($name, 0, 30);

        if(strlen($name) == 0) return false;

        return $name;
    }

    /**
     * Format the given email to an acceptable email for user
     * @param  text $uEmail user_email   
     */
    public static function userEmail($uEmail)
    {
        //replace accented characters
        $email = iconv('UTF-8', 'ASCII//TRANSLIT', $uEmail);

        //Sanitize email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        //Validate email
        $validate = filter_var($email, FILTER_VALIDATE_EMAIL);

        if(!$validate || strlen($email) == 0) return false;

        return strtolower($email);
    }

}
