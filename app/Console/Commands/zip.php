<?php

// Nyilatkozat ellenőrző és projekt zippelő Laravel beadandókhoz
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

class zip extends Command
{
    protected $signature = 'zip';
    protected $description = 'Create zip from your work';

    private $disk;
    private $io;
    private $project;
    private $config;
    private $checksums = false;

    public function __construct() {
        parent::__construct();
        $this->config = config('zipper');
        // config/filesystems.php alatt van beállítva, hogy a scope az egész project legyen
        // ezen a disk-en
        $this->disk = Storage::disk('project');
        if ($this->disk->exists("checksums.json")) {
            $this->checksums = json_decode($this->disk->get("checksums.json"), true);
        }
        $this->project = $this->parseFiles();
    }

    // Validálással kibővített console ask
    private function validatedAsk($question, $rules, $messages = []) {
        $value = $this->ask($question);
        $validator = Validator::make(
            ['field' => $value], // értékek
            ['field' => $rules], // szabályok
            $messages            // hibaüzenetek
        );
        if ($validator->fails()) {
            // Minden előfodruló hiba megjelenítése
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return $this->validatedAsk($question, $rules, $messages);
        }
        return $value;
    }

    private function handleStatement() {
        // Ha valaki egyszer már végigment a kitöltési folyamaton, akkor generálódott
        // egy checksum. Ilyenkor, mivel a validált folyamaton ment keresztül, felté-
        // telezzük, hogy a kitöltés helyes volt, ezért csak visszaellenőrizzük, hogy
        // a checksum egyezik-e (nem változott azóta a fájl), hogy ne kelljen minden
        // alkalommal újra kitöltenie a nyilatkozatos formot a hallgatónak.
        if ($this->disk->exists('STATEMENT.md') && Cache::has('statement_checksum') && Cache::has('statement_name') && Cache::has('statement_neptun_code')) {
            $checksum = Cache::get('statement_checksum');
            $name = Cache::get('statement_name');
            $neptun = Cache::get('statement_neptun_code');
            if ($checksum && $name && $neptun) {
                $statement = $this->disk->get('STATEMENT.md');
                if (sha1($statement) === $checksum) {
                    $this->io->success("A nyilatkozat korábban már ki lett töltve " . $name . " névre és " . $neptun . " Neptun kódra.");
                    $this->io->note("Ha a fenti adatok tévesek, akkor töröld ki a STATEMENT.md fájlt, majd hívd meg újra a zip parancsot, ilyenkor újra meg fog jelenni a nyilatkozat kitöltő.");
                    $this->newLine();
                    return true;
                } else {
                    $this->warn("A korábban kitöltött nyilatkozat ellenőrzése nem sikerült, ezért újra ki kell tölteni.");
                    $this->newLine();
                }
            }
        }
        // Nyilatkozat megjelenítése a hallgatónak, majd az elfogadás, és az adatok bekérése
        $this->line('NYILATKOZAT:');
        $this->newLine();
        $this->line(base64_decode($this->config['statementPreview']));
        $this->newLine();
        if ($this->confirm('Elolvastad, elfogadod, és magadra nézve kötelező érvényűnek tekinted a fenti nyilatkozatot?')) {
            $this->info("Kérjük, add meg a nevedet és a Neptun kódodat, hogy be tudjuk helyettesíteni azokat a nyilatkozatba.");
            // Név bekérése
            $name = $this->validatedAsk('Mi a neved?', [
                'required',
                'min:3',
                'max:128',
                'regex:/^[\pL\s\-]+$/u'
            ], [
                'required' => 'A név megadása kötelező.',
                'min' => 'A név hossza legalább :min karakter.',
                'max' => 'A név nem lehet hosszabb, mint :max karakter.',
                'regex' => 'A név alfanumerikus karakterekből és szóközökből állhat.'
            ]);
            // Neptun kód bekérése
            $neptun = Str::upper($this->validatedAsk('Mi a Neptun kódod?', [
                'required',
                'string',
                'size:6',
                'regex:/[a-zA-Z0-9]/'
            ], [
                'required' => 'A Neptun kód megadása kötelező.',
                'size' => 'A Neptun kód hossza pontosan :size karakter.',
                'regex' => 'A Neptun kód csak A-Z karakterekből és számokból állhat.'
            ]));
            // Aktuális dátum
            $date = Carbon::now('Europe/Budapest')->isoFormat('Y. MM. DD. kk:MM:ss');
            // Nyilatkozat kitöltése
            $filledStatement = Str::of(base64_decode($this->config['statementTemplate']))
                ->replace('<NAME>', $name)
                ->replace('<NEPTUN>', $neptun)
                ->replace('<DATE>', $date);
            // Adatok tárolása
            $this->disk->put('STATEMENT.md', $filledStatement);
            Cache::set('statement_checksum', sha1($filledStatement));
            Cache::set('statement_name', $name);
            Cache::set('statement_neptun_code', $neptun);
            //
            $this->io->success("A nyilatkozat kitöltése sikeresen megtörtént " . $name . " névre és " . $neptun . " Neptun kódra.");
            $this->io->note("Ha a fenti adatok tévesek, akkor töröld ki a STATEMENT.md fájlt, majd hívd meg újra a zip parancsot, ilyenkor újra meg fog jelenni a nyilatkozat kitöltő.");
        } else {
            $this->error('A nyilatkozat a tárgy követelményei szerint kötelező a beadandó leadásához és az értékelés megszerzéséhez.');
            return false;
        }
        $this->newLine();
        return true;
    }

    private function parseFiles($directory = '.', $parentGitignores = []) {
        $result = [
            'files' => [],
            'dirs' => [],
        ];
        // Ha van az adott könyvtárban .gitignore fájl, azt be kell olvasni
        $gitignore = null;
        if ($this->disk->exists($directory . '/.gitignore')) {
            $gitignore = \TOGoS_GitIgnore_Ruleset::loadFromString(
                $this->disk->get($directory . '/.gitignore')
            );
        }
        // Az adott könyvtárban lévő fájlok összegyűjtése, majd azon fájlok kiválogatása,
        // amelyeket a gitignore megenged (ha van gitignore)
        $files = $this->disk->files($directory);
        foreach ($files as $file) {
            // Ez a teljes útvonal!
            // Ez az ág ott lényeges, hogy pl. a főmappában a .gitignore-ban van olyan sza-
            // bály, hogy /public/storage, és ezeket a helyi ignore fájlok nem szűrik ki,
            // ezért végig kell nézni a parent gitignore-kat, amiknek a scope-jában lehet ez
            // az aktuális könyvtár is.
            // Itt fontos, hogy a "teljes útvonal" legyen meg a projekten belül, pl. így tudjuk
            // match-elni a fenti /public/storage-s szabályt.
            // A logika ugyanez a könyvtáraknál is.
            $ignoredByParent = false;
            foreach ($parentGitignores as $pg) {
                if ($pg->match($file)) {
                    $ignoredByParent = true;
                    break;
                }
            }
            // Ez csak a fájlnév!
            $base = basename($file);
            if ($ignoredByParent || ($gitignore && $gitignore->match($base)) || in_array($file, $this->config['manualIgnores'])) continue;
            $result['files'][$file] = sha1_file($file);
        }
        // Az adott könyvtárban lévő könyvtárak összegyűjtése, majd azon könyvtárak kiválogatása,
        // amelyeket a gitignore megenged (ha van gitignore)
        $dirs = $this->disk->directories($directory);
        foreach ($dirs as $dir) {
            $ignoredByParent = false;
            foreach ($parentGitignores as $pg) {
                if ($pg->match($dir)) {
                    $ignoredByParent = true;
                    break;
                }
            }
            $base = basename($dir);
            if ($ignoredByParent || ($gitignore && $gitignore->match($base)) || in_array($dir, $this->config['manualIgnores'])) continue;
            $result['dirs'][] = $dir;
            $recursive = $this->parseFiles(
                $directory . '/' . $base,
                $gitignore ? array_merge(array($gitignore), $parentGitignores) : $parentGitignores
            );
            $result['files'] = array_merge($result['files'], $recursive['files']);
            $result['dirs'] = array_merge($result['dirs'], $recursive['dirs']);
        }
        return $result;
    }

    private function handleZipping() {
        $filesToZip = [];

        // A zip fájlok a zipfiles nevű könyvtárba kerülnek
        if (!$this->disk->exists('zipfiles')) {
            $this->disk->makeDirectory('zipfiles');
            $this->info("zipfiles mappa létrehozva a zip fájlok számára");
        }

        // Szükséges adatok összegyűjtése
        $date = Carbon::now('Europe/Budapest')->isoFormat('YMMDD_kkMMssS');
        $neptun = Cache::get('statement_neptun_code');
        $zipName = "./zipfiles/" . $neptun . "_Laravel_" . $date . ".zip";

        // Zippelés
        $zip = new \ZipArchive();
        $zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach (array_keys($this->project['files']) as $file) {
            $zip->addFile($file, $file);
        }
        $zip->close();

        // Méret ellenőrzése, tájékoztatók
        $zipSize = \ByteUnits\bytes($this->disk->size($zipName));
        $this->io->success('A zip fájl elkészült: ' . $zipName . ' (méret: ' . $zipSize->format('kB') . ')');
        $this->io->note('A feladat megfelelő, hiánytalan beadása a hallgató felelőssége, ezért mindenképp ellenőrizd azt, mielőtt beadod!');
        $this->io->note('A legjobb, ha kicsomagolod és telepíted a feladatban látható parancsokkal, hogy minden jól működik-e, mintha az oktatók javítanák!');

        // Túlzottan nagy méret esetén figyelmeztessük a hallgatót, valószínűleg bennehagyott valamit,
        // amire nincs szükség
        if ($zipSize->isGreaterThan(\ByteUnits\Binary::megabytes(2))) {
            $this->io->warning('A zip fájl mérete nagyobb a megszokottnál, kérjük ellenőrid, vannak-e benne felesleges dolgok, pl. képek, stb!');
        } else if ($zipSize->isGreaterThan(\ByteUnits\Binary::megabytes(10))) {
            $this->io->error('A zip fájl mérete JÓVAL nagyobb a megszokottnál, kérjük ellenőrid, vannak-e benne felesleges dolgok, pl. képek, stb!');
        }
        return true;
    }

    private function handleCheck() {
        $error = false;
        $warning = false;

        // Megnézzük, hogy mi a szükséges mappák és a projektben fellelhető mappák metszete
        $commonDirs = array_intersect($this->config['requiredDirs'], $this->project['dirs']);
        // ...és ha ez a metszet nem adja vissza a teljes requiredDirs-t (a szükséges mappákat),
        // akkor bizony hiányzik valami...
        $dirsDiff = array_diff($this->config['requiredDirs'], $commonDirs);
        if (count($dirsDiff) > 0) {
            $error = true;
            $this->io->error([
                'A projekted valószínűleg hiányos, kérjük, hogy ellenőrizd. Ezekre a mappákra szükség van:',
                ...$dirsDiff
            ]);
        }

        // Ugyanaz a logika, mint a mappáknál feljebb
        $commonFiles = array_intersect($this->config['requiredFiles'], array_keys($this->project['files']));
        $filesDiff = array_diff($this->config['requiredFiles'], $commonFiles);
        if (count($filesDiff) > 0) {
            $error = true;
            $this->io->error([
                'A projekted valószínűleg hiányos, kérjük, hogy ellenőrizd. Ezekre a fájlokra szükség van:',
                ...$filesDiff
            ]);
        }

        if (!$error) {
            // Illetéktelen, szükségtelen módosítások ellenőrzése
            $modifiedProtectedFiles = [];
            $projectChecksums = $this->project['files'];
            foreach ($this->config['protectedFiles'] as $file) {
                $hasOW = array_key_exists($file, $this->checksums['overwriteChecksums']);    // has overwrite checksum (magasabb prioritása van)
                $hasLC = array_key_exists($file, $this->checksums['laravelChecksums']);      // has original checksum
                if (array_key_exists($file, $projectChecksums) && ($hasOW || $hasLC)) {
                    $fileChecksum = null;
                    if ($hasLC && !$hasOW) $fileChecksum = $this->checksums['laravelChecksums'][$file];
                    if ($hasOW) $fileChecksum = $this->checksums['overwriteChecksums'][$file];
                    if ($projectChecksums[$file] !== $fileChecksum) {
                        $modifiedProtectedFiles[] = $file;
                    }
                }
            }
            if (count($modifiedProtectedFiles) > 0) {
                $warning = true;
                $this->io->warning([
                    'Úgy érzékeltük, hogy olyan fájlokat is módosítottál, amiket nem vagy egyáltalán nem szükséges módosítani a beadandó elkészítéséhez.',
                    'Ez nem feltétlenül probléma, viszont arra kérünk, győződj meg róla, hogy szándékosan módosítottad őket, és nem pedig valami hiba áll a háttérben! Az érintett fájlok:',
                    ...$modifiedProtectedFiles
                ]);
                //$this->disk->put('mpf.txt', $modifiedProtectedFiles);
            }
        }

        if (!$error && !$warning) {
            $this->io->success('Az előzetes, automatizált ellenőrzéseink szerint a projekted rendben van.');
        }
        return !$error;
    }

    public function handle() {
        $this->io = new SymfonyStyle($this->input, $this->output);
        $this->io->title('Szerveroldali webprogramozás - Automatikus zippelő Laravelhez');

        $this->io->section('1. lépés: Nyilatkozat');
        if ($this->handleStatement()) {
            $this->io->section('2. lépés: Projekt ellenőrzése');
            if ($this->handleCheck()) {
                $this->io->section('3. lépés: Becsomagolás');
                $this->handleZipping();
            }
        }
        /*if ($this->disk->exists('mpf.txt')) {
            $this->disk->delete('mpf.txt');
        }*/
        return 0;
    }
}
