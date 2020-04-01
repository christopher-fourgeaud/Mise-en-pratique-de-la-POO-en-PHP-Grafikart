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
        } elseif ($type === 'file') {
            $input = $this->file($attributes);
        } elseif ($type === 'checkbox') {
            $input = $this->checkbox($value, $attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
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
     * Génère un <input type="checkbox">
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function checkbox(?string $value, array $attributes): string
    {
        $html = '<input type="hidden" name="' . $attributes['name'] . '" value="0">';
        if ($value) {
            $attributes['checked'] = true;
        }
        return $html . "<input type=\"checkbox\" " . $this->getHtmlFromArray($attributes) . " value=\"1\">";
    }

    /**
     * Génère un <input type="file">
     *
     * @param array $attributes
     * @return string
     */
    private function file(array $attributes): string
    {
        return "<input type=\"file\" " . $this->getHtmlFromArray($attributes) . ">";
    }

    /**
     * Génère un select
     *
     * @param string|null $value
     * @param array $options
     * @param array $attributes
     * @return void
     */
    private function select(?string $value, array $options, array $attributes): string
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");
        return "<select " . $this->getHtmlFromArray($attributes) . ">$htmlOptions</select>";
    }

    /**
     * Génère un <textarea>
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function textArea(?string $value, array $attributes): string
    {
        return "<textarea rows='10' type=\"text\" " . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";
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
        $htmlParts = [];
        foreach ($attributes as $name => $value) {
            if ($value === true) {
                $htmlParts[] = (string) $name;
            } elseif ($value !== false) {
                $htmlParts[] = "$name=\"$value\"";
            }
        }
        return implode(' ', $htmlParts);
    }

    private function convertValue($value): string
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string) $value;
    }
}
