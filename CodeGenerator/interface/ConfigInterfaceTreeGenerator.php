<?php

namespace YamlConfig\InterfaceCodeGenerator;

use YamlConfig\ClassCodeGenerator\ConfigInterfaceGenerator;
use YamlConfig\StructureCodeGenerator\ConfigStructureTreeGenerator;


/** Генератор структуры интерфейсов конфига */
class ConfigInterfaceTreeGenerator extends ConfigStructureTreeGenerator
{

    protected function createConfigStructureGenerator($configStructureInfo)
    {
        $configClassGenerator = new ConfigInterfaceGenerator();
        return $configClassGenerator->setStructureInfo($configStructureInfo);
    }
}
