<?php

namespace App\EventSubscriber;

use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ClearCartSubscriber
 * @package App\EventSubscriber;
 */
class ClearCartSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }

    /**
     * Removes all items from the cart when the clear button is clicked.
     * 
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event): void
    {
        // The current cart is available from the main form by using the FormEvent::getData() method
        $form = $event->getForm();
        $cart = $form->getData();

        //It should be an instance of an Order entity
        if (!$cart instanceof Order) {
            return;
        }

        // Is the clear button clicked?
        if (!$form->get('clear')->isClicked()) {
            return;
        }

        // Clears the cart
        $cart->removeItems();
    }
}
