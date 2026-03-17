<?php

namespace thedrama\craftsesame\assetbundles;

use craft\web\AssetBundle;

class FrontendAsset extends AssetBundle
{

    public function init(): void
    {
        $this->sourcePath = '@thedrama/craftsesame/resources/dist';

        $this->css = [
            'css/frontend.css',
        ];

        // require the frontend JS to be a module
        $this->js = [
            ['js/frontend.js', 'type' => 'module']
        ];

        parent::init();
    }
}