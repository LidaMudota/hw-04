# Извлечение мета‑тегов

Скрипт `extract_meta.php` удаляет из `input.html` теги `<title>`,
`<meta name="description">` и `<meta name="keywords">`. Значения
тегов сохраняются в `meta.json`, а очищённый HTML в `clean.html`.

## Запуск

```sh
php extract_meta.php
```

После выполнения появятся файлы:

* `meta.json` — извлечённые значения мета‑тегов в формате JSON;
* `clean.html` — исходный HTML без удалённых тегов.