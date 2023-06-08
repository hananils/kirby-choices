<?php

namespace Hananils;

use Kirby\Cms\Collection;
use Kirby\Toolkit\Str;
use Kirby\Cms\Field;
use Kirby\Field\FieldOptions;
use Kirby;
use Closure;

class Choices extends Collection
{
    public function __construct(Field $field, $all = false, $context = null)
    {
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

    public function has($key): bool
    {
        if (empty($key) || !is_string($key)) {
            return false;
        }

        return parent::has($key);
    }

    public function join($separator = ', '): string
    {
        return Kirby\Toolkit\A::join($this->data, $separator);
    }

    public function missing($required = []): array
    {
        return A::missing($this->data, $required);
    }

    public function average($decimals = 0): mixed
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

    public function __toString(): string
    {
        return $this->join();
    }
}

Kirby::plugin('hananils/choices', [
    'fieldMethods' => [
        'toChoices' => function (Field $field, $all = false, $context = null) {
            return new Choices($field, $all, $context);
        }
    ]
]);
