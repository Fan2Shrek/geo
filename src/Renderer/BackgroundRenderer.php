<?php

namespace App\Renderer;

use GL\Math\Vec3;
use VISU\Graphics\Texture;
use VISU\Graphics\GLState;
use VISU\Graphics\Rendering\Pass\CallbackPass;
use VISU\Graphics\Rendering\RenderPipeline;
use VISU\Graphics\Rendering\Resource\RenderTargetResource;
use VISU\Graphics\Rendering\RenderPass;
use VISU\Graphics\Rendering\PipelineContainer;
use VISU\Graphics\Rendering\PipelineResources;
use VISU\Graphics\Rendering\Pass\CameraData;
use VISU\Geo\Transform;
use VISU\Graphics\QuadVertexArray;

class BackgroundRenderer
{
    private Texture $backgroundTexture;
    private QuadVertexArray $backgroundVA;

    public function __construct(
        private GLState $glState,
    ) {
        $this->backgroundTexture  = new Texture($glState, 'background');
        $this->backgroundTexture->loadFromFile(VISU_PATH_RESOURCES . '/background.jpg');
        $this->backgroundVA = new QuadVertexArray($glState);
    }

    public function attachPass(RenderPipeline $pipeline, RenderTargetResource $renderTarget): void
    {
        $pipeline->addPass(new CallbackPass(
            'BackgroundPass',
            // setup (we need to declare who is reading and writing what)
            function (RenderPass $pass, RenderPipeline $pipeline, PipelineContainer $data) use ($renderTarget) {
                $pipeline->writes($pass, $renderTarget);
            },
            // execute
            function (PipelineContainer $data, PipelineResources $resources) use ($renderTarget) {
                $resources->activateRenderTarget($renderTarget);

                glDisable(GL_DEPTH_TEST);
                glDisable(GL_CULL_FACE);

                $this->backgroundTexture->bind(GL_TEXTURE0);

                $this->backgroundVA->draw();
            }
        ));
    }
}
