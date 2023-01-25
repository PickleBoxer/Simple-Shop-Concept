<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
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
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {
        $greet = '';
        if ($name = $request->query->get('hello')) {
            $greet = sprintf('Hello %s!', $name);
        }

        dump($request); //Debugging Variables
        dump($categoryRepository->findAll()); //Debugging Variables

        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'controller_name' => 'HomeController',
            'greet' => $greet,
        ]);
    }

    #[Route('/category/{id}', name: 'category')]
    public function show(Category $category, ProductRepository $productRepository): Response
    {
        // $category will equal the dynamic part of the URL
        return $this->render('home/category.html.twig', [
            'category' => $category,
            'products' => $productRepository->findBy(['id_category' => $category], ['createdAt' => 'DESC']),
        ]);
    }
}
