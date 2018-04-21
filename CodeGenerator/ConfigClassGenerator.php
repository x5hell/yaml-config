<?php

namespace YamlConfig\CodeGenerator;

use Helper\StringHelper;

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
    public function getClassInfo()
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
    protected function getClassTemplate()
    {
        return $this->getTemplateContent(
            'class.txt'
        );
    }

    /** @return string шаблон свойства класса */
    protected function getClassPropertyTemplate()
    {
        return $this->getTemplateContent(
            'classProperty.txt'
        );
    }

    /** @return string шаблон коментария свойства класса */
    protected function getClassPropertyCommentTemplate()
    {
        return $this->getTemplateContent(
            'classPropertyComment.txt'
        );
    }

    /** @return string шаблон get-функции класса */
    protected function getClassGetterTemplate()
    {
        return $this->getTemplateContent(
            'classGetter.txt'
        );
    }

    /** @return string шаблон ленивой get-функции класса */
    protected function getClassLazyGetterTemplate()
    {
        return $this->getTemplateContent(
            'classLazyGetter.txt'
        );
    }

    /** @return string шаблон комментария get-функции класса */
    protected function getClassGetterCommentTemplate()
    {
        return $this->getTemplateContent(
            'classGetterComment.txt'
        );
    }

    /**
     * @return string содержимое сгенеритованного класса
     */
    public function generateClassContent()
    {
        $classTemplate = $this->getClassTemplate();
        $replace = [
            '%nameSpace%' => $this->getNamespace(),
            '%useHistoryProperties%' => $this->getUseHistoryProperties(),
            '%useClasses%' => $this->getUseClasses(),
            '%classComment%' => $this->getClassComment(),
            '%className%' => $this->getClassName(),
            '%classProperties%' => $this->getClassProperties(),
            '%classFunctions%' => $this->getClassFunctions()
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
        $namespace = $this->getClassInfo()->getNamespace();
        return strlen($namespace) > 0
            ? "namespace $namespace;"
            : '';
    }

    /**
     * @return string подключение клласса для работы со свойствами имеющими временную актуальность
     */
    protected function getUseHistoryProperties()
    {
        return
            'use '.
            $this->getConfigNamespace().
            '\\HistoryProperties;';
    }

    /**
     * @return string список подключаемых классов
     */
    protected function getUseClasses()
    {
        $useClasses = $this->getClassInfo()->getUseClasses();
        $result = '';
        foreach ($useClasses as $useClass){
            $result .= "use $useClass;\n";
        }
        return $result;
    }

    /**
     * @return string комментарий класса
     */
    protected function getClassComment()
    {
        $classComment =
            StringHelper::upperCaseFirstLetter(
                trim(
                    $this->getClassInfo()->getClassComment()
                )
            );
        return strlen($classComment) > 0
            ? "/** $classComment */"
            : '';

    }

    /**
     * @return string название класса
     */
    protected function getClassName()
    {
        return $this->getClassInfo()->getClassName();
    }

    /**
     * @return string свойства класса
     */
    protected function getClassProperties()
    {
        $result = [];
        $propertyList = $this
            ->getClassInfo()
            ->getClassPropertyList();
        foreach ($propertyList as $property){
            $result[] = '    '. trim(
                $this->getClassProperty($property)
            );
        }
        return implode("\n\n", $result);
    }

    /**
     * @param ClassProperty $classProperty описание свойства класса
     * @return string свойство класса
     */
    protected function getClassProperty(
        ClassProperty $classProperty
    )
    {
        $propertyTemplate = $this->getClassPropertyTemplate();
        $replace = [
            '%classPropertyComment%' => $this->getClassPropertyComment(
                $classProperty
            ),
            '%classPropertyName%' => $classProperty->getName(),
            '%classPropertyValue%' => $this->getClassPropertyValue(
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
    protected function getClassPropertyValue(
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
    protected function getClassPropertyComment(
        ClassProperty $classProperty
    )
    {
        $propertyComment = $this->getClassPropertyCommentTemplate();
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
    protected function getClassFunctions()
    {
        $result = [];
        $propertyList = $this
            ->getClassInfo()
            ->getClassPropertyList();
        foreach ($propertyList as $property){
            $result[] =
                '    '.
                trim(
                    $this->getClassFunction($property)
                );
        }
        return implode("\n\n", $result);
    }

    /**
     * @param ClassProperty $property описание свойства класса
     * @return string функция класса
     */
    protected function getClassFunction(ClassProperty $property)
    {
        return $property->isClass()
            ? $this->getClassGetter(
                $property, $this->getClassLazyGetterTemplate())
            : $this->getClassGetter(
                $property, $this->getClassGetterTemplate());
    }

    /**
     * @param ClassProperty $property описание свойства класса
     * @param string $getterTemplate шаблон get-функции
     * @return string get-функция класса
     */
    protected function getClassGetter(
        ClassProperty $property,
        $getterTemplate
    )
    {
        $replace = [
            '%getterComment%' => $this->getClassGetterComment(
                $property
            ),
            '%PropertyName%' => ucfirst($property->getName()),
            '%propertyName%' => $property->getName()
        ];

        return StringHelper::replacePatterns(
            $getterTemplate,
            $replace
        );
    }

    /**
     * @param ClassProperty $property описание свойства класса
     * @return string комментарий get-функции класса
     */
    protected function getClassGetterComment(ClassProperty $property)
    {
        $getterComment = $this->getClassGetterCommentTemplate();
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