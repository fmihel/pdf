<?php
namespace fmihel\pdf\drivers;

use fmihel\console;
use fmihel\pdf\utils\Dir;

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
        $this->enabled = count($this->version) && $this->version[0] > 8;

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

    public function convert(string $filename, string $outPath, string $outFormat = 'jpg', string $outPrefixName = '$name_$i', array $param = []): array
    {
        $out    = [];
        $DEVICE = isset($param['dpi']) ? ' -r' . $param['dpi'] : '';

        $info     = pathinfo($filename);
        $run_path = pathinfo($_SERVER['SCRIPT_FILENAME']);
        $filename = str_replace('\\', '/', $filename);

        $count    = $this->countPage($filename);
        $tmp_pref = 'tmp' . rand(10000, 99999);

        $devices = [
            'jpg' => 'jpeg',
            'png' => 'png16m',
            'pdf' => 'pdf',
        ];

        // -dQFactor=1.0 -dJPEG=100 
        $command = '-sDEVICE=' . $devices[$outFormat] . "$DEVICE -sOutputFile=" . $tmp_pref . "-%d.$outFormat -dBATCH -dNOPAUSE $filename";

        $this->execute_gs($command);

        for ($i = 0; $i < $count; $i++) {
            $from = str_replace('\\', '/', $run_path['dirname']) . '/' . $tmp_pref . '-' . ($i + 1) . '.' . $outFormat;

            $file = str_replace(['$name', '$i'], [$info['filename'], $i + 1], $outPrefixName) . ".$outFormat";
            $to   = Dir::join($outPath, $file);

            if (copy($from, $to)) {
                unlink($from);
            }

            $out[] = $to;
        }

        return $out;
    }
    public function extract(string $filename, $pageNum, string $outFileName = ''): string
    {
        if (empty($outFileName)) {
            $run_path    = pathinfo($_SERVER['SCRIPT_FILENAME']);
            $name        = pathinfo($filename)['filename'];
            $outFileName = Dir::join($run_path['dirname'], $name . '_' . $pageNum . '.pdf');
        }
        $this->execute_gs('-dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $outFileName . '" -dFirstPage=' . $pageNum . ' -dLastPage=' . $pageNum . ' -sDEVICE=pdfwrite "' . $filename . '"');
        return $outFileName;
    }
    private function execute($command)
    {
        console::log($command);
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

}
