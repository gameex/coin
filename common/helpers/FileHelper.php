<?php
namespace common\helpers;

/**
 * 文件帮助类
 *
 * Class FileHelper
 * @package common\helpers
 */
class FileHelper
{
    /**
     * 检测目录并循环创建目录
     *
     * @param $path
     */
    public static function mkdirs($path)
    {
        if (!file_exists($path))
        {
            self::mkdirs(dirname($path));
            mkdir($path, 0777);
        }
    }

    /**
     * 写入日志
     *
     * @param string $path 路径
     * @param $content
     */
     public static function writeLog($path, $content)
     {
         file_put_contents($path, "\r\n" . $content, FILE_APPEND);
     }

    /**
     * 获取文件夹大小
     *
     * @param string $dir 根文件夹路径
     * @return int
     */
    public static function getDirSize($dir)
    {
        $handle = opendir($dir);
        $sizeResult = 0;
        while (false !== ($FolderOrFile = readdir($handle)))
        {
            if($FolderOrFile != "." && $FolderOrFile != "..")
            {
                if(is_dir("$dir/$FolderOrFile"))
                {
                    $sizeResult += self::getDirSize("$dir/$FolderOrFile");
                }
                else
                {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }

        closedir($handle);
        return $sizeResult;
    }

    /**
     * 基于数组创建目录
     *
     * @param $files
     */
    public static function createDirOrFiles($files)
    {
        foreach ($files as $key => $value)
        {
            if(substr($value, -1) == '/')
            {
                @mkdir($value);
            }
            else
            {
                @file_put_contents($value, '');
            }
        }
    }
}