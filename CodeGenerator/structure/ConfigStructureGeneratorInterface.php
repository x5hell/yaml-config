<?php

namespace YamlConfig\Structure;

/** Генератор структуры конфига */
interface ConfigStructureGeneratorInterface
{

    /**
     * @param ConfigStructureInfoInterface $structureInfo информация о структуре конфига
     */
    public function setStructureInfo($structureInfo);

    /**
     * @return string путь к папке с шаблонами
     */
    public function getTemplateDirectoryPath();

    /**
     * @return string содержимое сгенеритованной структуры
     */
    public function generateStructureContent();
}
