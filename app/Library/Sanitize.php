<?php
class Sanitize
{
    /**
     * Replace all 'dangerous' html characters with their & counterparts
     *
     * @param $string The string to be parsed, passed by reference.
     * @param $removeEmoji Shall we remove emoji as well?
     */
    static public function string(&$string, $removeEmoji = false)
    {
        $string = htmlspecialchars($string, ENT_QUOTES);

        if($removeEmoji)
    }

    /**
     * Remove all emojis from the given string
     *
     * @param $string The string to be parsed, passed by reference
     */
    static public function removeEmojis(&$string)
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

        $string = $cleanString;
    }
}

