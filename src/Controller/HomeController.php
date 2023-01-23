<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * HomeController
 */
class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]

    /**
     * index
     *
     * @param  mixed $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $greet = '';
        if ($name = $request->query->get('hello')) {
            $greet = sprintf('Hello %s!', $name);
        }

        dump($request); //Debugging Variables

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'greet' => $greet,
        ]);
    }
}
