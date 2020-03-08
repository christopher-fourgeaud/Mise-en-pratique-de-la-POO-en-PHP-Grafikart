<?php

namespace Framework\Renderer;

interface RendererInterface
{

    /**
     * Permet de rajouter un chemin pour charger les vues
     *
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;

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
    public function render(string $view, array $params = []): string;

    /**
     * Permet de rajouter des variables globales à toutes les vues
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addglobal(string $key, $value): void;
}
