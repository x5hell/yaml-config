<?php

namespace YamlConfig\ClassCodeGenerator;

use Slov\Helper\StringHelper;
use YamlConfig\StructureCodeGenerator\ConfigStructureGenerator;
use YamlConfig\StructureCodeGenerator\StructurePropertyInterface;

/** Генератор класса интерфейса */
class ConfigInterfaceGenerator extends ConfigStructureGenerator
{

    protected function getTemplateDirectoryPath()
    {
        return __DIR__.
            DIRECTORY_SEPARATOR.
            'Templates';
    }

    /** @return string шаблон интерфейса */
    protected function getStructureTemplate()
    {
        return $this->getTemplateContent(
            'interface.txt'
        );
    }

    /** @return string шаблон get-функции интерфейса */
    protected function getStructureGetterTemplate()
    {
        return $this->getTemplateContent(
            'interfaceGetter.txt'
        );
    }

    /** @return string шаблон комментария get-функции интерфейса */
    protected function getStructureGetterCommentTemplate()
    {
        return $this->getTemplateContent(
            'interfaceGetterComment.txt'
        );
    }

    public function generateStructureContent()
    {
        $structureTemplate = $this->getStructureTemplate();
        $replace = [
            '%nameSpace%' => $this->getNamespace(),
            '%useClasses%' => $this->getUseClasses(),
            '%structureComment%' => $this->getStructureComment(),
            '%structureName%' => $this->getStructureName(),
            '%structureFunctions%' => $this->getStructureFunctions()
        ];

        return StringHelper::replacePatterns(
            $structureTemplate,
            $replace
        );
    }

    protected function getStructureFunction(StructurePropertyInterface $property)
    {
        return $this->getStructureGetter($property, $this->getStructureGetterTemplate());
    }
}
