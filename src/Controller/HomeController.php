<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\AddToCartType;
use App\Manager\CartManager;
use App\Form\CommentFormType;
use App\Form\SearchProductsType;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(Request $request, CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        $greet = '';
        if ($name = $request->query->get('hello')) {
            $greet = sprintf('Hello %s!', $name);
        }

        //dump($categoryRepository->findAll()); //Debugging Variables

        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'controller_name' => 'HomeController',
            'greet' => $greet,
            'productsNew' => $productRepository->findBy(['active' => true], ['createdAt' => 'DESC'], 8),
        ]);
    }

    #[Route('/products', name: 'products')]
    public function products(Request $request, ProductRepository $productRepository): Response
    {
        $cat = '';

        if ($id = $request->query->get('cat')) {
            $products = $productRepository->findBy(['id_category' => $id], ['createdAt' => 'DESC']);
        } else {
            $products = $productRepository->findAll();
        }

        $form = $this->createForm(SearchProductsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $value = $form->getData();
            $products = $productRepository->findBy(['name' => strval($value)]);

        }

        return $this->render('home/products.html.twig', [
            'products' => $products,
            'search' => $form,
        ]);
    }

    #[Route('/category/{slug}', name: 'category')]
    public function show(Request $request, Category $category, ProductRepository $productRepository): Response
    {
        // To manage the pagination in the template
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepository->getProductPaginator($category, $offset);

        // set page size
        $pageSize = ProductRepository::PAGINATOR_PER_PAGE;
        // total items
        $totalItems = count($paginator);
        // no. pages
        $pagesCount = ceil($totalItems / $pageSize);
        
        $pages_links = [];
        for ($i = 0; $i <= $pagesCount -1; $i++) {
            $pages_links[$i+1] = min($totalItems, $i * $pageSize);
        }

        dump($pages_links);


        // $category will equal the dynamic part of the URL
        // The controller gets the offset from the Request query string ($request->query) as an integer (getInt()), defaulting to 0 if not available.
        // The previous and next offsets are computed based on all the information we have from the paginator.
        return $this->render('home/category.html.twig', [
            'category' => $category,
            //'products' => $productRepository->findBy(['id_category' => $category], ['createdAt' => 'DESC']), //without pagination
            'products' => $paginator,
            'param' => $offset,
            'pageSize' => $pageSize,
            'pagination' => $pages_links,
            'previous' => $offset - ProductRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ProductRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/product/{id}', name: 'product')]
    public function showProduct(
        Request $request,
        Product $product,
        CommentRepository $commentRepository,
        CartManager $cartManager,
        #[Autowire('%comments_photo_dir%')] string $photoDir,
    ): Response {
        // Displaying a Form for comments
        // create the form in the controller and pass it to the template
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        dump($product);

        // Handle the Comment form submission and the persistence of its information to the database
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

        // Displaying a Form for AddToCart
        $formAdd = $this->createForm(AddToCartType::class);

        // Handle the AddToCart form submission and add the product to the cart by using the CartManager manager.
        $formAdd->handleRequest($request);

        if ($formAdd->isSubmitted() && $formAdd->isValid()) {
            // OrderItem object is updated according to the submitted data
            $item = $formAdd->getData();
            // Link the product to the OrderItem object
            $item->setProduct($product);

            // add the item to the current cart
            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime());

            // Add the item to the current cart and persist it in the session and database
            $cartManager->save($cart);

            // Flash Notice
            $this->addFlash(
                'notice',
                'Product added to cart'
            );
            // $this->addFlash() is equivalent to $request->getSession()->getFlashBag()->add()

            // Redirect the user to the product page
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
            'formAdd' => $formAdd->createView(),
        ]);
    }
}
