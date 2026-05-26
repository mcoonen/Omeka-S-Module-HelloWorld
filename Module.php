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
        // Get the existing value for the fields from the 'setting' database table
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $name = $settings->get('helloworld_name');

        # Return a HTML string with form fields
        return sprintf(
            '<label for="name">Enter a name:</label>
            <input name="my-form-field-name-joepie" value="%s" />',
            htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8')
        );

    }

    /**
     * Handle this module's configuration form.
     *
     * @param AbstractController $controller
     * @return bool False if there was an error during handling
     */

    public function handleConfigForm(AbstractController $controller)
    {
        // Get the field value from the HTTP POST
        $request = $controller->getRequest();
        $name = $request->getPost('my-form-field-name-joepie', '');

        // Write the value to the Omeka 'setting' database table
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $settings->set('helloworld_name', htmlspecialchars($name));

        # Add a message to the Laminas messenger
        $controller->messenger()->addSuccess("Name entered: " . htmlspecialchars($name));
        return true;
    }

}

