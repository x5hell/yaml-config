<?php

namespace YamlConfig\Structure;

/** Свойство структуры */
abstract class StructureProperty implements StructurePropertyInterface
{
    /** @var string имя свойства */
    protected $name;

    /** @var string значение свойства */
    protected $value;

    /** @var string тип свойства */
    protected $type;

    /** @var string комментарий свойства */
    protected $comment;

    /** @var boolean true - свойство является структурой */
    protected $isStructure;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function isStructure()
    {
        return $this->isStructure;
    }

    public function setIsStructure($isStructure)
    {
        $this->isStructure = $isStructure;
    }
}