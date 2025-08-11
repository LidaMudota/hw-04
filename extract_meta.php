<?php
require __DIR__ . '/MetaTagIterator.php';

$inputFile = __DIR__ . '/input.html';
$html = file_get_contents($inputFile);

libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
libxml_clear_errors();

$it = new MetaTagIterator($doc);

$extracted = [
    'title' => null,
    'description' => null,
    'keywords' => null,
];

foreach ($it as $node) {
    if ($node->nodeName === 'title') {
        $extracted['title'] = trim($node->textContent);
    } elseif ($node->nodeName === 'meta') {
        $name = strtolower((string)($node->attributes->getNamedItem('name')?->nodeValue));
        if ($name === 'description' || $name === 'keywords') {
            $content = (string)($node->attributes->getNamedItem('content')?->nodeValue);
            $extracted[$name] = trim($content);
        }
    }
    if ($node->parentNode) {
        $node->parentNode->removeChild($node);
    }
}

file_put_contents(__DIR__ . '/meta.json', json_encode($extracted, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents(__DIR__ . '/clean.html', $doc->saveHTML());

echo "Извлечено:\n";
foreach ($extracted as $k => $v) {
    echo " - {$k}: " . ($v === null ? '(нет)' : $v) . "\n";
}
echo "\nГотово. Смотри:\n - meta.json\n - clean.html\n";
