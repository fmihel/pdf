<?php
namespace fmihel\pdf\utils;

class Dir
{
    public static function join(...$paths)
    {
        $paths = array_map(function ($path) {return str_replace('\\', '/', $path);}, $paths);
        $out = implode('/', $paths);
        while (strpos($out, '//') !== false) {
            $out = str_replace('//', '/', $out);
        }

        return $out;

    }

    public static function relativePath(string $from, string $to): string
    {
        // Normalize paths to remove redundant slashes and resolve '..'
        $from = realpath($from);
        $to   = realpath($to);

        if ($from === false || $to === false) {
            return $to; // One or both paths are invalid, return the target path as is
        }

        $fromParts = explode(DIRECTORY_SEPARATOR, $from);
        $toParts   = explode(DIRECTORY_SEPARATOR, $to);

        // Find the common base path
        $commonParts = 0;
        while (isset($fromParts[$commonParts]) && isset($toParts[$commonParts]) && $fromParts[$commonParts] === $toParts[$commonParts]) {
            $commonParts++;
        }

        // Add '..' for each directory to go up from the 'from' path
        $relativePath = str_repeat('..' . DIRECTORY_SEPARATOR, count($fromParts) - $commonParts);

        // Add the remaining parts of the 'to' path
        for ($i = $commonParts; $i < count($toParts); $i++) {
            $relativePath .= $toParts[$i] . DIRECTORY_SEPARATOR;
        }

        // Remove trailing slash if not pointing to a directory
        if ($relativePath !== '' && substr($relativePath, -1) === DIRECTORY_SEPARATOR && ! is_dir($to)) {
            $relativePath = substr($relativePath, 0, -1);
        }

        return $relativePath;
    }

}
