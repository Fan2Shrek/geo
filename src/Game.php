<?php

namespace App;

use App\Scene\MainScene;
use VISU\Runtime\GameLoopDelegate;
use GameContainer;
use VISU\OS\Window;
use VISU\Graphics\Rendering\PipelineContainer;
use VISU\Graphics\Rendering\RenderPipeline;
use VISU\Graphics\Rendering\RenderContext;
use VISU\Graphics\Rendering\PipelineResources;

class Game implements GameLoopDelegate
{
    private int $frameIndex = 0;
    private bool $booted = false;
    private bool $shouldStop = false;
    private GameContainer $container;
    private Window $window;
    private MainScene $mainScene;
    private PipelineResources $pipelineResources;

    public function __construct(GameContainer $container)
    {
        $this->container = $container;
    }

    public function shouldStop(): bool
    {
        return $this->shouldStop;
    }

    public function update(): void
    {
        $this->window->pollEvents();

        $this->mainScene->update();
    }

    public function render(float $deltaTime): void
    {
        $windowRenderTarget = $this->window->getRenderTarget();

        $data = new PipelineContainer;
        $pipeline = new RenderPipeline($this->pipelineResources, $data, $windowRenderTarget);
        $context = new RenderContext($pipeline, $data, $this->pipelineResources, $deltaTime);

        $this->mainScene->render($context);

        $pipeline->execute(++$this->frameIndex, $this->container->resolveProfiler());
    }

    private function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->window = $this->container->resolveWindowMain();
        $this->window->initailize($this->container->resolveGL());
        $this->window->setSwapInterval(1);
        $this->mainScene = new MainScene($this->container->resolveGL(), $this->container);
        $this->pipelineResources = new PipelineResources($this->container->resolveGL());

        $this->booted = true;
    }

    public function start(): void
    {
        $this->boot();
        $this->container->resolveGameLoopMain()->start();
    }
}
