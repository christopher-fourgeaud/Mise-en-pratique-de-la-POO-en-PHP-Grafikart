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
     * Undocumented variable
     *
     * @var callable
     */
    private $callable;

    /**
     * Undocumented variable
     *
     * @var array
     */
    private $parameters;


    public function __construct(string $name, callable $callable, array $parameters)
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
     * @return callable
     */
    public function getCallback(): callable
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
