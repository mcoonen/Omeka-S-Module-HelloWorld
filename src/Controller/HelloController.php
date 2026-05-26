<?php
namespace HelloWorld\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

// Create an action controller.
class HelloController extends AbstractActionController
{
    // Define an action "world".
    public function greetAction()
    {
        // Get "message" from the query parameters.
        // In production code, it's a good idea to sanitize user input.
        $name = $this->params()->fromQuery('name', 'Stranger');

        // Pass variables to the view.
        return new ViewModel(['name' => $name]);
    }

    public function greetAdminAction()
    {
        // Get the name configured in the module settings.
        // Note: The base class is 'AbstractActionController' which does not implement `getServiceLocator()`
        // so we have to use the `getServiceManager()` to return a `ServiceLocatorInterface`
        $settings = $this->getEvent()->getApplication()->getServiceManager()->get('Omeka\Settings');
        $name = $settings->get('helloworld_name', 'Placeholder');
        return new ViewModel(['nameFromSettings' => $name]);
    }

    public function indexAction()
    {
        // Get "message" from the query parameters.
        // In production code, it's a good idea to sanitize user input.
        $message = $this->params()->fromQuery('message', 'index');
        // Pass variables to the view.
        return new ViewModel(['name' => 'Stranger']);
    }
}
