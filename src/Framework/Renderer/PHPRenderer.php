<?php

namespace Framework\Renderer;

class PHPRenderer implements RendererInterface
{
    /**
     * Tableau de nos chemins
     *
     * @var array
     */
    private $paths = [];

    /**
     * Variables globalement accessible pour toutes les vues
     *
     * @var array
     */
    private $globals = [];

    const DEFAULT_NAMESPACE = '__MAIN';

    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
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
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
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
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
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
        $this->globals[$key] = $value;
    }

    /**
     * Retourne True si la vue possède un namespace
     *
     * @param string $view
     * @return boolean
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * Retourne le namespace de la route.
     *
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        // Retourne le chemin jusqu'au premier slash (/) en sautant l'arobase (@)
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * Remplace le nom de la vue par son Namespace
     *
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
