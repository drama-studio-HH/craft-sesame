<?php

namespace thedrama\craftsesame\assetbundles;

use craft\web\AssetBundle;

class FormAsset extends AssetBundle
{

    public function init(): void
    {
        $this->sourcePath = '@thedrama/craftsesame/resources/dist';

        $this->css = [
            'css/form.css',
        ];

        // require the frontend JS to be a module
        $this->js = [
            ['js/form.js', 'type' => 'module']
        ];

        parent::init();
    }
}