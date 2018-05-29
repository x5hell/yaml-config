<?php

namespace YamlConfig\StructureCodeGenerator;

/** Свойство структуры */
interface StructurePropertyInterface
{
    /**
     * @return string имя свойства
     */
    public function getName();

    /**
     * @param string $name имя свойства
     */
    public function setName($name);

    /**
     * @return string значение свойства
     */
    public function getValue();

    /**
     * @param string $value значение свойства
     */
    public function setValue($value);

    /**
     * @return string тип свойства
     */
    public function getType();

    /**
     * @param string $type тип свойства
     */
    public function setType($type);

    /**
     * @return string комментарий свойства
     */
    public function getComment();

    /**
     * @param string $comment комментарий свойства
     */
    public function setComment($comment);

    /**
     * @return bool true - свойство является структурой
     */
    public function isStructure();

    /**
     * @param bool $isStructure true - свойство является структурой
     */
    public function setIsStructure($isStructure);
}