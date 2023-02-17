<?php
// src/EventSubscriber/LoginSuccessSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class LoginSuccessSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    const CART_KEY_NAME = 'cart_id';

    /**
     * CartSessionStorage constructor.
     *
     * @param RequestStack $requestStack
     * 
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator,RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [LoginSuccessEvent::class => 'onLogIn'];
    }

    public function onLogIn(LoginSuccessEvent $event): void
    {
        // get the user
        $user = $event->getUser();

        // get cart
        $cart = $user->getCart();

        // if user have cart -> Sets the cart in session.
        if ($cart) {
            $this->requestStack->getSession()->set(self::CART_KEY_NAME, $cart->getId());
        }
    }
}
