# hw-04

CLI-скрипт для извлечения `title`, `meta[name=description]` и `meta[name=keywords]` из HTML.

## Использование

```sh
php extract.php sample.html
# или
cat sample.html | php extract.php
```

## Выход

JSON вида:

```json
{"title":"...","description":"...","keywords":"..."}
```

## Коды выхода

* `0` — успех;
* `2` — нет ввода или файл недоступен.