<?php

namespace YamlConfig\StructureCodeGenerator;

/** Генератор структуры конфига */
interface ConfigStructureGeneratorInterface
{

    /**
     * @param ConfigStructureInfoInterface $structureInfo информация о структуре конфига
     * @return $this
     */
    public function setStructureInfo($structureInfo);

    /**
     * @return string содержимое сгенеритованной структуры
     */
    public function generateStructureContent();
}
