<?php

namespace App\EventSubscriber;

use App\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $categoryRepository;

    // Inject dependencies via constructor
    // To do this you need to add an argument to the constructor signature to accept the dependency
    public function __construct(Environment $twig, CategoryRepository $categoryRepository)
    {
        $this->twig = $twig;
        $this->categoryRepository = $categoryRepository;
    }

    // Event which is dispatched just before the controller is called
    // Inject the categories global variable so that Twig will have access to it when the controller will render the template.
    public function onKernelController(ControllerEvent $event): void
    {
        // add as many controllers as you want: the categories variable will always be available in Twig
        $this->twig->addGlobal('categories', $this->categoryRepository->findAll());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
