<?php

namespace Hananils;

use Kirby\Cms\Collection;
use Kirby\Toolkit\Str;
use Kirby\Field\FieldOptions;
use Kirby;
use Closure;

class Choices extends Collection
{
    public function __construct(Kirby\Cms\Field $field, $all = false)
    {
        $key = $field->key();
        $blueprint = $field
            ->parent()
            ->blueprint()
            ->field($key);

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
            if (isset($options[0]['value'])) {
                $associated = [];

                foreach ($options as $option) {
                    $associated[$option['value']] = $option['text'];
                }

                $options = $associated;
            }

            // Get choices
            if ($all) {
                $choices = $options;
            } else {
                // Filter by given field selection
                foreach ($field->split() as $key) {
                    if (isset($options[$key])) {
                        $choices[$key] = $options[$key];
                    } else {
                        $choices[Str::slug($key)] = $key;
                    }
                }
            }
        }

        $this->caseSensitive = true;
        $this->set($choices);
    }

    public function join($separator = ', ')
    {
        return Kirby\Toolkit\A::join($this->data, $separator);
    }

    public function missing($required = [])
    {
        return A::missing($this->data, $required);
    }

    public function average($decimals = 0)
    {
        return A::average($this->data, $decimals);
    }

    public function toArray(Closure $map = null): array
    {
        if ($map !== null) {
            return array_map($map, $this->data);
        }

        return $this->data;
    }

    public function toString(): string
    {
        return $this->join();
    }
}

Kirby::plugin('hananils/choices-methods', [
    'fieldMethods' => [
        'toChoices' => function (Kirby\Cms\Field $field, $all = false) {
            return new Choices($field, $all);
        }
    ]
]);
