<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{

    /**
     * Instance de twig
     *
     * @var Environment
     */
    private $twig;

    /**
     * @var FilesystemLoader
     */
    private $loader;

    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

    /**
     * Permet de rajouter un chemin pour charger les vues
     *
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Permet de rendre une vue
     * Le chemin peut etre précisé avec des namespaces rajoutés via addPath()
     * exemple 1 : $this->render('@blog/view');
     * exemple 2 : $this->render('view');
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Permet de rajouter des variables globales à toutes les vues
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addglobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
