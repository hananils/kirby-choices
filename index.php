<?php

Kirby::plugin('hananils/choices-methods', [
    'fieldMethods' => [
        'toChoices' => function ($field) {
            $page = $field->parent();
            $key = $field->key();
            $blueprint = $page->blueprint()->field($key);

            if (!isset($blueprint['options'])) {
                return $field;
            }

            // Get options
            if ($blueprint['options'] === 'query') {
                $options = Kirby\Form\Options::query(
                    $blueprint['query'],
                    $page
                );
            } elseif ($blueprint['options'] === 'api') {
                $options = Kirby\Form\Options::api($blueprint['api'], $page);
            } else {
                $options = $blueprint['options'];
            }

            if (count($options) && $options[0]['value']) {
                $associated = [];
                foreach ($options as $option) {
                    $associated[$option['value']] = $option['text'];
                }
                $options = $associated;
            }

            // Get choices
            $choices = [];
            foreach ($field->split() as $key) {
                // dump($options[$key]);
                if (isset($options[$key])) {
                    $choices[] = $options[$key];
                } else {
                    $choices[] = $key;
                }
            }

            if ($choices) {
                $field->value = $choices;
            } else {
                $field->value = [];
            }

            return $field;
        },
        'join' => function ($field, $separator = ', ') {
            if (!is_array($field->value)) {
                return $field;
            }

            $field->value = Kirby\Toolkit\A::join($field->value, $separator);

            return $field;
        },
        'get' => function ($field, $key, $default = null) {
            if (!is_array($field->value)) {
                return $field;
            }

            $field->value = A::get($field->value, $key, $default);

            return $field;
        },
        'first' => function ($field) {
            if (!is_array($field->value)) {
                return $field;
            }

            $field->value = A::first($field->value);

            return $field;
        },
        'last' => function ($field) {
            if (!is_array($field->value)) {
                return $field;
            }

            $field->value = A::last($field->value);

            return $field;
        },
        'shuffle' => function ($field) {
            if (!is_array($field->value)) {
                return $field;
            }

            $field->value = A::shuffle($field->value);

            return $field;
        },
        'sort' => function ($field, $column, $direction = 'desc', $method = 0) {
            if (!is_array($field->value)) {
                return $field;
            }

            $field->value = A::sort(
                $field->value,
                $column,
                $direction,
                $method
            );

            return $field;
        },
        'missing' => function ($field, $required = []) {
            if (!is_array($field->value)) {
                return $field;
            }

            $field->value = A::missing($field->value, $required);

            return $field;
        },
        'average' => function ($field, $decimals = 0) {
            if (!is_array($field->value)) {
                return $field;
            }

            $field->value = A::average($field->value, $decimals);

            return $field;
        },
    ],
]);
