<?php

namespace Framework\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

/**
 * Série d'extensions concernant les textes
 *
 * Class TextExtension
 */
class TextExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    /**
     * Renvoie un extrait d'un contenu sans couper le dernier mot
     *
     * @param string $content
     * @param integer $maxLength
     * @return string
     */
    public function excerpt(?string $content, int $maxLength = 100): string
    {
        if (is_null($content)) {
            return '';
        }
        if (mb_strlen($content) > $maxLength) {
            // Stocke les X premiers caractères de mon contenu
            $excerpt = mb_substr($content, 0, $maxLength);

            // Enregistre la position du dernier espace(' ') de mon contenu
            $lastSpace = mb_strrpos($excerpt, ' ');

            // Renvoi le contenu en retirant le dernier espace
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
