<?php
declare(strict_types=1);

/**
 * HW-04: извлечение title/description/keywords через ИТЕРАЦИИ.
 * - Генератор iterNodes() обходит DOMNodeList лениво.
 * - Без сторонних пакетов, без regex.
 */

function readInput(?string $path): string {
    if ($path !== null && $path !== '-') {
        if (!is_file($path) || !is_readable($path)) {
            fwrite(STDERR, "File not found or not readable: {$path}\n");
            exit(2);
        }
        $s = file_get_contents($path);
        return $s === false ? '' : $s;
    }
    $s = stream_get_contents(STDIN);
    return $s === false ? '' : $s;
}

/** @return Generator<DOMNode> */
function iterNodes(DOMNodeList $list): Generator {
    for ($i = 0; $i < $list->length; $i++) {
        $node = $list->item($i);
        if ($node !== null) {
            yield $node; // ЛЕНИВАЯ итерация по NodeList
        }
    }
}

$path = $argv[1] ?? null;
$html = readInput($path);
if (trim($html) === '') {
    fwrite(STDERR, "Empty HTML input.\n");
    exit(2);
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();

// Безопасные опции парсинга; устойчивость к «грязному» HTML:
$opts = LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_COMPACT;
$dom->loadHTML($html, $opts);
libxml_clear_errors();

$head = $dom->getElementsByTagName('head')->item(0) ?? $dom;

// TITLE (первая непустая)
$title = null;
foreach (iterNodes($head->getElementsByTagName('title')) as $n) {
    /** @var DOMNode $n */
    $t = trim((string)$n->textContent);
    if ($t !== '') { $title = $t; break; }
}

// META (description/keywords) через ту же итерацию
$description = null;
$keywords    = null;

foreach (iterNodes($head->getElementsByTagName('meta')) as $node) {
    if (!($node instanceof DOMElement)) continue;
    $name = strtolower((string)$node->getAttribute('name'));
    if ($name === 'description' && $description === null) {
        $val = trim((string)$node->getAttribute('content'));
        if ($val !== '') $description = $val;
    } elseif ($name === 'keywords' && $keywords === null) {
        $val = trim((string)$node->getAttribute('content'));
        if ($val !== '') $keywords = $val;
    }
    if ($description !== null && $keywords !== null) break;
}

echo json_encode(
    ['title' => $title, 'description' => $description, 'keywords' => $keywords],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
) . PHP_EOL;