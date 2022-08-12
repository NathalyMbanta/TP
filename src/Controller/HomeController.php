<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')] // quand l'url / sera demandée, la méthode index sera exécutée
    public function index(ProductRepository $productRepository): Response
    {
        // $produits = $productRepository->findAll(); // récupère tous les produits en base de données
        $produits = $productRepository->findBy([], ['created_at' => 'DESC', 'id' => 'DESC'], 8); // trouve 8 produits, tri par date d'ajout DESC
        // $produits = $productRepository->findLast(8); // queryBuiler
        // $produits = $productRepository->findLastEight(); // SQL
        
        return $this->render('home/index.html.twig', [ // demande à Twig d'afficher le template home/index.html.twig
            'products' => $produits // en lui passant des infos
        ]);
    }
}
