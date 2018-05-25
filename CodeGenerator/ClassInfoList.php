<?php

namespace YamlConfig\CodeGenerator;

use YamlConfig\Helper\ArrayHelper;
use YamlConfig\YamlCommentsParser;

/** Список информации о классах */
class ClassInfoList
{
    /** @var ConfigClassInfo[] список информации о классах */
    protected $classInfoList;

    /** @var string полный путь к конфигу  */
    protected $configFullPath;

    /** @var string пространство имён конфига */
    protected $configNamespace;

    /** @var string[] комментарии к узлам конфига */
    protected $configNodeComments;

    /** @var string содержимое конфигурации yaml */
    protected $yamlConfigContent;

    /**
     * @return ConfigClassInfo[]
     */
    public function getClassInfoList()
    {
        return $this->classInfoList;
    }

    /**
     * @param ConfigClassInfo[] $classInfoList
     */
    protected function setClassInfoList($classInfoList)
    {
        $this->classInfoList = $classInfoList;
    }


    /**
     * @return string полный путь к конфигу
     */
    protected function getConfigFullPath()
    {
        return $this->configFullPath;
    }

    /**
     * @param string $configFullPath полный путь к конфигу
     */
    public function setConfigFullPath($configFullPath)
    {
        $this->configFullPath = $configFullPath;
    }

    /**
     * @return string пространство имён конфига
     */
    public function getConfigNamespace()
    {
        return $this->configNamespace;
    }

    /**
     * @param string $configNamespace пространство имён конфига
     */
    public function setConfigNamespace($configNamespace)
    {
        $this->configNamespace = $configNamespace;
    }
    
    /**
     * 
     * @return ConfigClassInfo
     */
    public function createConfigClassInfo()
    {
        return new ConfigClassInfo();
    }
    
    /**
     * 
     * @return ClassProperty
     */
    public function createClassProperty()
    {
        return new ClassProperty();
    }

    /**
     * @return string содержимое конфига yaml
     */
    protected function getYamlConfigContent()
    {
        if(is_null($this->yamlConfigContent)){
            $this->yamlConfigContent = file_get_contents(
                $this->getConfigFullPath()
            );
        }
        return $this->yamlConfigContent;
    }

    /**
     * @param ConfigClassInfo $classInfo добавление информации о классе в список
     */
    protected function addClassInfo(ConfigClassInfo $classInfo)
    {
        $this->classInfoList[] = $classInfo;
    }

    /**
     * @param array $tree дерево конфига
     * @param string[] $path текущий путь
     * @return ConfigClassInfo[] список информации о классах
     */
    public function initFromTree($tree, $path = [])
    {
        $this->setClassInfoList([]);
        foreach ($tree as $nodeName => $node){
            if($this->isClassNode($node)){
                $classInfo = $this->getConfigClassInfo(
                    $node, [$nodeName], ucfirst($nodeName)
                );
                $this->addClassInfo($classInfo);
            }
        }
        return $this->classInfoList;
    }

    /** Проверка является ли узел классом
     * @param mixed $node узел дерева конфига
     * @return boolean true - является */
    protected function isClassNode($node)
    {
        return
            is_array($node)
            &&
            ArrayHelper::isList($node) === false
            &&
            ArrayHelper::isDateList($node) === false;
    }

    /**
     * @param array $classNode узел класса
     * @param string[] $path путь к классу
     * @param string $className имя класса
     * @return ConfigClassInfo информация о классе
     */
    protected function getConfigClassInfo($classNode, $path, $className)
    {
        $configClassInfo = $this->createConfigClassInfo();
        $namespace = $this->getConfigNamespace();
        $namespacePath = array_slice($path, 0, -1);
        $nodePath = array_slice($path, 1);
        foreach ($namespacePath as $pathPart){
            $namespace .= '\\'. $this->fixClassName($pathPart);
        }
        $configClassInfo->setNamespace($namespace);
        $configClassInfo->setClassComment(
            $this->getCommentByPath($nodePath)
        );
        $configClassInfo->setClassName($className);
        $useClassList = [];
        $classPropertyList = [];
        foreach ($classNode as $subNodeName => $subNode){
            $classProperty = $this->getClassProperty($nodePath, $subNodeName, $subNode);
            if ($classProperty->isClass()){
                $subClassName = $this->fixClassName($subNodeName);
                $useClassList[] = implode(
                    '\\',
                    [$namespace, $className, $subClassName]
                );
                $classInfo = $this->getConfigClassInfo(
                    $subNode,
                    array_merge($path, [$subNodeName]),
                    $subClassName
                );
                $this->addClassInfo($classInfo);
            }
            $classPropertyList[] = $classProperty;
        }
        $configClassInfo->setUseClasses($useClassList);
        $configClassInfo->setClassPropertyList($classPropertyList);
        return $configClassInfo;
    }
    
    /** Исправить имя класса
     * @param string $className имя класса
     * @return string исправленное имя класса */
    protected function fixClassName($className)
    {
        return $this->fixPropertyName(ucfirst($className));
    }

    /** Исправить имя свойства
     * @param string $propertyName имя свойства
     * @return string исправленное имя свойства
     */
    protected function fixPropertyName($propertyName)
    {
        return preg_match('/^\d/', $propertyName)
            ? '_'. $propertyName
            : $propertyName;
    }

    /**
     * @param string[] $classPath путь к классу
     * @param string $propertyName имя свойства
     * @param mixed $propertyValue значение свойства
     * @return ClassProperty
     */
    protected function getClassProperty($classPath, $propertyName, $propertyValue)
    {
        $classProperty = $this->createClassProperty();
        $classProperty->setName($this->fixPropertyName($propertyName));
        if ($this->isClassNode($propertyValue)) {
            $subClassName = $this->fixClassName($propertyName);
            $classProperty->setType($subClassName);
            $classProperty->setIsClass(true);
        } else {
            $classProperty->setValue($propertyValue);
            $classProperty->setType(gettype($propertyValue));
            $classProperty->setIsClass(false);
        }
        $classProperty->setComment(
            $this->getCommentByPath(array_merge($classPath, [$propertyName]))
        );
        return $classProperty;
    }

    /**
     * @param array $pathPartList список частей пути к узлу дерева
     * @param string $pathSeparator
     * @return string текст комментария
     */
    protected function getCommentByPath(array $pathPartList, $pathSeparator = '.')
    {
        $path = implode($pathSeparator, $pathPartList);
        $configNodeComments = $this->getConfigNodeComments();
        if(array_key_exists($path, $configNodeComments)){
            return $configNodeComments[$path];
        }
    }

    /**
     * @return string[] комментарии к узлам конфига
     */
    protected function getConfigNodeComments()
    {
        if(is_null($this->configNodeComments)){
            $this->configNodeComments = YamlCommentsParser::parse(
                $this->getYamlConfigContent()
            );
        }

        return $this->configNodeComments;
    }
}
