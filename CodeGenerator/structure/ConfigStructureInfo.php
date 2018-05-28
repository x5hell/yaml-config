<?php

namespace YamlConfig\Structure;

/** Информация о структуре конфига */
abstract class ConfigStructureInfo implements ConfigStructureInfoInterface
{
    /** @var string пространство имён структуры конфига */
    protected $namespace;

    /** @var string[] список подключаемых структур */
    protected $useStructures;

    /** @var string название структуры конфига */
    protected $className;

    /** @var string комментарий структуры конфига */
    protected $comment;

    /** @var StructurePropertyInterface[] список свойств */
    protected $classPropertyList;

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getUseStructures()
    {
        return $this->useStructures;
    }

    public function setUseStructures($useStructures)
    {
        $this->useStructures = $useStructures;
    }

    public function getName()
    {
        return $this->className;
    }

    public function setName($className)
    {
        $this->className = $className;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getPropertyList()
    {
        return $this->classPropertyList;
    }

    public function setPropertyList($classPropertyList)
    {
        $this->classPropertyList = $classPropertyList;
    }
}