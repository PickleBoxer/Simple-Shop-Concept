<?php

namespace App\EventSubscriber;

use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class RemoveCartItemSubscriber
 * @package App\EventSubscriber;
 */
class RemoveCartItemSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }

    /**
     * Removes items from the cart based on the data sent from the user.
     * method will be invoked after the form is submitted
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

        // Removes items from the cart
        foreach ($form->get('items')->all() as $child) {
            if ($child->get('remove')->isClicked()) {
                $cart->removeItem($child->getData());
                break;
            }
        }
    }
}
