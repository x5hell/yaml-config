<?php

namespace YamlConfig\Structure;

/** Список информации о структурах */
interface StructureInfoListInterface
{
    /**
     * @return ConfigStructureInfoInterface[] список информации о структуре конфига
     */
    public function getStructureInfoList();

    /**
     * @param string $configFullPath полный путь к конфигу
     */
    public function setConfigFullPath($configFullPath);

    /**
     * @param string $configNamespace пространство имён конфига
     */
    public function setConfigNamespace($configNamespace);

    /**
     * @return StructureInfoInterface информация о структуре конфига
     */
    public function createConfigStructureInfo();

    /**
     * @return StructureProperty
     */
    public function createStructureProperty();

    /**
     * @param array $tree дерево конфига
     * @param string[] $path текущий путь
     * @return ConfigStructureInfoInterface[] список информации о структуре
     */
    public function initFromTree($tree, $path = []);

}
