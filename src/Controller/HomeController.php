<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * HomeController
 */
class HomeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    // The route name will be useful when we want to reference the homepage in the code. Instead of hard-coding the / path, we will use the route name.
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

    #[Route('/product/{id}', name: 'product')]
    public function showProduct(
        Request $request,
        Product $product,
        CommentRepository $commentRepository,
        #[Autowire('%comments_photo_dir%')] string $photoDir,
    ): Response {
        // Displaying a Form for comments
        // create the form in the controller and pass it to the template
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        //handle the form submission and the persistence of its information to the database
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //The product is forced to be the same as the one from the URL (removed it from the form).
            $comment->setProduct($product);

            // Manage photo uploads
            if ($photo = $form['photo']->getData()) {
                // Random name for the file
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    // Move the uploaded file to its final location
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                // Store the filename in the Comment object
                $comment->setPhotoFilename($filename);
            }

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            // If the form is not valid, display the page, but the form will now contain submitted values and error messages so that they can be displayed back to the user.
            return $this->redirectToRoute('product', ['id' => $product->getId()]);
        }

        // To manage the pagination in the template
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($product, $offset);

        // $product will equal the dynamic part of the URL
        // The controller gets the offset from the Request query string ($request->query) as an integer (getInt()), defaulting to 0 if not available.
        // The previous and next offsets are computed based on all the information we have from the paginator.
        return $this->render('home/product.html.twig', [
            'product' => $product,
            //'comments' => $commentRepository->findBy(['product' => $product], ['createdAt' => 'DESC']), //without pagination
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form,
        ]);
    }
}
