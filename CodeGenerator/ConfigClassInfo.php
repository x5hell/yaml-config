<?php

namespace YamlConfig\CodeGenerator;

/** Информация о классе конфига */
class ConfigClassInfo
{
    /** @var string пространство имён класса */
    protected $namespace;

    /** @var string[] список подключаемых классов */
    protected $useClasses;

    /** @var string название класса */
    protected $className;

    /** @var string комментарий класса */
    protected $classComment;

    /** @var ClassProperty[] список свойств класса */
    protected $classPropertyList;

    /**
     * @return string пространство имён класса
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace пространство имён класса
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string[] список подключаемых классов
     */
    public function getUseStructures()
    {
        return $this->useClasses;
    }

    /**
     * @param string[] $useClasses список подключаемых классов
     */
    public function setUseStructures($useClasses)
    {
        $this->useClasses = $useClasses;
    }

    /**
     * @return string название класса
     */
    public function getName()
    {
        return $this->className;
    }

    /**
     * @param string $className название класса
     */
    public function setName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string комментарий класса
     */
    public function getComment()
    {
        return $this->classComment;
    }

    /**
     * @param string $classComment комментарий класса
     */
    public function setComment($classComment)
    {
        $this->classComment = $classComment;
    }

    /**
     * @return ClassProperty[] список свойств класса
     */
    public function getPropertyList()
    {
        return $this->classPropertyList;
    }

    /**
     * @param ClassProperty[] $classPropertyList список свойств класса
     */
    public function setPropertyList($classPropertyList)
    {
        $this->classPropertyList = $classPropertyList;
    }
}