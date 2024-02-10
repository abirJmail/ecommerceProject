<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    #[Route('/commande/erreur/{stripeSessionID}', name: 'app_order_cancel')]
    public function index($stripeSessionID,): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionID($stripeSessionID);
        //  dd($order);
if (!$order || $order->getUser()!= $this->getUser()) {
    return $this->redirectToRoute('app_home');
}
        // Envoyer un email à notre utilisateur pour lui indiquer l'échec de paiement

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
