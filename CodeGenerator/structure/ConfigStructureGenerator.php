<?php

namespace YamlConfig\StructureCodeGenerator;

use Slov\Helper\StringHelper;

/** Генератор структуры конфига */
abstract class ConfigStructureGenerator implements ConfigStructureGeneratorInterface
{
    /** @var ConfigStructureInfoInterface информация о структуре конфига */
    protected $structureInfo;

    /**
     * @return ConfigStructureInfoInterface информация о структуре конфига
     */
    protected function getStructureInfo()
    {
        return $this->structureInfo;
    }

    public function setStructureInfo($structureInfo)
    {
        $this->structureInfo = $structureInfo;
        return $this;
    }

    /**
     * @return string путь к папке с шаблонами
     */
    abstract protected function getTemplateDirectoryPath();

    /**
     * @param string $templateFileName имя файла шаблона
     * @return string содержимое шаблона
     */
    protected function getTemplateContent($templateFileName)
    {
        return file_get_contents(
            $this->getTemplateDirectoryPath().
            DIRECTORY_SEPARATOR.
            $templateFileName
        );
    }

    /** @return string шаблон структуры */
    abstract protected function getStructureTemplate();

    /** @return string шаблон get-функции структуры */
    abstract protected function getStructureGetterTemplate();

    /** @return string шаблон комментария get-функции структуры */
    abstract protected function getStructureGetterCommentTemplate();

    /**
     * @return string пространство имён структуры
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
    protected function getUseClasses()
    {
        $useClasses = $this->getStructureInfo()->getUseClasses();
        $result = '';
        foreach ($useClasses as $useClass){
            $result .= "use $useClass;\n";
        }
        return $result;
    }

    /**
     * @return string комментарий структуры
     */
    protected function getStructureComment()
    {
        $structureComment =
            StringHelper::upperCaseFirstLetter(
                trim(
                    $this->getStructureInfo()->getComment()
                )
            );
        return strlen($structureComment) > 0
            ? "/** $structureComment */"
            : '';

    }

    /**
     * @return string название структуры
     */
    protected function getStructureName()
    {
        return $this->getStructureInfo()->getName();
    }

    /**
     * @return string функции структруры
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
     * @param StructurePropertyInterface $property описание свойства структуры
     * @return string функция структуры
     */
    abstract protected function getStructureFunction(StructurePropertyInterface $property);

    /**
     * @param StructurePropertyInterface $property описание свойства структуры
     * @param string $getterTemplate шаблон get-функции
     * @return string get-функция структуры
     */
    protected function getStructureGetter(
        StructurePropertyInterface $property,
        $getterTemplate
    )
    {
        $replace = [
            '%getterComment%' => $this->getStructureGetterComment(
                $property
            ),
            '%StructureName%' => ucfirst($property->getName()),
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
     * @param StructurePropertyInterface $property описание свойства структуры
     * @return string комментарий get-функции структуры
     */
    protected function getStructureGetterComment(StructurePropertyInterface $property)
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
