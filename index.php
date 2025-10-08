<?php

namespace Hananils;

@include_once __DIR__ . '/vendor/autoload.php';

use Closure;
use Kirby\Cms\App as Kirby;
use Kirby\Cms\Collection;
use Kirby\Toolkit\Str;
use Kirby\Content\Field;
use Kirby\Field\FieldOptions;
use Kirby\Toolkit\A;

/**
 * A Choices collection to retrieve field options.
 */
class Choices extends Collection implements \Stringable
{
    /**
     * Given a field with options, this class retrieves all choices made by the
     * editors or – if the `all` parameter is set – of all options defined for
     * this field.
     *
     * In case of nested fields, e. g. when used inside a structure field,
     * the name of the parent field must be passed as context in order to
     * retrieve the choices.
     *
     * Choices extends the default Kirby Collection class and thus offers all
     * methods known from other Kirby object like `first()`, `last()`,
     * `shuffle()`, `sort()` and the like.
     *
     * @param $field The field holding the choices.
     * @param $all Whether to include all defined choices or not.
     * @param $context The name of the parent, if the field is nested.
     */
    public function __construct(
        Field $field,
        bool $all = false,
        null|string $context = null
    ) {
        $key = $field->key();

        // Field is nested, e. g. in a structure
        if ($context) {
            $contextField = $field
                ->parent()
                ->blueprint()
                ->field($context);
            $blueprint = $contextField['fields'][$key];
        } else {
            $blueprint = $field
                ->parent()
                ->blueprint()
                ->field($key);
        }

        $options = [];
        $choices = [];

        if (isset($blueprint['options'])) {
            // Get options
            if (isset($blueprint['options']['type'])) {
                $fieldOptions = FieldOptions::factory($blueprint['options']);
                $options = $fieldOptions->render($field->model());
            } else {
                $options = $blueprint['options'];
            }

            // Create associative array
            if (isset($options[0]['text'])) {
                $associated = [];

                foreach ($options as ['value' => $value, 'text' => $text]) {
                    if ($value === $text) {
                        // If value and text are the same, it can be assumed that
                        // no specific value has been set in the blueprint
                        $value = Str::slug($value);
                    }

                    $associated[$value] = $text;
                }

                $options = $associated;
            }

            // Get choices
            if ($all) {
                $choices = $options;
            } else {
                // Filter by given selection
                foreach ($field->split() as $text) {
                    if ($blueprint['type'] === 'color') {
                        $value = $text;
                        $text = $options[$text];
                    } elseif ($key = array_search($text, $options)) {
                        // Find choice by key
                        $value = $key;
                    } elseif (isset($options[$text])) {
                        // Find choice by text
                        $value = Str::slug($text);
                        $text = $options[$text];
                    } else {
                        // Fallback for unknown choices
                        $value = Str::slug($text);
                    }

                    $choices[$value] = $text;
                }
            }
        }

        $this->caseSensitive = true;
        $this->set($choices);
    }

    /**
     * Extracts an attribute value from the given element
     * in the collection. This is useful if elements in the collection
     * might be objects, arrays or anything else and you need to
     * get the value independently from that. We use it for `filter`.
     */
    public function getAttribute(
        $item,
        $attribute,
        $split = false,
        $related = null
    ) {
        $value = $this->{'getAttributeFrom' . gettype($item)}(
            $item,
            $attribute
        );

        if ($split !== false) {
            return Str::split($value, $split === true ? ',' : $split);
        }

        if ($related !== null) {
            return Str::toType((string) $value, $related);
        }

        return $value;
    }

    /**
     * Returns either the value or text representation of a choice item.
     * Used for filtering.
     *
     * @param $text The item text.
     * @param $attribute The item representation, either `value` or `text`.
     */
    protected function getAttributeFromString(
        string $text,
        string $attribute
    ): string|null {
        return match ($attribute) {
            'text' => $text,
            'value' => array_search($text, $this->data),
            default => null
        };
    }

    /**
     * Checks if a value or text is present in the choices.
     *
     * @param string $key The value or text to look up.
     */
    public function has($key): bool
    {
        if (!$key || !is_string($key)) {
            return false;
        }

        return array_search($key, $this->data) !== false ||
            isset($this->data[$key]);
    }

    /**
     * Checks if any of the given values or texts is present in the choices.
     *
     * @param array $choices The values or texts to look up.
     */
    public function hasAny(array $choices): bool
    {
        $valueIntersection = array_intersect($choices, $this->toValues());
        $textIntersection = array_intersect($choices, $this->toTexts());

        return !empty($valueIntersection) || !empty($textIntersection);
    }

    /**
     * Checks if all of the given values or texts are present in the choices.
     *
     * @param array $choices The values or texts to look up.
     */
    public function hasAll(array $choices): bool
    {
        $all = true;
        $values = $this->toValues();
        $texts = $this->toTexts();

        foreach ($choices as $choice) {
            if (!in_array($choice, $values) && !in_array($choice, $texts)) {
                $all = false;
                break;
            }
        }

        return $all;
    }

    /**
     * Checks if all of the given values or texts are present in the choices.
     * This is an alias for `hasAll` which also except as single value as string.
     *
     * @param $choices The values or texts to look up.
     */
    public function includes(array|string $choices): bool
    {
        if (is_string($choices)) {
            $choices = [$choices];
        }

        return $this->hasAll($choices);
    }

    /**
     * Checks if none of the given values or texts are present in the choices.
     * This is the negation of `includes`.
     *
     * @param $choices The values or texts to look up.
     */
    public function excludes(array|string $choices): bool
    {
        return !$this->contains($choices);
    }

    /**
     * Joins all choices with a separator.
     *
     * @param $separator The separator to be used when joining choices.
     * @param $as Either a Closure to convert the choices or `true` for values and `false|null` for texts.
     */
    public function join(
        string $separator = ', ',
        Closure|null|bool $as = null
    ): string {
        return implode($separator, $this->toArray($as));
    }

    /**
     * Returns an array of missing choices.
     *
     * @param $required Array of required choices.
     */
    public function missing(array $required = []): array
    {
        return A::missing($this->data, $required);
    }

    /**
     * Returns the average value of all choices.
     *
     * @param $decimals The decimal precision.
     */
    public function average(int $decimals = 0): self
    {
        $this->data = [A::average($this->data, $decimals)];

        return $this;
    }

    /**
     * Converts all texts to lowercase.
     */
    public function lower(): self
    {
        return $this->map('Kirby\Toolkit\Str::lower');
    }

    /**
     * Converts all texts to uppercase.
     */
    public function upper(): self
    {
        return $this->map('Kirby\Toolkit\Str::upper');
    }

    /**
     * Converts all texts to uppercase first.
     */
    public function ucfirst(): self
    {
        return $this->map('Kirby\Toolkit\Str::ucfirst');
    }

    /**
     * Converts all texts to uppercase words.
     */
    public function ucwords(): self
    {
        return $this->map('Kirby\Toolkit\Str::ucwords');
    }

    /**
     * Converts all texts to slugs.
     *
     * @param $separator The separator.
     * @param $allowed The allowed characters.
     * @param $maxlength The maximum character count.
     */
    public function slug(
        null|string $separator = null,
        null|string $allowed = null,
        int $maxlength = 128
    ): self {
        return $this->map(function ($text) use (
            $separator,
            $allowed,
            $maxlength
        ) {
            return Str::slug($text, $separator, $allowed, $maxlength);
        });
    }

    /**
     * Converts all texts to snake case.
     *
     * @param $delimiter The delimiter.
     */
    public function snake(string $delimiter = '_'): self
    {
        return $this->map(function ($text) use ($delimiter) {
            return Str::snake($text, $delimiter);
        });
    }

    /**
     * Converts all texts to studly case.
     */
    public function studly(): self
    {
        return $this->map('Kirby\Toolkit\Str::studly');
    }

    /**
     * Converts all texts to camel case.
     */
    public function camel(): self
    {
        return $this->map('Kirby\Toolkit\Str::camel');
    }

    /**
     * Converts all texts from camel to kebab case.
     */
    public function camelToKebab(): self
    {
        return $this->map('Kirby\Toolkit\Str::camelToKebab');
    }

    /**
     * Converts all texts to kebab case.
     */
    public function kebab(): self
    {
        return $this->map('Kirby\Toolkit\Str::kebab');
    }

    /**
     * Converts all texts from kebab to camel case.
     */
    public function kebabToCamel(): self
    {
        return $this->map('Kirby\Toolkit\Str::kebabToCamel');
    }

    /**
     * Converts the object into an array. For compatibility reasons this
     * allows for passing `true` and `false` to either receive an array of texts
     * or values.
     */
    public function toArray(Closure|null|bool $map = null): array
    {
        return match ($map) {
            null, false => $this->toTexts(),
            true => $this->toValues(),
            default => array_map($map, $this->toTexts())
        };
    }

    /**
     * Returns the value representation of all choices.
     */
    public function toValues(): array
    {
        return array_keys($this->data);
    }

    /**
     * Returns the text representation of all choices.
     */
    public function toTexts(): array
    {
        return array_values($this->data);
    }

    /**
     * Returns all choices as string using the default separator.
     */
    public function toString(): string
    {
        return $this->join();
    }

    /**
     * Magic method that returns all choices as string using the default separator.
     */
    public function __toString(): string
    {
        return $this->join();
    }
}

Kirby::plugin('hananils/choices', [
    'fieldMethods' => [
        /**
         * Given a field with options, this method returns a collection of all
         * choices made by the editors or – if the `all` parameter is set –
         * of all options defined for this field.
         *
         * In case of nested fields, e. g. when used inside a structure field,
         * the name of the parent field must be passed as context in order to
         * retrieve the choices.
         *
         * Choices extends the default Kirby Collection class and thus offers all
         * methods known from other Kirby object like `first()`, `last()`,
         * `shuffle()`, `sort()` and the like.
         *
         * @param $field The field holding the choices.
         * @param $all Whether to include all defined choices or not.
         * @param $context The name of the parent, if the field is nested.
         */
        'toChoices' => function (
            Field $field,
            bool $all = false,
            null|string $context = null
        ): Choices {
            return new Choices($field, $all, $context);
        }
    ]
]);
