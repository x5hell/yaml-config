<?php

namespace YamlConfig\CodeGenerator;

/** Свойство класса */
class ClassProperty
{
    /** @var string имя свойства */
    protected $name;

    /** @var string значение свойства */
    protected $value;

    /** @var string тип свойства */
    protected $type;

    /** @var string комментарий свойства */
    protected $comment;

    /** @var boolean true - свойство является классом */
    protected $isClass;

    /**
     * @return string имя свойства
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name имя свойства
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string значение свойства
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value значение свойства
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string тип свойства
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type тип свойства
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string комментарий свойства
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment комментарий свойства
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return bool true - свойство является классом
     */
    public function isStructure()
    {
        return $this->isClass;
    }

    /**
     * @param bool $isClass true - свойство является классом
     */
    public function setIsStructure($isClass)
    {
        $this->isClass = $isClass;
    }
}