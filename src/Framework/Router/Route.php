<?php

namespace Framework\Router;

/**
 * Class Route
 * Représente la route appelée
 */
class Route
{
    /**
     * Nom de la route
     *
     * @var string
     */
    private $name;

    /**
     * Fonction callback
     *
     * @var callable
     */
    private $callable;

    /**
     * Tableau de paramètre
     *
     * @var array
     */
    private $parameters;


    /**
     * Route constructor
     *
     * @param string $name
     * @param string|callable $callable
     * @param array $parameters
     */
    public function __construct(string $name, $callable, array $parameters)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->parameters = $parameters;
    }

    /**
     * Récupère le nom de la route
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Récupère la fonction callable
     *
     * @return string|callable
     */
    public function getCallback()
    {
        return $this->callable;
    }

    /**
     * Récupère les paramètres de l'url
     *
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
