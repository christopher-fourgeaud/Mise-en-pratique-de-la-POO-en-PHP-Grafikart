<?php

namespace App\Admin;

use Framework\Renderer\RendererInterface;

class DashboardAction
{

    /**
     * Instance de RendererIterface
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Tableau des widgets
     *
     * @var AdminWidgetInterface[]
     */
    private $widgets;

    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    public function __invoke()
    {
        $widgets = array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->render();
        }, '');
        return $this->renderer->render('@admin/dashboard', [
            "widgets" => $widgets
        ]);
    }
}
