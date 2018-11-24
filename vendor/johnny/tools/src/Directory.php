<?php namespace Johnny\Tools;

class Directory
{
    /**
     * make dir
     *
     * @param $dir
     * @param int $mode
     * @return bool
     */
    public static function mkdir($dir, $mode = 0755)
    {
        if (is_dir($dir) || @mkdir($dir,$mode))
        {
            return true;
        }
        if (!self::mkdir(dirname($dir),$mode))
        {
            return false;
        }

        return @mkdir($dir,$mode);
    }
}

