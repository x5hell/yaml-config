<?php

namespace YamlConfig\ClassCodeGenerator;

use Slov\Helper\StringHelper;
use YamlConfig\StructureCodeGenerator\ConfigStructureGenerator;
use YamlConfig\StructureCodeGenerator\StructurePropertyInterface;

/** Генератор класса конфига */
class ConfigClassGenerator extends ConfigStructureGenerator
{

    protected function getTemplateDirectoryPath()
    {
        return __DIR__.
            DIRECTORY_SEPARATOR.
            'Templates';
    }

    /** @return string шаблон класса */
    protected function getStructureTemplate()
    {
        return $this->getTemplateContent(
            'class.txt'
        );
    }

    /** @return string шаблон свойства класса */
    protected function getStructurePropertyTemplate()
    {
        return $this->getTemplateContent(
            'classProperty.txt'
        );
    }

    /** @return string шаблон коментария свойства класса */
    protected function getStructurePropertyCommentTemplate()
    {
        return $this->getTemplateContent(
            'classPropertyComment.txt'
        );
    }

    /** @return string шаблон get-функции класса */
    protected function getStructureGetterTemplate()
    {
        return $this->getTemplateContent(
            'classGetter.txt'
        );
    }

    /** @return string шаблон ленивой get-функции класса */
    protected function getStructureLazyGetterTemplate()
    {
        return $this->getTemplateContent(
            'classLazyGetter.txt'
        );
    }

    /** @return string шаблон комментария get-функции класса */
    protected function getStructureGetterCommentTemplate()
    {
        return $this->getTemplateContent(
            'classGetterComment.txt'
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
            '%structureProperties%' => $this->getStructureProperties(),
            '%structureFunctions%' => $this->getStructureFunctions()
        ];

        return StringHelper::replacePatterns(
            $structureTemplate,
            $replace
        );
    }

    /**
     * @return string свойства структуры
     */
    protected function getStructureProperties()
    {
        $result = [];
        $propertyList = $this
            ->getStructureInfo()
            ->getPropertyList();
        foreach ($propertyList as $property){
            $result[] = '    '. trim(
                    $this->getStructureProperty($property)
                );
        }
        return implode("\n\n", $result);
    }

    /**
     * @param StructurePropertyInterface $property описание свойства структуры
     * @return string свойство структуры
     */
    protected function getStructureProperty(
        StructurePropertyInterface $property
    )
    {
        $propertyTemplate = $this->getStructurePropertyTemplate();
        $replace = [
            '%structurePropertyComment%' => $this->getStructurePropertyComment(
                $property
            ),
            '%structurePropertyName%' => $property->getName(),
            '%structurePropertyValue%' => $this->getStructurePropertyValue(
                $property
            )
        ];

        return StringHelper::replacePatterns(
            $propertyTemplate,
            $replace
        );
    }

    /**
     * @param StructurePropertyInterface $property описание свойства структуры
     * @return string свойство структуры
     */
    protected function getStructurePropertyValue(
        StructurePropertyInterface $property
    )
    {
        return $property->getValue() !== null
            ? ' = '. var_export($property->getValue(), 1)
            : '';
    }

    /**
     * @param StructurePropertyInterface $property описание свойства структуры
     * @return string комментарий к свойству структуры
     */
    protected function getStructurePropertyComment(
        StructurePropertyInterface $property
    )
    {
        $propertyComment = $this->getStructurePropertyCommentTemplate();
        $replace = [
            '%propertyType%' => $property->getType(),
            '%propertyComment%' => trim($property->getComment())
        ];

        return $property->getComment() === null
            ? ''
            : StringHelper::replacePatterns(
                $propertyComment,
                $replace
            );
    }

    protected function getStructureFunction(StructurePropertyInterface $property)
    {
        return $property->isStructure()
            ? $this->getStructureGetter(
                $property, $this->getStructureLazyGetterTemplate())
            : $this->getStructureGetter(
                $property, $this->getStructureGetterTemplate());
    }
}
