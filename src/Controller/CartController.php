<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Route("/mon-panier", name="cart")
     */
    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart)
    {
        // dd($cart->get());
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * Route("/cart/add/{id}", name="add_to_cart")
     */

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add(Cart $cart, $id)
    {
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * Route("/cart/remove", name="remove_my_cart")
     */

    #[Route('/cart/remove', name: 'remove_my_cart')]
    public function remove(Cart $cart)
    {
        $cart->remove();

        return $this->redirectToRoute('app_product');
    }

    // /**
    //  * Route("/cart/delete/{id}", name="delete_to_cart")
    //  */

    #[Route('/cart/delete/{id}', name: 'delete_to_cart')]
    public function delete(Cart $cart, $id)
    {
        $cart->delete($id);

        return $this->redirectToRoute('cart');
    }

    // /**
    //  * Route("/cart/decrease/{id}", name="decrease_to_cart")
    //  */

    #[Route('/cart/decrease/{id}', name: 'decrease_to_cart')]
    public function decrease(Cart $cart, $id)
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }
}

// <?php

// namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;

// class CartController extends AbstractController
// {
//     #[Route('/cart', name: 'app_cart')]
//     public function index(): Response
//     {
//         return $this->render('cart/index.html.twig', [
//             'controller_name' => 'CartController',
//         ]);
//     }
// }
