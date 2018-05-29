<?php

namespace YamlConfig\ClassCodeGenerator;

use YamlConfig\StructureCodeGenerator\ConfigStructureTreeGenerator;


/** Генератор структуры классов конфига */
class ConfigClassTreeGenerator extends ConfigStructureTreeGenerator
{

    protected function createConfigStructureGenerator($configStructureInfo)
    {
        $configClassGenerator = new ConfigClassGenerator();
        return $configClassGenerator->setStructureInfo($configStructureInfo);
    }
}
