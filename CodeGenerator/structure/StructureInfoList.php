<?php

namespace YamlConfig\Structure;

use YamlConfig\Helper\ArrayHelper;
use YamlConfig\YamlCommentsParser;

/** Список информации о структурах */
class ClassInfoList implements StructureInfoListInterface
{
    /** @var ConfigStructureInfoInterface[] список информации о структуре конфига */
    protected $structureInfoList;

    /** @var string полный путь к конфигу  */
    protected $configFullPath;

    /** @var string пространство имён конфига */
    protected $configNamespace;

    /** @var string[] комментарии к узлам конфига */
    protected $configNodeComments;

    /** @var string содержимое конфигурации yaml */
    protected $yamlConfigContent;

    public function getStructureInfoList()
    {
        return $this->structureInfoList;
    }

    /**
     * @param ConfigStructureInfoInterface[] $structureInfoList список информации о структуре конфига
     */
    protected function setStructureInfoList($structureInfoList)
    {
        $this->structureInfoList = $structureInfoList;
    }

    /**
     * @return string полный путь к конфигу
     */
    protected function getConfigFullPath()
    {
        return $this->configFullPath;
    }

    public function setConfigFullPath($configFullPath)
    {
        $this->configFullPath = $configFullPath;
    }

    /**
     * @return string пространство имён конфига
     */
    protected function getConfigNamespace()
    {
        return $this->configNamespace;
    }

    public function setConfigNamespace($configNamespace)
    {
        $this->configNamespace = $configNamespace;
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
     * @param ConfigStructureInfoInterface $configStructureInfo добавление информации о структуре в список
     */
    protected function addStructureInfo(ConfigStructureInfoInterface $configStructureInfo)
    {
        $this->structureInfoList[] = $configStructureInfo;
    }

    public function initFromTree($tree, $path = [])
    {
        $this->setStructureInfoList([]);
        foreach ($tree as $nodeName => $node){
            if($this->iStructureNode($node)){
                $classInfo = $this->getConfigStructureInfo(
                    $node, [$nodeName], ucfirst($nodeName)
                );
                $this->addStructureInfo($classInfo);
            }
        }
        return $this->structureInfoList;
    }

    /** Проверка является ли узел структурой
     * @param mixed $node узел дерева конфига
     * @return boolean true - является */
    protected function iStructureNode($node)
    {
        return
            is_array($node)
            &&
            ArrayHelper::isList($node) === false
            &&
            ArrayHelper::isDateList($node) === false;
    }

    /**
     * @param array $structureNode узел структуры
     * @param string[] $path путь к структуре
     * @param string $structureName имя структуры
     * @return ConfigStructureInfoInterface информация о структуре
     */
    protected function getConfigStructureInfo($structureNode, $path, $structureName)
    {
        $configStructureInfo = $this->createConfigStructureInfo();
        $namespace = $this->getConfigNamespace();
        $namespacePath = array_slice($path, 0, -1);
        $nodePath = array_slice($path, 1);
        foreach ($namespacePath as $pathPart){
            $namespace .= '\\'. $this->fixStructureName($pathPart);
        }
        $configStructureInfo->setNamespace($namespace);
        $configStructureInfo->setComment(
            $this->getCommentByPath($nodePath)
        );
        $configStructureInfo->setName($structureName);
        $useClassList = [];
        $structurePropertyList = [];
        foreach ($structureNode as $subNodeName => $subNode){
            $structureProperty = $this->getStructureProperty($nodePath, $subNodeName, $subNode);
            if ($structureProperty->isStructure()){
                $subStructureName = $this->fixStructureName($subNodeName);
                $useClassList[] = implode(
                    '\\',
                    [$namespace, $structureName, $subStructureName]
                );
                $structureInfo = $this->getConfigStructureInfo(
                    $subNode,
                    array_merge($path, [$subNodeName]),
                    $subStructureName
                );
                $this->addStructureInfo($structureInfo);
            }
            $structurePropertyList[] = $structureProperty;
        }
        $configStructureInfo->setUseStructures($useClassList);
        $configStructureInfo->setPropertyList($structurePropertyList);
        return $configStructureInfo;
    }
    
    /** Исправить имя структуры
     * @param string $structureName имя структуры
     * @return string исправленное имя структуры */
    protected function fixStructureName($structureName)
    {
        return $this->fixPropertyName(ucfirst($structureName));
    }

    /** Исправить имя свойства
     * @param string $propertyName имя свойства
     * @return string исправленное имя свойства */
    protected function fixPropertyName($propertyName)
    {
        return preg_match('/^\d/', $propertyName)
            ? '_'. $propertyName
            : $propertyName;
    }

    /**
     * @param string[] $classPath путь к структуре
     * @param string $propertyName имя свойства
     * @param mixed $propertyValue значение свойства
     * @return StructurePropertyInterface
     */
    protected function getStructureProperty($classPath, $propertyName, $propertyValue)
    {
        $classProperty = $this->createStructureProperty();
        $classProperty->setName($this->fixPropertyName($propertyName));
        if ($this->iStructureNode($propertyValue)) {
            $subClassName = $this->fixStructureName($propertyName);
            $classProperty->setType($subClassName);
            $classProperty->setIsStructure(true);
        } else {
            $classProperty->setValue($propertyValue);
            $classProperty->setType(gettype($propertyValue));
            $classProperty->setIsStructure(false);
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
