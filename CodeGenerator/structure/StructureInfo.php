<?php

namespace YamlConfig\StructureCodeGenerator;

/** Информация о структуре конфига */
abstract class StructureInfo implements StructureInfoInterface
{
    /** @var string пространство имён структуры */
    protected $namespace;

    /** @var string[] список подключаемых классов */
    protected $useClasses;

    /** @var string название структуры */
    protected $name;

    /** @var string комментарий структуры */
    protected $comment;

    /** @var StructurePropertyInterface[] список свойств структуры */
    protected $propertyList;

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getUseClasses()
    {
        return $this->useClasses;
    }

    public function setUseStructures($useClasses)
    {
        $this->useClasses = $useClasses;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
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
        return $this->propertyList;
    }

    public function setPropertyList($propertyList)
    {
        $this->propertyList = $propertyList;
    }
}