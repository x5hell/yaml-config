<?php

namespace YamlConfig\StructureCodeGenerator;

/** Информация о структуре конфига */
interface StructureInfoInterface
{

    /**
     * @return string пространство имён структуры
     */
    public function getNamespace();

    /**
     * @param string $namespace пространство имён структуры
     */
    public function setNamespace($namespace);

    /**
     * @return string[] список подключаемых классов
     */
    public function getUseClasses();

    /**
     * @param string[] $useClasses список подключаемых классов
     */
    public function setUseStructures($useClasses);

    /**
     * @return string название структуры
     */
    public function getName();

    /**
     * @param string $name название структуры
     */
    public function setName($name);

    /**
     * @return string комментарий структуры
     */
    public function getComment();

    /**
     * @param string $comment комментарий структуры
     */
    public function setComment($comment);

    /**
     * @return StructurePropertyInterface[] список свойств структуры
     */
    public function getPropertyList();

    /**
     * @param StructurePropertyInterface[] $propertyList список свойств структуры
     */
    public function setPropertyList($propertyList);
}