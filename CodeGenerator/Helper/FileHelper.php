<?php

namespace Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileHelper
{
    /** Удаление папки со всем её содержимым
     * @param string $directoryPath путь к удаляемой папке
     */
    public static function removeDirectory($directoryPath)
    {
        $directoryIterator = new RecursiveDirectoryIterator(
            $directoryPath,
            RecursiveDirectoryIterator::SKIP_DOTS
        );
        $files = new RecursiveIteratorIterator(
            $directoryIterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($directoryPath);
    }

    /** Создать директорию рекурсивно если её не существует
     * @param string $directoryPath путь к создаваемой папке
     */
    public static function createDirectory($directoryPath)
    {
        if(file_exists($directoryPath) === false){
            mkdir(
                $directoryPath,
                0777,
                true
            );
        }
    }

    /** Пересоздать директорию (удалить и создать новую)
     * @param string $directoryPath путь к создаваемой папке
     */
    public static function recreateDirectory($directoryPath)
    {
        if(file_exists($directoryPath)){
            self::removeDirectory($directoryPath);
        }
        self::createDirectory($directoryPath);
    }
}