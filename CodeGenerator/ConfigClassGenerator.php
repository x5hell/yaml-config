<?php

namespace YamlConfig\CodeGenerator;

use YamlConfig\Helper\StringHelper;

/** Генератор класса конфига */
class ConfigClassGenerator
{
    /** @var ConfigClassInfo информация о классе конфига */
    protected $classInfo;

    /** @var string пространство имён конфига */
    protected $configNamespace;

    /** @param ConfigClassInfo $configClassInfo информация о классе конфига
     * @param string $configNamespace пространство имён конфига
     */
    public function __construct($configClassInfo, $configNamespace)
    {
        $this->classInfo = $configClassInfo;
        $this->configNamespace = $configNamespace;
    }

    /**
     * @return ConfigClassInfo информация о классе конфига
     */
    public function getStructureInfo()
    {
        return $this->classInfo;
    }

    /**
     * @return string пространство имён конфига
     */
    public function getConfigNamespace()
    {
        return $this->configNamespace;
    }

    /**
     * @param string $templateFileName имя файла шаблона
     * @return string содержимое шаблона
     */
    protected function getTemplateContent($templateFileName)
    {
        return file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR.
            'Templates'.
            DIRECTORY_SEPARATOR.
            $templateFileName
        );
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

    /**
     * @return string содержимое сгенеритованного класса
     */
    public function generateStructureContent()
    {
        $classTemplate = $this->getStructureTemplate();
        $replace = [
            '%nameSpace%' => $this->getNamespace(),
            '%useClasses%' => $this->getUseStructures(),
            '%classComment%' => $this->getStructureComment(),
            '%className%' => $this->getStructureName(),
            '%classProperties%' => $this->getStructureProperties(),
            '%classFunctions%' => $this->getStructureFunctions()
        ];

        return StringHelper::replacePatterns(
            $classTemplate,
            $replace
        );
    }

    /**
     * @return string пространство имён класса
     */
    protected function getNamespace()
    {
        $namespace = $this->getStructureInfo()->getNamespace();
        return strlen($namespace) > 0
            ? "namespace $namespace;"
            : '';
    }

    /**
     * @return string список подключаемых классов
     */
    protected function getUseStructures()
    {
        $useClasses = $this->getStructureInfo()->getUseStructures();
        $result = '';
        foreach ($useClasses as $useClass){
            $result .= "use $useClass;\n";
        }
        return $result;
    }

    /**
     * @return string комментарий класса
     */
    protected function getStructureComment()
    {
        $classComment =
            StringHelper::upperCaseFirstLetter(
                trim(
                    $this->getStructureInfo()->getComment()
                )
            );
        return strlen($classComment) > 0
            ? "/** $classComment */"
            : '';

    }

    /**
     * @return string название класса
     */
    protected function getStructureName()
    {
        return $this->getStructureInfo()->getName();
    }

    /**
     * @return string свойства класса
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
     * @param ClassProperty $classProperty описание свойства класса
     * @return string свойство класса
     */
    protected function getStructureProperty(
        ClassProperty $classProperty
    )
    {
        $propertyTemplate = $this->getStructurePropertyTemplate();
        $replace = [
            '%classPropertyComment%' => $this->getStructurePropertyComment(
                $classProperty
            ),
            '%classPropertyName%' => $classProperty->getName(),
            '%classPropertyValue%' => $this->getStructurePropertyValue(
                $classProperty
            )
        ];

        return StringHelper::replacePatterns(
            $propertyTemplate,
            $replace
        );
    }

    /**
     * @param ClassProperty $classProperty описание свойства класса
     * @return string свойство класса
     */
    protected function getStructurePropertyValue(
        ClassProperty $classProperty
    )
    {
        return $classProperty->getValue() !== null
            ? ' = '. var_export($classProperty->getValue(), 1)
            : '';
    }

    /**
     * @param ClassProperty $classProperty описание свойства класса
     * @return string комментарий к свойству класса
     */
    protected function getStructurePropertyComment(
        ClassProperty $classProperty
    )
    {
        $propertyComment = $this->getStructurePropertyCommentTemplate();
        $replace = [
            '%propertyType%' => $classProperty->getType(),
            '%propertyComment%' => trim($classProperty->getComment())
        ];

        return $classProperty->getComment() === null
            ? ''
            : StringHelper::replacePatterns(
                $propertyComment,
                $replace
            );
    }

    /**
     * @return string функции класса
     */
    protected function getStructureFunctions()
    {
        $result = [];
        $propertyList = $this
            ->getStructureInfo()
            ->getPropertyList();
        foreach ($propertyList as $property){
            $result[] =
                '    '.
                trim(
                    $this->getStructureFunction($property)
                );
        }
        return implode("\n\n", $result);
    }

    /**
     * @param ClassProperty $property описание свойства класса
     * @return string функция класса
     */
    protected function getStructureFunction(ClassProperty $property)
    {
        return $property->isStructure()
            ? $this->getStructureGetter(
                $property, $this->getStructureLazyGetterTemplate())
            : $this->getStructureGetter(
                $property, $this->getStructureGetterTemplate());
    }

    /**
     * @param ClassProperty $property описание свойства класса
     * @param string $getterTemplate шаблон get-функции
     * @return string get-функция класса
     */
    protected function getStructureGetter(
        ClassProperty $property,
        $getterTemplate
    )
    {
        $replace = [
            '%getterComment%' => $this->getStructureGetterComment(
                $property
            ),
            '%ClassName%' => ucfirst($property->getName()),
            '%PropertyName%' => $this->fixPropertyName($property->getName()),
            '%propertyName%' => $property->getName()
        ];

        return StringHelper::replacePatterns(
            $getterTemplate,
            $replace
        );
    }
    
    /** Исправить название свойства
     * @param string $propertyName имя свойства
     * @return string исправленное имя свойства */
    protected function fixPropertyName($propertyName)
    {
        return ucfirst(ltrim($propertyName, '_'));
    }

    /**
     * @param ClassProperty $property описание свойства класса
     * @return string комментарий get-функции класса
     */
    protected function getStructureGetterComment(ClassProperty $property)
    {
        $getterComment = $this->getStructureGetterCommentTemplate();
        $replace = [
            '%propertyType%' => $property->getType(),
            '%propertyComment%' => trim($property->getComment())
        ];

        return strlen($property->getComment())
            ? StringHelper::replacePatterns(
                $getterComment, $replace)
            : '';
    }
}
