<?php
namespace fmihel\pdf\drivers;

use fmihel\pdf\utils\Dir;

class ImagickDriver implements IPDFDriver
{
    private $enabled = null;
    public function enabled(): bool
    {
        if (is_null($this->enabled)) {
            $this->enabled = class_exists('Imagick');
        }
        return $this->enabled;
    }

    public function countPage(string $filename)
    {
        if ($this->enabled()) {

            try {

                $img = new \Imagick();
                $img->readImage($filename);
                $out = $img->getNumberImages();
                $img->clear();
                $img->destroy();

                return $out;

            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
        return 0;
    }

    public function convert(string $filename, string $outPath, string $outFormat = 'jpg', string $outPrefixName = '$name_$i', array $param = [])
    {
        if ($this->enabled()) {

            $info     = pathinfo($filename);
            $run_path = pathinfo($_SERVER['SCRIPT_FILENAME']);

            $param = array_merge([
                'dpi'                => 150,
                'compression'        => \Imagick::COMPRESSION_JPEG,
                'compressionQuality' => 100,
            ], $param);

            $DIVICES = [
                'jpg' => 'jpeg',
            ];

            $img = new \Imagick();
            try {

                $img->setResolution($param['dpi'], $param['dpi']);
                $img->readImage($filename);

                $count = $img->getNumberImages();

                $img->setImageFormat($DIVICES[$outFormat]);
                $img->setImageCompression($param['compression']);
                $img->setImageCompressionQuality($param['compressionQuality']);

                $tmp_pref = 'tmp' . rand(10000, 99999);

                $img->writeImages($tmp_pref . '.' . $outFormat, true);

                for ($i = 0; $i < $count; $i++) {
                    $from = str_replace('\\', '/', $run_path['dirname']) . '/' . $tmp_pref . '-' . $i . '.' . $outFormat;

                    $file = str_replace(['$name', '$i'], [$info['filename'], $i + 1], $outPrefixName) . ".$outFormat";
                    $to   = Dir::join($outPath, $file);

                    if (copy($from, $to)) {
                        unlink($from);
                    }
                }

            } catch (\Exception $e) {

                error_log($e->getMessage());

            } finally {

                $img->destroy();
            }

        }
    }
}
