<?php

namespace YamlConfig\StructureCodeGenerator;

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
     * @param array $tree дерево конфига
     * @param string[] $path текущий путь
     * @return ConfigStructureInfoInterface[] список информации о структуре
     */
    public function initFromTree($tree, $path = []);

}
