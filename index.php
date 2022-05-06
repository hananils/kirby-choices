<?php

namespace Hananils;

use Kirby;
use Kirby\Cms\Collection;
use Kirby\Toolkit\Str;
use Closure;

class Choices extends Collection
{
    public function __construct($field, $all = false)
    {
        $page = $field->parent();
        $key = $field->key();
        $blueprint = $page->blueprint()->field($key);

        $options = [];
        $choices = [];

        if (isset($blueprint['options'])) {
            // Get options
            if ($blueprint['options'] === 'query') {
                $query = $blueprint['query'];
                $options = Kirby\Form\Options::query($query, $page);
            } elseif ($blueprint['options'] === 'api') {
                $api = $blueprint['api'];
                $options = Kirby\Form\Options::api($api, $page);
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
        return A::average($field->value, $decimals);
    }

    public function toArray(Closure $map = null): array
    {
        if ($map !== null) {
            return array_map($map, $this->data);
        }

        return $this->data;
    }
}

Kirby::plugin('hananils/choices-methods', [
    'fieldMethods' => [
        'toChoices' => function ($field, $all = false) {
            return new Choices($field, $all);
        }
    ]
]);
