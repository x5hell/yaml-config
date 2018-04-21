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
    public function getUseClasses()
    {
        return $this->useClasses;
    }

    /**
     * @param string[] $useClasses список подключаемых классов
     */
    public function setUseClasses($useClasses)
    {
        $this->useClasses = $useClasses;
    }

    /**
     * @return string название класса
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className название класса
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string комментарий класса
     */
    public function getClassComment()
    {
        return $this->classComment;
    }

    /**
     * @param string $classComment комментарий класса
     */
    public function setClassComment($classComment)
    {
        $this->classComment = $classComment;
    }

    /**
     * @return ClassProperty[] список свойств класса
     */
    public function getClassPropertyList()
    {
        return $this->classPropertyList;
    }

    /**
     * @param ClassProperty[] $classPropertyList список свойств класса
     */
    public function setClassPropertyList($classPropertyList)
    {
        $this->classPropertyList = $classPropertyList;
    }
}