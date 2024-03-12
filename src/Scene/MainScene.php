<?php

namespace App\Scene;

use App\Renderer\BackgroundRenderer;
use GameContainer;
use VISU\ECS\EntitiesInterface;
use VISU\Graphics\GLState;
use VISU\Graphics\Rendering\RenderContext;
use VISU\ECS\EntityRegisty;
use App\System\RenderingSystem2D;
use App\System\CameraSystem2D;

class MainScene
{
    private BackgroundRenderer $backgroundRenderer;
    private EntitiesInterface $entities;
    private RenderingSystem2D $renderingSystem;

    public function __construct(
        private GLState $glState,
        private GameContainer $container
    ) {
        $this->entities = new EntityRegisty();

        $this->backgroundRenderer = new BackgroundRenderer($glState);

        $this->renderingSystem = new RenderingSystem2D(
            $this->container->resolveGL(),
        );

        $this->registerSystems();
    }

    public function registerSystems(): void
    {
        $this->renderingSystem->register($this->entities);
    }

    public function render(RenderContext $context): void
    {
        $this->renderingSystem->render($this->entities, $context);
    }

    public function update(): void
    {
        $this->renderingSystem->update($this->entities);
    }
}
