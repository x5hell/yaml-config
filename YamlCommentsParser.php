<?php

namespace YamlConfig;

/** Парсер yaml-комментариев */
class YamlCommentsParser
{

    /**
     * @param string $yaml текст в формате Yaml
     * @return array комментарии yaml элементов
     */
    public static function parse($yaml)
    {
        $yamlParts = self::splitYaml($yaml);
        $yamlComments = [];

        self::fillYamlComments($yamlParts, $yamlComments);

        return $yamlComments;
    }

    /** Создание ассоциативного массива Yaml-комментариев
     * @param string[] $yamlParts список фрагментов yaml
     * @param string[] $yamlComments комментарии yaml элементов
     * @param string[] $yamlPath текущий путь в разбираемой yaml-структур
     */
    protected static function fillYamlComments(
        $yamlParts, & $yamlComments, $yamlPath = []
    )
    {
        if(count($yamlParts) > 0)
        {
            $yamlPart = array_shift($yamlParts);
            $yamlPartInfo = self::getYamlPartInfo($yamlPart);
            $actualYamlPath = self::getActualYamlPath($yamlPath, $yamlPartInfo);
            self::fillYamlComment($yamlPartInfo, $yamlComments, $actualYamlPath);
            self::fillYamlComments($yamlParts, $yamlComments, $actualYamlPath);
        }
    }

    /**
     * @param YamlPartInfo $yamlPartInfo информация о yaml фрагменте
     * @param string[] $yamlComments комментарии yaml элементов
     * @param string[] $yamlPathInfo текущий путь в yaml структуре
     */
    protected static function fillYamlComment($yamlPartInfo, &$yamlComments, $yamlPathInfo)
    {
        $comment = $yamlPartInfo->getComment();
        if(isset($comment)){
            $path = implode('.', $yamlPathInfo);
            $yamlComments[$path] = $comment;
        }
    }


    /**
     * @param string[] $previousYamlPath предыдущий путь в yaml-структуре
     * @param YamlPartInfo $yamlPartInfo информация о yaml фрагменте
     * @return string[] текущий путь в yaml структуре
     */
    protected static function getActualYamlPath($previousYamlPath, $yamlPartInfo)
    {
        $previousLevel = count($previousYamlPath);
        if(is_null($yamlPartInfo->getName())){
            return $previousYamlPath;
        } else if($yamlPartInfo->getLevel() > $previousLevel){

            $currentNodeName = $yamlPartInfo->getName() !== '-'
                ? $yamlPartInfo->getName()
                : 0;
            $actualYamlPath = array_slice($previousYamlPath, 0, $previousLevel);
            array_push($actualYamlPath, $currentNodeName);
        } else {
            $actualYamlPath = array_slice(
                $previousYamlPath,
                0,
                $yamlPartInfo->getLevel()
            );

            $previousNodeName = array_pop($actualYamlPath);

            $currentNodeName = $yamlPartInfo->getName() !== '-'
                ? $yamlPartInfo->getName()
                : (int)$previousNodeName + 1;
            array_push($actualYamlPath, $currentNodeName);
        }
        
        return $actualYamlPath;
    }

    /**
     * @param string $yamlPart yaml фрагмент
     * @return YamlPartInfo информация о yaml фрагменте
     */
    protected static function getYamlPartInfo($yamlPart)
    {
        $yamlPartInfo = new YamlPartInfo();
        if(
            preg_match(
                '/^(\s*)([^\:]+)\:/',
                $yamlPart,
                $match
            )
            ||
            preg_match(
                '/^(\s+)(\-)/',
                $yamlPart,
                $match
            )
        ){
            $yamlPartInfo->setLevel(strlen($match[1])/2 + 1);
            $yamlPartInfo->setName($match[2]);
            if(preg_match('/.*\#(.*)/', $yamlPart,$match)){
                $yamlPartInfo->setComment($match[1]);
            }
        }
        return $yamlPartInfo;
    }

    /**
     * @param string $yaml текст в формате Yaml
     * @return string[] получение частей yaml текста
     */
    protected static function splitYaml($yaml)
    {
        $yamlLines = explode("\n", $yaml);

        return self::yamlLines2Parts($yamlLines);
    }

    /** Объединение yaml-строк в yaml-части
     * @param string[] $yamlLines yaml-строки
     * @return string[] yaml-части
     */
    protected static function yamlLines2Parts($yamlLines)
    {
        $yamlParts = [];
        $multiLinePart = false;
        $multiLineLevel = 0;
        $yamlPart = '';
        foreach ($yamlLines as $yamlLine){
            $yamlPartInfo = self::getYamlPartInfo($yamlLine);
            if(
                $multiLinePart === false
                &&
                preg_match(
                    '/^\s*[^\:]+\:\s*(\>|\|)/',
                    $yamlLine
                )
            ){
                $multiLineLevel = $yamlPartInfo->getLevel();
                $multiLinePart = true;
                $yamlPart = $yamlLine;
            } else if ($multiLinePart === false){
                $yamlParts[] = $yamlLine;
            } else if(
                $multiLinePart
            ){
                $yamlPart .= "\n". $yamlLine;
                if($yamlPartInfo->getLevel() < $multiLineLevel){
                    $yamlParts[] = $yamlPart;
                    $multiLinePart = false;
                }
            }
        }
        return $yamlParts;
    }
}