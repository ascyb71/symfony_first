<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// IMPORTANT : Le nom ici est bien DashboardController
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ProductRepository $productRepository): Response
    {
        // 1. On récupère tous les produits
        $products = $productRepository->findAll();

        // 2. On prépare les compteurs
        $totalValue = 0;
        $outOfStockCount = 0;

        // 3. On calcule
        foreach ($products as $product) {
            // Valeur = Prix * Stock
            $totalValue += ($product->getPrice() * $product->getStock());

            // Si le stock est 0 ou moins, c'est une rupture
            if ($product->getStock() <= 0) {
                $outOfStockCount++;
            }
        }

        // 4. On prend les 5 derniers produits ajoutés (Nouveautés)
        $latestProducts = array_slice(array_reverse($products), 0, 5);
        return $this->render('dashboard/index.html.twig', [
            'products' => $products,
            'totalValue' => $totalValue,
            'outOfStockCount' => $outOfStockCount,
            'latestProducts' => $latestProducts
        ]);
    }
}
