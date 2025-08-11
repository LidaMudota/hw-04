<?php
final class MetaTagIterator implements Iterator
{
    private DOMDocument $doc;
    private array $nodes = [];
    private int $pos = 0;

    public function __construct(DOMDocument $doc)
    {
        $this->doc = $doc;
        $xpath = new DOMXPath($this->doc);
        $q = <<<X
//title
|
//meta[
  translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')
  = 'description'
  or
  translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')
  = 'keywords'
]
X;
        foreach ($xpath->query($q) as $n) {
            if ($n instanceof DOMNode) {
                $this->nodes[] = $n;
            }
        }
    }

    public function current(): DOMNode
    {
        return $this->nodes[$this->pos];
    }

    public function key(): int
    {
        return $this->pos;
    }

    public function next(): void
    {
        $this->pos++;
    }

    public function rewind(): void
    {
        $this->pos = 0;
    }

    public function valid(): bool
    {
        return isset($this->nodes[$this->pos]);
    }
}
