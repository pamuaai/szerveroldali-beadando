<?php

// Checksum készítése egy megadott Laravel release-ről
// Készítette Tóta Dávid
// 2021. szeptember 6.

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Style\SymfonyStyle;

class checksums extends Command
{
    protected $signature = 'checksums';
    protected $description = 'Get original Laravel checksums';

    // A fájlok checksumjait melyik release-el vessük össze
    // TODO: kiszervezni paraméterbe? verzió detektálás?
    private $checkUrl = 'https://github.com/laravel/laravel/archive/refs/tags/v8.6.3.zip';

    private $disk;
    private $io;

    public function __construct() {
        parent::__construct();
        // config/filesystems.php alatt van beállítva, hogy a scope az egész project legyen
        // ezen a disk-en
        $this->disk = Storage::disk('project');
    }

    private function getOriginalLaravelChecksums() {
        // Ha még nincs temp mappa, hozzuk létre
        if (!$this->disk->exists('temp')) {
            $this->disk->makeDirectory('temp');
        }
        // Laravel projekt letöltése a temp mappába
        $data = file_get_contents($this->checkUrl);
        $this->disk->put('temp/laravel.zip', $data);
        // Kicsomagolás a temp/laravel mappába
        $zip = new \ZipArchive;
        $res = $zip->open('temp/laravel.zip');
        if ($res === true) {
            $zip->extractTo('temp/laravel');
            $zip->close();
            // Letöltött zip törlése
            $this->disk->delete('temp/laravel.zip');
            // A zipben volt egy mappa, azon belül van a Laravel project
            $dirs = $this->disk->directories('temp/laravel');
            if (count($dirs) < 1) {
                return false;
            }
            // Ez a path prefix azt adja meg, ami a letöltött project root előtt van, mint útvonal
            // A fájlok checksumjait majd a projecten belüli elhelyezkedésük szerint nézzük (tehát pl
            // egy web.php az a routes/web.php), de a checksumot a teljes útvonal szerint számoljuk
            // ki, szóval mindkettőre szükség van
            $prefix = $dirs[0].'/';
            $files = $this->disk->allFiles('temp/laravel');
            $checksums = [];
            foreach ($files as $file) {
                $fileWithoutPrefix = str_replace($prefix, "", $file);
                $checksums[$fileWithoutPrefix] = sha1_file($file);
            }
            $this->disk->deleteDirectory('temp');
            return $checksums;
        } else {
            return false;
        }
        $this->disk->deleteDirectory('temp');
        return false;
    }

    public function handle() {
        $this->io = new SymfonyStyle($this->input, $this->output);
        $laravelChecksums = $this->getOriginalLaravelChecksums();
        if ($laravelChecksums !== false) {
            $overwriteChecksums = [];
            $files = config('zipper.overwriteOriginalChecksums');
            foreach ($files as $file) {
                if ($this->disk->exists($file)) {
                    $overwriteChecksums[$file] = sha1_file($file);
                }
            }
            $this->disk->put("checksums.json", json_encode([
                "laravelChecksums" => $laravelChecksums,
                "overwriteChecksums" => $overwriteChecksums,
            ], JSON_PRETTY_PRINT));
        } else {
            $this->io->error('Nem sikerült letölteni a tiszta Laravel projektet a fájlok ellenőrzéséhez');
        }
        return 0;
    }
}
