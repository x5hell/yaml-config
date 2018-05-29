<?php

namespace YamlConfig\Structure;

/** Информация о структуре конфига */
interface ConfigStructureInfoInterface
{
    /**
     * @return string пространство имён структуры конфига
     */
    public function getNamespace();

    /**
     * @param string $namespace пространство имён структуры конфига
     */
    public function setNamespace($namespace);

    /**
     * @return string[] список подключаемых классов
     */
    public function getUseClasses();

    /**
     * @param string[] $useStructures список подключаемых структур
     */
    public function setUseClasses($useStructures);

    /**
     * @return string название структуры конфига
     */
    public function getName();

    /**
     * @param string $className название структуры конфига
     */
    public function setName($className);

    /**
     * @return string комментарий структуры конфига
     */
    public function getComment();

    /**
     * @param string $comment комментарий структуры конфига
     */
    public function setComment($comment);

    /**
     * @return StructurePropertyInterface[] список свойств
     */
    public function getPropertyList();

    /**
     * @param StructurePropertyInterface[] $classPropertyList список свойств
     */
    public function setPropertyList($classPropertyList);
}