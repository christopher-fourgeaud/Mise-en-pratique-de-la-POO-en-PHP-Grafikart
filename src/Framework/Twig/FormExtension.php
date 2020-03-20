<?php

namespace Framework\Twig;

use DateTime;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class FormExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'field',
                [$this, 'field'],
                [
                    'is_safe' => [
                        'html'
                    ],
                    'needs_context' => true
                ]
            )
        ];
    }


    /**
     *
     * Génère le code html d'un champs
     *
     * @param array $context Contexte de la vue Twig
     * @param string $name Nom du champs
     * @param mixed $value Valeur du champs
     * @param string|null $label Label à utiliser
     * @param array $options
     * @return string
     */
    public function field($context, string $name, $value, ?string $label = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $class = 'form-group';
        $error = $this->getErrorHtml($context, $name);
        $value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name' => $name,
            'id' => $name
        ];

        if ($error) {
            $class .= ' has-danger';
            $attributes['class'] .= ' form-control-danger';
        }
        if ($type === 'textarea') {
            $input = $this->textArea($value, $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return "<div class=\"" . $class . "\">
                <label for=\"name\">{$label}</label>
                {$input}
                {$error}
            </div>";
    }

    /**
     * Génère un <input>
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"{$value}\">";
    }

    /**
     * Génère un <textarea>
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function textArea(?string $value, array $attributes): string
    {
        return "<textarea type=\"text\" " . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";
    }

    /**
     * Génère l'HTML en fonction des erreurs du contexte
     * @param $context
     * @param $key
     * @return string
     */
    private function getErrorHtml($context, $name)
    {
        $error = $context['errors'][$name] ?? false;
        if ($error) {
            return "<small class=\"form-text text-muted\">{$error}</small>";
        }
        return "";
    }

    /**
     * Transforme un tableau $clef => $valeur en attribut HTML
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes)
    {
        return implode(
            ' ',
            array_map(
                function ($name, $value) {
                    return "$name=\"$value\"";
                },
                array_keys($attributes),
                $attributes
            )
        );
    }

    private function convertValue($value): string
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string) $value;
    }
}
