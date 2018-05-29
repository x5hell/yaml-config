<?php

namespace YamlConfig\StructureCodeGenerator;

use Symfony\Component\Yaml\Yaml;
use Slov\Helper\FileHelper;
use YamlConfig\YamlCommentsParser;

/** Генератор структуры */
abstract class ConfigStructureTreeGenerator
{
    /** @var string путь к папке проекта */
    protected $projectPath;

    /** @var string относительный путь к папке в которой будет сгенерирован код конфига */
    protected $configCodeRelativePath;

    /** @var string относительный путь расположения yaml-файл с настройками  */
    protected $configRelativePath;

    /** @var string пространство имён конфига */
    protected $configNamespace;

    /** @var string название структуры конфига */
    protected $configName;

    /** @var string[] комментарии к узлам конфига */
    protected $configNodeComments;

    /** @var string содержимое конфигурации yaml */
    protected $yamlConfigContent;

    /** @var array дерево конфигурации yaml */
    protected $yamlConfigTree;

    /**
     * @return string путь к папке проекта
     */
    protected function getProjectPath()
    {
        return $this->projectPath;
    }

    /**
     * @param string $projectPath путь к папке проекта
     * @return $this
     */
    public function setProjectPath($projectPath)
    {
        $this->projectPath = realpath($projectPath);
        return $this;
    }

    /**
     * @return string относительный путь к папке в которой будут сгенерирован код конфига
     */
    protected function getConfigCodeRelativePath()
    {
        return $this->configCodeRelativePath;
    }

    /**
     * @param string $configCodeRelativePath относительный путь к папке в которой будут сгенерирован код конфига
     * @return $this
     */
    public function setConfigCodeRelativePath($configCodeRelativePath)
    {
        $this->configCodeRelativePath = $configCodeRelativePath;
        return $this;
    }

    /**
     * @return string относительный путь расположения yaml-файл с настройками
     */
    protected function getConfigRelativePath()
    {
        return $this->configRelativePath;
    }

    /**
     * @param string $configRelativePath относительный путь расположения yaml-файл с настройками
     * @return $this
     */
    public function setConfigRelativePath($configRelativePath)
    {
        $this->configRelativePath = $configRelativePath;
        return $this;
    }

    /**
     * @return string пространство имён конфига
     */
    protected function getConfigNamespace()
    {
        return $this->configNamespace;
    }

    /**
     * @param string $configNamespace пространство имён конфига
     * @return $this
     */
    public function setConfigNamespace($configNamespace)
    {
        $this->configNamespace = $configNamespace;
        return $this;
    }

    /**
     * @return string название структуры конфига
     */
    protected function getConfigName()
    {
        return $this->configName;
    }

    /**
     * @param string $configName название структуры конфига
     * @return $this
     */
    public function setConfigName($configName)
    {
        $this->configName = $configName;
        return $this;
    }
    
    /**
     * @return StructureInfoListInterface список информации о структурах
     */
    protected function createStructureInfoList()
    {
        return new StructureInfoList();
    }
    
    /**
     * 
     * @param ConfigStructureInfoInterface $configStructureInfo информация о структуре конфига
     * @return ConfigStructureGeneratorInterface
     */
    abstract protected function createConfigStructureGenerator($configStructureInfo);

    /**
     * @return string[] комментарии к узлам конфига
     */
    protected function getConfigNodeComments()
    {
        if(is_null($this->configNodeComments)){
            $this->configNodeComments = YamlCommentsParser::parse(
                $this->getYamlConfigContent()
            );
        }

        return $this->configNodeComments;
    }

    /**
     * @return string содержимое конфига yaml
     */
    protected function getYamlConfigContent()
    {
        if(is_null($this->yamlConfigContent)){
            $this->yamlConfigContent = file_get_contents(
                $this->getConfigFullPath()
            );
        }
        return $this->yamlConfigContent;
    }

    /**
     * @return string полный путь к конфигу
     */
    public function getConfigFullPath()
    {
        return $this->getProjectPath().
            DIRECTORY_SEPARATOR.
            $this->getConfigRelativePath();
    }

    /**
     * @return array массив дерева конфига
     */
    protected function getYamlConfigTree()
    {
         if(is_null($this->yamlConfigTree)){
             $this->yamlConfigTree = Yaml::parse(
                 $this->getYamlConfigContent()
             );
         }

        return $this->yamlConfigTree;
    }

    /**
     * @return string относительный путь к папке в которой будет сгенерирован код конфига
     */
    protected function getConfigCodeFullPath()
    {
        return
            $this->getConfigCodeRelativePath() === ''
                ? $this->getProjectPath()
                : $this->getProjectPath().
                DIRECTORY_SEPARATOR.
                $this->getConfigCodeRelativePath();
    }

    /** Генерация кода конфига
     * @param callable $callback функция вызываемая после успешной генерации */
    public function generate($callback = null)
    {
        if($this->generationNeeded()){
            FileHelper::recreateDirectory(
                $this->getConfigCodeFullPath()
            );
            $configWithRoot = [
                $this->getConfigName() => $this->getYamlConfigTree()
            ];

            $structureInfoList = $this->buildStructureInfoList($configWithRoot);

            foreach($structureInfoList->getStructureInfoList() as $structureInfo){
                $this->saveStructureContent($structureInfo);
            };

            if(is_callable($callback)){
                $callback();
            }
        }
    }
    
    /**
     * @return bool true - если генерация требуется
     */
    protected function generationNeeded()
    {
        return
            is_dir($this->getConfigCodeFullPath()) === false
            ||
            filemtime($this->getConfigFullPath()) > filemtime($this->getConfigCodeFullPath());
    }

    /** Сгенерировать и сохранить контент структуры конфига
     * @param ConfigStructureInfoInterface $structureInfo информация о структуре */
    protected function saveStructureContent(ConfigStructureInfoInterface $structureInfo)
    {
        $structureContent = $this->generateStructureContent($structureInfo);
        $fileRootDirectory = $this->getConfigCodeFullPath();

        $namespaceParts = explode('\\', $structureInfo->getNamespace());
        $baseNamespaceParts = explode('\\', $this->getConfigNamespace());
        $relativeNamespaceParts = array_slice($namespaceParts, count($baseNamespaceParts));
        $fileRelativeDirectory = implode(DIRECTORY_SEPARATOR, $relativeNamespaceParts);
        
        $fileDirectoryPath = strlen($fileRelativeDirectory) > 0
            ? $fileRootDirectory.
                DIRECTORY_SEPARATOR.
                $fileRelativeDirectory
            : $fileRootDirectory;

        $fileFullPath = $fileDirectoryPath.
            DIRECTORY_SEPARATOR.
            $structureInfo->getName(). '.php';
        FileHelper::createDirectory($fileDirectoryPath);
        file_put_contents($fileFullPath, $structureContent);
    }

    /**
     * @param array $configTree дерево конфига
     * @return StructureInfoListInterface список информации о генерируемых структурах
     */
    protected function buildStructureInfoList($configTree)
    {
        $structureInfoList = $this->createStructureInfoList();
        $structureInfoList->setConfigFullPath(
            $this->getConfigFullPath()
        );
        $structureInfoList->setConfigNamespace(
            $this->getConfigNamespace()
        );
        $structureInfoList->initFromTree($configTree);
        return $structureInfoList;
    }

    /**
     * @param ConfigStructureInfoInterface $structureInfo информация о структуре
     * @return string содержимое сгенерированной структуры
     */
    protected function generateStructureContent(ConfigStructureInfoInterface $structureInfo)
    {
        $structureGenerator = $this->createConfigStructureGenerator(
            $structureInfo
        );
        return $structureGenerator->generateStructureContent();
    }
}
