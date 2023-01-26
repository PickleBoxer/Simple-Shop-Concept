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

    #[Route('/category/{slug}', name: 'category')]
    public function show(Request $request, Category $category, ProductRepository $productRepository): Response
    {
        // To manage the pagination in the template
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepository->getProductPaginator($category, $offset);

        // $category will equal the dynamic part of the URL
        // The controller gets the offset from the Request query string ($request->query) as an integer (getInt()), defaulting to 0 if not available.
        // The previous and next offsets are computed based on all the information we have from the paginator.
        return $this->render('home/category.html.twig', [
            'category' => $category,
            //'products' => $productRepository->findBy(['id_category' => $category], ['createdAt' => 'DESC']), //without pagination
            'products' => $paginator,
            'previous' => $offset - ProductRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ProductRepository::PAGINATOR_PER_PAGE),
        ]);
    }
}
