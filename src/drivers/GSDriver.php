<?php
namespace fmihel\pdf\drivers;

use fmihel\console;

class GSDriver implements IPDFDriver
{

    private $is_win  = false;
    public $version  = [];
    private $gs      = 'gs';
    private $enabled = false;

    public function __construct(array $param = [])
    {
        $this->is_win = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($this->is_win) {
            foreach (['gswin64c.exe', 'gswin64.exe'] as $exe) {
                if (! empty($find = $this->execute("where $exe"))) {
                    if (strpos($find, $exe) !== false) {
                        $this->gs = $exe;
                        break;
                    }
                }
            }
        }

        $this->version = $this->str_to_version($this->execute_gs('--version'));
        $this->enabled = count($this->version) > 0 && $this->version[0] > 8;

    }
    public function enabled(): bool
    {
        return $this->enabled;
    }
    public function countPage(string $filename)
    {

        if ($this->enabled()) {

            // $run_path = pathinfo($_SERVER['SCRIPT_FILENAME']);
            // $to_path  = pathinfo($filename);
            // $filename = str_replace('\\', '/', $this->getRelativePath($run_path['dirname'], $to_path['dirname']) . basename($filename));
            $filename = str_replace('\\', '/', $filename);

            if ($this->version[0] > 9) {
                $command = '-dQUIET -dNODISPLAY --permit-file-read="' . $filename . '" -c "(' . $filename . ') (r) file runpdfbegin pdfpagecount = quit"';
            } else {
                $command = '-dQUIET -dNODISPLAY -c "(' . $filename . ') (r) file runpdfbegin pdfpagecount = quit"';
            }

            return intval($this->execute_gs($command));
        }
        return 0;
    }

    public function convert(string $filename, string $outPath, string $outFormat = 'jpg', string $outPrefixName = '$name_$i', array $param = [])
    {
        $DEVICE = isset($param['dpi']) ? ' -r' . $param['dpi'] : '';

        $info     = pathinfo($filename);
        $run_path = pathinfo($_SERVER['SCRIPT_FILENAME']);
        $filename = str_replace('\\', '/', $filename);

        $count    = $this->countPage($filename);
        $tmp_pref = 'tmp' . rand(10000, 99999);

        $devices = [
            'jpg' => 'jpeg',
            'png' => 'png16m',
        ];

        // -dQFactor=1.0 -dJPEG=100 
        $command = '-sDEVICE=' . $devices[$outFormat] . "$DEVICE -sOutputFile=" . $tmp_pref . "-%d.$outFormat -dBATCH -dNOPAUSE $filename";
        console::log($command);
        $this->execute_gs($command);

        $name = $info['filename'];
        for ($i = 0; $i < $count; $i++) {
            $from = str_replace('\\', '/', $run_path['dirname']) . '/' . $tmp_pref . '-' . ($i + 1) . '.' . $outFormat;

            $file = str_replace(['$name', '$i'], [$name, $i + 1], $outPrefixName) . ".$outFormat";
            $to   = $this->join($outPath, $file);

            if (copy($from, $to)) {
                unlink($from);
            }
        }

    }

    private function execute($command)
    {
        $out = null;
        if ($this->is_win) {
            $out = shell_exec($command);
        } else {
            exec($command, $out);
        }
        if (gettype($out) === 'array') {
            return implode(' ', $out);
        }
        return $out;
    }
    private function execute_gs(string $command): string
    {
        // console::log("\n" . $this->gs . ' ' . $command);
        $result = $this->execute($this->gs . ' ' . $command);

        return empty($result) ? '' : "$result";
    }

    private function str_to_version(string $version): array
    {
        $out = [];
        foreach (explode('.', $version) as $v) {
            $out[] = intval($v);
        }
        return $out;
    }

    private function join(...$paths)
    {
        $paths = array_map(function ($path) {return str_replace('\\', '/', $path);}, $paths);
        $out = implode('/', $paths);
        while (strpos($out, '//') !== false) {
            $out = str_replace('//', '/', $out);
        }

        return $out;

    }

    // private function getRelativePath(string $from, string $to): string
    // {
    //     // Normalize paths to remove redundant slashes and resolve '..'
    //     $from = realpath($from);
    //     $to   = realpath($to);

    //     if ($from === false || $to === false) {
    //         return $to; // One or both paths are invalid, return the target path as is
    //     }

    //     $fromParts = explode(DIRECTORY_SEPARATOR, $from);
    //     $toParts   = explode(DIRECTORY_SEPARATOR, $to);

    //     // Find the common base path
    //     $commonParts = 0;
    //     while (isset($fromParts[$commonParts]) && isset($toParts[$commonParts]) && $fromParts[$commonParts] === $toParts[$commonParts]) {
    //         $commonParts++;
    //     }

    //     // Add '..' for each directory to go up from the 'from' path
    //     $relativePath = str_repeat('..' . DIRECTORY_SEPARATOR, count($fromParts) - $commonParts);

    //     // Add the remaining parts of the 'to' path
    //     for ($i = $commonParts; $i < count($toParts); $i++) {
    //         $relativePath .= $toParts[$i] . DIRECTORY_SEPARATOR;
    //     }

    //     // Remove trailing slash if not pointing to a directory
    //     if ($relativePath !== '' && substr($relativePath, -1) === DIRECTORY_SEPARATOR && ! is_dir($to)) {
    //         $relativePath = substr($relativePath, 0, -1);
    //     }

    //     return $relativePath;
    // }
}
