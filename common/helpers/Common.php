<?php

namespace common\helpers;

use Yii;
use DateTime;

/**
 * Общий вспомогательный класс
 * 
 */
class Common
{

    /**
     * Метод для поиска значений в многомерных массивах
     * Применение
     * $b = array(array("Mac", "NT"), array("Irix", "Linux")); 
     * echo in_array_r("Irix", $b) ? 'found' : 'not found'; 
     */
    public function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || 
                (is_array($item) && in_array_r($needle, $item, $strict))) { 
                    return true; 
            } 
        } 
        
        return false; 
    }

    /**
     *  удаления папок и файлов рекурсивно
     *  Лучше использовать FileHelper::removeDirectory('/path/to/dir');
     */
    public static function rRmDir($dir)
    {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir") self::rRmDir($dir."/".$object); else unlink($dir."/".$object);
           }
         }
         reset($objects);
         rmdir($dir);
       }
    }

    /**
     *  Проставление прав рекурсивно
     */
    public static function rChmodDir($dir)
    {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir") self::rChmodDir($dir."/".$object); else chmod($dir."/".$object, 0777);
           }
         }
         reset($objects);
         chmod($dir, 0777);
       }
    }


    /**
     * Устанавливаем пользователя
     * который является владельцем текущего скрипта
     * Этот метод скорей всего не отработает и вернёт
     * CHOWN(): OPERATION NOT PERMITTED
     */
    public static function rChangeUserDir($dir)
    {

        if (is_dir($dir)) {

            $objects = scandir($dir);

            foreach ($objects as $object) {

                if ($object != "." && $object != "..") {

                    chown($dir."/".$object, get_current_user());

                    if (filetype($dir."/".$object) == "dir") {
                        self::rChangeUserDir($dir."/".$object);
                    }
                    
                }

            }

            //Устанавливаем указатель массива на его первый элемент
            reset($objects);
            chown($dir, get_current_user());
        }
        
    }

    /**
     * Выводит размер файла в нормальном виде
     *
     */
    public static function filesize($file, $decimals = 2)
    {

        if (!file_exists($file)) return false;

        $bytes = filesize($file);

        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor > 0) $sz = 'KMGT';
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
    }

    /**
     * Получаем тип файла
     * 
     * @param string $file_name
     */
    public static function fileType(string $file_name)
    {

        $type = 'image';

        //Audio
        if (preg_match("/\.(?:mp3|wav|og(?:g|a)|flac|midi?|rm|aac|wma|mka|ape)$/i", $file_name)) {
            $type = 'audio';
        }

        //Video
        if (preg_match("/\.(?:mpeg|ra?m|avi|mp(?:g|e|4)|mov|divx|asf|qt|wmv|m\dv|rv|vob|asx|ogm)$/i", $file_name)) {
            $type = 'video';
        }

        //Text
        if (preg_match("/\.(?:txt)$/i", $file_name)) {
            $type = 'text';
        }

        //Html
        if (preg_match("/\.(?:html|xhtml|htm|xml)$/i", $file_name)) {
            $type = 'html';
        }
        
        //pdf
        if (preg_match("/\.(?:pdf)$/i", $file_name)) {
            $type = 'pdf';
        }

        //office
        if (preg_match("/\.(?:doc|docm|docx|dot|dotm|dotx|odt|xlsx|xls|csv)$/i", $file_name)) {
            $type = 'text';
        }

        //gdocs
        if (preg_match("/\.(?:word|excel|powerpoint|office|iwork-pages|tiff?|rtf|pptx?|pps|potx?|ods|odt|pages|ai|dxf|ttf|tiff?|wmf)$/i", $file_name)) {
            $type = 'gdocs';
        }

        //Остальные форматы - flash//object//generic//office//gdocs//other//other

        return $type;

    }


    public static function checkUtf8($charset): bool
    {
        if (strtolower($charset) != "utf-8") {
            return false;
        }

        return true;
    }

    /**
     * @param $in_charset
     * @param $str
     * @return false|string
     */
    public function convertToutf8($in_charset, $str)
    {
        return iconv(strtolower($in_charset), "utf-8", $str);
    }


    public static function rusTranslit(string $string): string
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',  'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            ' ' => '',    '  ' => '',   '.' => "",
            '!' => '',    '+' =>  '',   '(' => "",
            ')' => "",    '\'' => "",   '/' => '',
            '\\' => '',   '"' => "",    '%' => "",
            '*' => "",    '&' => '',    '<' => '',
            '>' => "",    '?'=>"",      '@' => "",
            '$' => '',    '^' => '',    '~' => "",
            ','=>"",      '{' => "",    '}' => '',
            '[' => '',    ']' => "",    '#' => "",
            '№' => "",    ':' => '',    ';' => '',
            '=' => '_',   ' ' => '_',   "'" => '',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );

        // название файлов не больше 32-х
        return substr(strtolower(strtr($string, $converter)), 0, 32);
    }

}