<?php

namespace HelloWorld;

use Omeka\Module\AbstractModule;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\View\Renderer\PhpRenderer;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Get this module's configuration form.
     *
     * @param PhpRenderer $renderer
     * @return string
     */
    public function getConfigForm(PhpRenderer $renderer)
    {
        return '
        <label for="name">Enter a name:</label>
        <input name="foo">
        ';
    }

    /**
     * Handle this module's configuration form.
     *
     * @param AbstractController $controller
     * @return bool False if there was an error during handling
     */

    public function handleConfigForm(AbstractController $controller)
    {
        $request = $controller->getRequest();
        $foo = $request->getPost('foo', '');
        $controller->messenger()->addSuccess("Name entered: " . htmlspecialchars($foo));
        return true;
    }

}

