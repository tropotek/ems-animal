<?php
namespace An\Listener;

use Tk\Event\Subscriber;
use An\Plugin;

/**
 * Class StartupHandler
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ProfileEditHandler implements Subscriber
{

    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     */
    public function onControllerInit(\Tk\Event\Event $event)
    {
        /** @var \Tk\Controller\Iface $controller */
        $controller = $event->get('controller');
        if ($controller instanceof \App\Controller\Profile\Edit) {
            if ($controller->getCourse() && $controller->getUser()->isStaff()) {
                /** @var \Tk\Ui\Admin\ActionPanel $actionPanel */
                $actionPanel = $controller->getActionPanel();
                $actionPanel->addButton(\Tk\Ui\Button::create('Animal Types',
                    \App\Uri::createHomeUrl('/animalTypeManager.html')->set('profileId', $controller->getCourse()->profileId), 'fa fa-paw'));
            }
        }
    }


    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     */
    public function onControllerShow(\Tk\Event\Event $event) { }


    /**
     * @return array The event names to listen to
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\PageEvents::CONTROLLER_INIT => array('onControllerInit', 0),
            \Tk\PageEvents::CONTROLLER_SHOW => array('onControllerShow', 0)
        );
    }
    
}