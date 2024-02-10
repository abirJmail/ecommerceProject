<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte/mes-commandes", name="account_order")
     */
    #[Route('/compte/mes-commandes', name: 'account_order')]
    public function index()
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
// dd($orders);
        return $this->render('account/order.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/compte/mes-commandes/{reference}", name="account_order_show")
     */
    #[Route('/compte/mes-commandes/{id}', name: 'account_order_show')]
    public function show($id)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneById($id);
        // $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_order');
        }

        return $this->render('account/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
