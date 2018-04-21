<?php

namespace Helper;


class StringHelper
{
    /**
     * @param string $text текст
     * @return string текст с заглавной первой буквой
     */
    public static function upperCaseFirstLetter($text)
    {
        $encoding = mb_internal_encoding();
        mb_internal_encoding("UTF-8");
        $result =
            mb_strtoupper(
                mb_substr($text, 0, 1)
            ).
            mb_substr($text, 1);
        mb_internal_encoding($encoding);
        return $result;
    }

    /** Замена в строке шаблонов на значения
     * @param string $string стоока с шаблонами
     * @param array $replace ассоциативный массив вида: шаблон => значение
     * @return string строка с значениями */
    public static function replacePatterns($string, array $replace)
    {
        return str_replace(
            array_keys($replace),
            array_values($replace),
            $string
        );
    }
}