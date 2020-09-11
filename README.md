# Kirby Choices Methods

Kirby 3 plugin to get selected choices from any field that provides options.

## Installation

### Download

Download and copy this repository to `/site/plugins/choices-methods`.

### Git submodule

```
git submodule add https://github.com/hananils/kirby-choices-methods.git site/plugins/choices-methods
```

### Composer

```
composer require hananils/kirby-choices-methods
```

## Field methods

Field methods can be called on any field offering options.

### toChoices()

Converts the field value to an array featuring the text values of all selected options.

```php
$page->tags()->toChoices();
```

### Array methods

In order to adjust the output the plugin offers additional [array methods](https://getkirby.com/docs/reference/tools/a) like `join()`, `get()`, `first()`, `last()`, `shuffle()`, `sort()`, `missing()` and `average()`.

This plugin works nicely with [list methods](https://github.com/hananils/kirby-list-methods).

## License

MIT

## Credits

[hana+nils · Büro für Gestaltung](https://hananils.de)
