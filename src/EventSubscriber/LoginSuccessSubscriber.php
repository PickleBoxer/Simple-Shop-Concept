<?php
// src/EventSubscriber/LoginSuccessSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\FirewallMapInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

    private $firewallMap;

    /**
     * CartSessionStorage constructor.
     *
     * @param RequestStack $requestStack
     * 
     */
    public function __construct(RequestStack $requestStack,FirewallMapInterface $firewallMap)
    {
        $this->requestStack = $requestStack;
        $this->firewallMap = $firewallMap;
    }

    public static function getSubscribedEvents(): array
    {
        return [LoginSuccessEvent::class => 'onLogIn'];
    }

    public function onLogIn(LoginSuccessEvent $event): void
    {
        // get the current request
        $request = $event->getRequest();
        // get firewall name 
        $firewallConfig = $this->firewallMap->getFirewallConfig($request);

        // exclude admin firewall from get user and cart
        if ('admin' === $firewallConfig->getName()) {
            return;
        }


        // returns User object or null if not authenticated
        $user = $event->getUser();

        // get cart
        $cart = $user->getCart();

        // if user have cart -> Sets the cart in session.
        if ($cart) {
            $this->requestStack->getSession()->set(self::CART_KEY_NAME, $cart->getId());
        }
    }
}
