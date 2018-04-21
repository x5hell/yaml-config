<?php

namespace Helper;


class ArrayHelper
{
    /** Преобразовать иерархический массив в плоский
     * @param array $tree иерархический массив
     * @param string $separator разделитель узлов
     * @return array плоский массив */
    public static function tree2flat($tree, $separator = '.'){
        $flat = [];
        $matrix = self::tree2matrix($tree);
        foreach ($matrix as $matrixElement){
            $value = array_pop($matrixElement);
            $path = implode($separator, $matrixElement);
            $flat[$path] = $value;
        }
        return $flat;
    }

    /** Преобразовать многомерный массива в матрицу
     * @param array $tree многомерный массив
     * @param string[] $treePath путь к массиву
     * @return array матрица */
    public static function tree2matrix(
        $tree, $treePath = []
    ){
        $matrix = [];
        foreach($tree as $nodeName => $node){
            if(is_array($node)){
                $list = self::tree2matrix(
                    $node,
                    array_merge($treePath, [$nodeName])
                );
                foreach ($list as $element) {
                    $matrix[] = $element;
                }
            } else {
                $matrix[] = array_merge($treePath, [$nodeName, $node]);
            }
        }
        return $matrix;
    }

    /**
     * @param array $array массив
     * @return bool true - если массив является списком
     */
    public static function isList(array $array)
    {
        $isEmptyArray = array() === $array;
        $listKeys = range(0, count($array) - 1);
        return $isEmptyArray ||  array_keys($array) ===  $listKeys;
    }

    /**
     * @param array $array массив
     * @return bool true - если ассоциативный массив с ключами-датами
     */
    public static function isDateList(array $array)
    {
        foreach($array as $key => $value){
            if(
                preg_match(
                    '/^(\d{4})\-(\d{1,2})\-(\d{1,2})$/',
                    $key,
                    $date
                ) === 0
                ||
                checkdate(
                    (int) $date[2],
                    (int) $date[3],
                    (int) $date[1]
                ) === false
            ){
                return false;
            }
        }

        return true;
    }
}