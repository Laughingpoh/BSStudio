<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchType;
use App\Classe\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
         $this->entityManager = $entityManager;
    }

    #[Route('/nos-produits', name: 'app_products')]
    public function index(Request $request): Response
    {
        $search = new Search();

        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        } else {
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig',[
            'products' => $products,
            'form'=> $form->createView() // Passage à twig
        ]);
    }

    #[Route('/produit/{slug}', name: 'app_product')]
    public function show($slug)
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);

        //dd($product);
        if ( !$product ) {
            return $this->redirectToRoute('app_products');
        }
        return $this->render('product/show.html.twig',[
            'product' => $product,
            'products' => $products
        ]);
    }
}
