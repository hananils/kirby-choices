![Kirby Choices Methods](.github/title.png)

**Choices** is a plugin for [Kirby 3](https://getkirby.com/) that provides methods to manage field `options` in the frontend. For any field – like select, multiselect or checkboxlist – that offers `options` settings in the blueprint, this plugin will load and return the readable text values for the keys stored in the content file. It works with static options directly set in the blueprint as well as dynamic ones loaded via `query` or `api` settings.

## Example

### Field

```yaml
fields:
    fruits:
        label: Fruits
        type: select
        options:
            apple: Apple
            pear: Pear
            banana: Banana
```

### Content

```yaml
Title: Choices example

----

Fruits: apple
```

### Template

```php
// Will echo "Apple"
<?= $page->fruits()->toChoices() ?>
```

## Installation

### Download

Download and copy this repository to `/site/plugins/choices`.

### Git submodule

```
git submodule add https://github.com/hananils/kirby-choices.git site/plugins/choices
```

### Composer

```
composer require hananils/kirby-choices
```

# Field methods

## toChoices()

Converts the field value to a [Choices collection](#user-content-choices-collection) featuring the text values of the selected options:

```php
$page->fruits()->toChoices();
```

If the method is passed an optional value of `true`, it will return text values for all options specified in the blueprint:

```php
$page->fruits()->toChoices(true);
```

# Choices collection

The Choices collection can be used to loop over all given options and return their values. It offers all methods known to the [default Kirby collection](https://getkirby.com/docs/reference/objects/toolkit/collection) like `first()`, `last()`, `shuffle()`, `sort()` and the like. Additionally, it provides three methods to simplify content output:

## join($separator)

The join method concatenates all field value by a given separator.

-   **`$separator`:** optional separator, uses `, ` as default.

```php
// Will return: Apple, Pear
$page
    ->fruits()
    ->toChoices()
    ->join();
```

If you want to have more control on how to join values – e. g. to have the last item joined by `and` – have a look at the [List methods plugin](https://github.com/hananils/kirby-list-methods).

## missing($required)

The missing method compare the current field values with an array of required values and return the missing ones.

-   **`$required`:** array of required values to be checked.

```php
// Will return: Banana
$page
    ->fruits()
    ->toChoices()
    ->missing(['Apple', 'Banana']);
```

## average($decimals)

The average method will calculate the average of all selected value, useful when handling numeric values.

-   **`$decimals`:** the number of decimals to return.

# License

This plugin is provided freely under the [MIT license](LICENSE.md) by [hana+nils · Büro für Gestaltung](https://hananils.de). We create visual designs for digital and analog media.
