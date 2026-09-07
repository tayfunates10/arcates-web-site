<?php
/**
 * Arcates Web Site — test calistirici
 *
 * Composer yoktur. Bu dosya `test()`, `assertTrue()`, `assertSame()` ve
 * kardes yardimcilarini sunar. Basarisizlikta cikis kodu 1 doner, boylece CI
 * kirilir.  DOCS.md 14.1
 *
 * Kullanim:
 *   php tests/run.php              tum gruplar
 *   php tests/run.php unit         yalnizca tests/unit
 *   php tests/run.php security functional
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Istanbul');

define('ARC_ROOT', dirname(__DIR__));
define('ARC_TESTING', true);

require ARC_ROOT . '/app/autoload.php';
require __DIR__ . '/helpers.php';

/** Calistirici durumu. */
final class TestRunner
{
    public static array $results = [];
    public static string $group = '';
    public static ?string $current = null;
    public static array $assertions = [];

    public static function record(bool $ok, string $message): void
    {
        self::$assertions[] = ['ok' => $ok, 'message' => $message];
    }
}

/**
 * Tek bir testi tanimlar ve hemen calistirir.
 *
 * @param string   $id   Test numarasi, ornegin "U-01".
 * @param string   $name Insan okunur aciklama.
 * @param callable $fn   Govde. Iddialar icin assert* yardimcilarini kullanir.
 */
function test(string $id, string $name, callable $fn): void
{
    TestRunner::$current    = $id;
    TestRunner::$assertions = [];

    $skipped = false;
    $error   = null;

    try {
        $fn();
    } catch (SkippedTest $e) {
        $skipped = true;
        $error   = $e->getMessage();
    } catch (Throwable $e) {
        $error = get_class($e) . ': ' . $e->getMessage()
            . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';
    }

    $failed = [];
    foreach (TestRunner::$assertions as $a) {
        if (!$a['ok']) {
            $failed[] = $a['message'];
        }
    }

    if ($skipped) {
        $status = 'skip';
    } elseif ($error !== null || $failed) {
        $status = 'fail';
    } elseif (!TestRunner::$assertions) {
        $status = 'fail';
        $failed[] = 'Hiçbir iddia çalıştırılmadı.';
    } else {
        $status = 'pass';
    }

    TestRunner::$results[] = [
        'group'  => TestRunner::$group,
        'id'     => $id,
        'name'   => $name,
        'status' => $status,
        'error'  => $error,
        'failed' => $failed,
        'count'  => count(TestRunner::$assertions),
    ];

    TestRunner::$current = null;
}

/** Testi atlanmis olarak isaretler (ornegin veritabani yoksa). */
final class SkippedTest extends RuntimeException
{
}

function skip(string $reason): void
{
    throw new SkippedTest($reason);
}

function assertTrue(mixed $value, string $message = 'Değer doğru olmalı'): void
{
    TestRunner::record($value === true, $message . ' — gelen: ' . arc_dump($value));
}

function assertFalse(mixed $value, string $message = 'Değer yanlış olmalı'): void
{
    TestRunner::record($value === false, $message . ' — gelen: ' . arc_dump($value));
}

function assertSame(mixed $expected, mixed $actual, string $message = 'Değerler aynı olmalı'): void
{
    TestRunner::record(
        $expected === $actual,
        $message . ' — beklenen: ' . arc_dump($expected) . ', gelen: ' . arc_dump($actual)
    );
}

function assertNotSame(mixed $expected, mixed $actual, string $message = 'Değerler farklı olmalı'): void
{
    TestRunner::record($expected !== $actual, $message . ' — her ikisi: ' . arc_dump($actual));
}

function assertContains(string $needle, string $haystack, string $message = 'Metin içermeli'): void
{
    TestRunner::record(
        str_contains($haystack, $needle),
        $message . ' — aranan: ' . arc_dump($needle) . ', metin: ' . arc_dump(mb_substr($haystack, 0, 300))
    );
}

function assertNotContains(string $needle, string $haystack, string $message = 'Metin içermemeli'): void
{
    TestRunner::record(
        !str_contains($haystack, $needle),
        $message . ' — istenmeyen: ' . arc_dump($needle) . ', metin: ' . arc_dump(mb_substr($haystack, 0, 300))
    );
}

function assertCount(int $expected, array|Countable $value, string $message = 'Öge sayısı eşleşmeli'): void
{
    TestRunner::record(count($value) === $expected, $message . ' — beklenen: ' . $expected . ', gelen: ' . count($value));
}

function assertGreaterThan(int|float $limit, int|float $value, string $message = 'Değer büyük olmalı'): void
{
    TestRunner::record($value > $limit, $message . ' — sınır: ' . $limit . ', gelen: ' . $value);
}

function assertLessThan(int|float $limit, int|float $value, string $message = 'Değer küçük olmalı'): void
{
    TestRunner::record($value < $limit, $message . ' — sınır: ' . $limit . ', gelen: ' . $value);
}

/** Verilen govde beklenen istisnayi firlatmali. */
function assertThrows(string $class, callable $fn, string $message = 'İstisna beklendi'): void
{
    try {
        $fn();
    } catch (Throwable $e) {
        TestRunner::record($e instanceof $class, $message . ' — gelen: ' . get_class($e));
        return;
    }
    TestRunner::record(false, $message . ' — hiçbir istisna fırlatılmadı');
}

function arc_dump(mixed $v): string
{
    if (is_string($v)) {
        return "'" . $v . "'";
    }
    if (is_bool($v)) {
        return $v ? 'true' : 'false';
    }
    if ($v === null) {
        return 'null';
    }
    if (is_array($v)) {
        return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: 'array';
    }
    if (is_object($v)) {
        return get_class($v);
    }
    return (string) $v;
}

// --- Calistirma -------------------------------------------------------------

$groups = array_slice($argv, 1);
if (!$groups) {
    $groups = ['unit', 'security', 'functional'];
}

$started = microtime(true);

foreach ($groups as $group) {
    $dir = __DIR__ . '/' . $group;
    if (!is_dir($dir)) {
        fwrite(STDERR, "Uyarı: '{$group}' grubu bulunamadı.\n");
        continue;
    }
    TestRunner::$group = $group;
    $files = glob($dir . '/*.php') ?: [];
    sort($files);
    foreach ($files as $file) {
        require $file;
    }
}

$elapsed = round((microtime(true) - $started) * 1000);

$pass = $fail = $skipCount = 0;
$byGroup = [];
foreach (TestRunner::$results as $r) {
    $byGroup[$r['group']][] = $r;
    match ($r['status']) {
        'pass' => $pass++,
        'fail' => $fail++,
        default => $skipCount++,
    };
}

$colour = static function (string $text, string $code): string {
    return (PHP_SAPI === 'cli' && getenv('NO_COLOR') === false) ? "\033[{$code}m{$text}\033[0m" : $text;
};

echo "\nArcates test çalıştırıcı\n";
echo str_repeat('=', 66), "\n";

foreach ($byGroup as $group => $rows) {
    echo "\n", strtoupper($group), "\n", str_repeat('-', 66), "\n";
    foreach ($rows as $r) {
        $mark = match ($r['status']) {
            'pass' => $colour('  GEÇTİ', '32'),
            'fail' => $colour('KALDI  ', '31'),
            default => $colour('ATLANDI', '33'),
        };
        printf("%s  %-7s %s\n", $mark, $r['id'], $r['name']);
        if ($r['status'] === 'fail') {
            if ($r['error']) {
                echo "           ! ", $r['error'], "\n";
            }
            foreach ($r['failed'] as $f) {
                echo "           ! ", $f, "\n";
            }
        }
        if ($r['status'] === 'skip' && $r['error']) {
            echo "           ~ ", $r['error'], "\n";
        }
    }
}

echo "\n", str_repeat('=', 66), "\n";
printf(
    "Toplam %d test — %s geçti, %s kaldı, %s atlandı (%d ms)\n\n",
    count(TestRunner::$results),
    $colour((string) $pass, '32'),
    $colour((string) $fail, $fail ? '31' : '0'),
    $colour((string) $skipCount, '33'),
    $elapsed
);

exit($fail > 0 ? 1 : 0);
