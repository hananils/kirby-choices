[![Choices for Kirby CMS](header.png)](https://kirby.hananils.de/plugins/choices)

Choices offers methods to resolve selections stored by select, multiselect or checkboxes fields back to their human-readable values. It also gives access to the full list of options defined in the blueprint, either written statically or loaded via `query` or `api` settings.

## Introduction

Choices’ primary usecase is outputting selections and field options on the frontend. For instance, you can use it to show categories and tags or to create classes and data attributes from a selection. It can also be used to resolve selections in `info` or `help` texts inside of panel sections.

Choices is used as shared term for both selection and option lists.

The plugin creates a collection of all choices and allows you to [evaluate](https://kirby.hananils.test/plugins/choices/boolean-methods), [manipulate](https://kirby.hananils.test/plugins/choices/choice-manipulation) and [convert](https://kirby.hananils.test/plugins/choices/conversion) them on the fly.

### Field method



### Basic examples

Given a select field with fruits, either return the selected or all available options in human readable format.

#### Field

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

#### Content

```yaml
Title: Choices example

-----

Fruits: apple, pear
```

#### Output the selected options

```php
// Return selected fruits: "Apple Pear"
<?php foreach ($page->fruits()->toChoices() as $fruit): ?>
    <?= $fruit ?>
<?php endforeach; ?>
```

#### Output all available options

```php
// Return all fruit options: "Apple Pear Banana"
<?php foreach ($page->fruits()->toChoices(all: true) as $fruit): ?>
    <?= $fruit ?>
<?php endforeach; ?>
```

#### Out the selected options as comma-separated list

```php
// Return selected fruits: "Apple, Pear"
<?= $pages->fruits()->toChoices()->join() ?>
```

> [!TIP]
> See the [manipulation guides](https://kirby.hananils.test/plugins/choices/choice-manipulation) to learn how to use Kirby’s string methods on all choices before output. There are also [methods to evaluate](https://kirby.hananils.test/plugins/choices/boolean-methods) the existence or non-existence of a choice and further [options to convert content to another type](https://kirby.hananils.test/plugins/choices/conversion) – for instance, switching between text and value output.

## Installation

By default, plugins in Kirby reside in a special folder located at `/site/plugins`. Each plugin is installed in its proprietary subfolder. This installation can be handled in four different ways: you can either install them manually or manage them using Kirby CLI, Git submodules or Composer. You can install Choices either way and should choose the method suiting your project best.

Please note that all examples given here assume you are using the default plugin root. [If you changed your plugin root](https://getkirby.com/docs/reference/system/roots/plugins), e. g. with a custom folder setup, you’ll also have to adjust the paths given in this guide. For further information on how to manage plugins, please read the [official Kirby plugin introduction](https://getkirby.com/docs/guide/plugins/plugin-basics).

### Download

Download and copy this repository to `/site/plugins/choices`.

### Kirby CLI

```shell
kirby plugin:install hananils/kirby-choices
```

### Git submodule

```bash
git submodule add \
    https://github.com/hananils/kirby-choices.git \
    site/plugins/choices
```

### Composer

```shell
composer require hananils/kirby-choices
```

## Documentation

[![Find all documentation at kirby.hananils.de](footer.png)](https://kirby.hananils.de/plugins/choices)

Where possible, files contain inline annotations. For extended documentation, please visit our dedicated plugin site at [kirby.hananils.de/​plugins/​choices](https://kirby.hananils.de/plugins/choices).

### Guides

- [Evaluation](https://kirby.hananils.de/plugins/choices/boolean-methods)
- [Manipulation](https://kirby.hananils.de/plugins/choices/choice-manipulation)
- [Conversion](https://kirby.hananils.de/plugins/choices/conversion)

### Reference

- [Field Methods](https://kirby.hananils.de/plugins/choices/field-methods)

## License

This plugin is provided freely under the [MIT license](https://kirby.hananils.de/plugins/choices/license) by [hana+nils · Büro für Gestaltung](https://kirby.hananils.de). We create visual designs for digital and analog media.