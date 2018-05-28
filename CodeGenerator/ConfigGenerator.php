<?php

namespace YamlConfig\CodeGenerator;

use Symfony\Component\Yaml\Yaml;
use YamlConfig\Helper\FileHelper;
use YamlConfig\YamlCommentsParser;

/** Генератор конфига */
class ConfigGenerator
{

    const DEFAULT_CONFIG_NAME = 'Config';

    /** @var string путь к папке проекта */
    protected $projectPath;

    /** @var string относительный путь к папке в которой будет сгенерирован код конфига */
    protected $configCodeRelativePath;

    /** @var string относительный путь расположения yaml-файл с настройками  */
    protected $configRelativePath;

    /** @var string пространство имён конфига */
    protected $configNamespace;

    /** @var string название класса конфига */
    protected $configName = self::DEFAULT_CONFIG_NAME;

    /** @var string[] комментарии к узлам конфига */
    protected $configNodeComments;

    /** @var string содержимое конфигурации yaml */
    protected $yamlConfigContent;

    /** @var array дерево конфигурации yaml */
    protected $yamlConfigTree;

    /**
     * @return string путь к папке проекта
     */
    public function getProjectPath()
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
    public function getConfigNamespace()
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
     * @return string название класса конфига
     */
    public function getConfigName()
    {
        return $this->configName;
    }

    /**
     * @param string $configName название класса конфига
     * @return $this
     */
    public function setConfigName($configName)
    {
        $this->configName = $configName;
        return $this;
    }
    
    /**
     * 
     * @return ClassInfoList
     */
    public function createStructureInfoList()
    {
        return new ClassInfoList;
    }
    
    /**
     * 
     * @param ConfigClassInfo $configClassInfo информация о классе конфига
     * @param string $configNamespace пространство имён конфига
     * @return ConfigClassGenerator
     */
    public function createConfigStructureGenerator($configClassInfo, $configNamespace)
    {
        return new ConfigClassGenerator($configClassInfo, $configNamespace);
    }

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
     * @return array
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

            $classInfoList = $this->buildStructureInfoList($configWithRoot);

            foreach($classInfoList->getStructureInfoList() as $classInfo){
                $this->saveStructureContent($classInfo);
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

    /** Сгенерировать и сохранить контент класса конфига
     * @param ConfigClassInfo $classInfo информация о классе */
    protected function saveStructureContent(ConfigClassInfo $classInfo)
    {
        $classContent = $this->generateClassContent($classInfo);
        $fileRootDirectory = $this->getConfigCodeFullPath();

        $namespaceParts = explode('\\', $classInfo->getNamespace());
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
            $classInfo->getName(). '.php';
        FileHelper::createDirectory($fileDirectoryPath);
        file_put_contents($fileFullPath, $classContent);
    }

    /**
     * @param array $configTree дерево конфига
     * @return ClassInfoList список информации о генерируемых классах
     */
    protected function buildStructureInfoList($configTree)
    {
        $classInfoList = $this->createStructureInfoList();
        $classInfoList->setConfigFullPath(
            $this->getConfigFullPath()
        );
        $classInfoList->setConfigNamespace(
            $this->getConfigNamespace()
        );
        $classInfoList->initFromTree($configTree);
        return $classInfoList;
    }

    /**
     * @param ConfigClassInfo $classInfo информация о классе
     * @return string содержимое сгенерированного класса
     */
    protected function generateClassContent(ConfigClassInfo $classInfo)
    {
        $classGenerator = $this->createConfigStructureGenerator(
            $classInfo, $this->getConfigNamespace()
        );
        return $classGenerator->generateStructureContent();
    }
}
