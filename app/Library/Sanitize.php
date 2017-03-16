<?php

interface SanitizeInterface
{
    static public function string($string, $removeEmojis = false, $removeBannedWords = true);

    static public function removeEmojis($string);

    static public function int($integer);

    static public function boolean($bool);

    static public function booleanToInt($bool);

    static public function profileName($pName);

    public static function userName($uName);

    public static function userEmail($uEmail);

    public static function bannedWords($string);
}

class Sanitize implements SanitizeInterface
{
    /**
     * Replace all 'dangerous' html characters with their & counterparts
     * @param  string  $string            The string to sanitize
     * @param  boolean $removeEmojis      Remove emojis from string or not
     * @param  boolean $removeBannedWords Remove bannes words or not
     * @return string  The string sanitize
     */
    static public function string($string, $removeEmojis = false, $removeBannedWords = true)
    {
        if($removeBannedWords)
            $string = self::bannedWords($string);

        $string = htmlspecialchars($string, ENT_QUOTES);

        if($removeEmojis)
            $string = self::removeEmojis($string);

        return $string;
    }

    /**
     * Remove all emojis from the given string
     * @param  string $string The string to be parsed
     * @return string The string withour emojis
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
     * Validate $integer to be a integer
     * @param  integer $integer The string to beundefinedparsed
     * @return integer the integer validated
     */
    static public function int($integer)
    {
        if(!ctype_digit(strval($integer)))
           return 0;

        return intval($integer);
    }

    /**
     * Validate $boolean to be a boolean. return the value of the boolean if true, false otherwise.
     * @param  boolean $bool The string to be parsed
     * @return boolean Interpreted value
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

    /**
     * Convert a boolean to an integer
     * @param  boolean $bool Value to convert
     * @return integer Equivalent int
     */
    static public function booleanToInt($bool)
    {
        if(self::boolean($bool))
            return 1;

        return 0;
    }

    /**
     * Format the given string to an acceptable string for profiles
     * @param  string $pName ProfileName
     * @return string ProfileName parsed
     */
    static public function profileName($pName)
    {
        $name = self::bannedWords($pName);

        //replace accented characters
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        //Remove unwanted characters
        $name = preg_replace('/[\$+!*\'(),\{\}\|\\\^~[\]`<>\#%";\/\?:@&=\s+]/', '', $name);

        $name = filter_var($name, FILTER_SANITIZE_URL);

        //make sure the string is not too long
        $name = substr($name, 0, 30);

        return strtolower($name);
    }

    /**
     * Format the given string to an acceptable string for user
     * @param  text    $uName user_name
     * @return boolean User name parsed
     */
    public static function userName($uName)
    {
        $name = self::bannedWords($uName);

        //replace punctuation characters
        $name = preg_replace("#[[:punct:]]#", "", $name);

        //sanitize
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $name = substr($name, 0, 30);

        if(strlen($name) == 0) return false;

        return $name;
    }

    /**
     * Format the given email to an acceptable email for user
     * @param  text    $uEmail user_email
     * @return mixed The email parse, or false if the amil is invalid
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

    /**
     * Remove banned words from the given string
     * @param  string $string String to parse
     * @return string The given string but without any banned words
     */
    public static function bannedWords($string)
    {
        $banned = Response::read("Ban", "get", "word")['data']['words'];

        $cleanText = str_ireplace($banned, "", $string);

        return $cleanText;
    }
}
