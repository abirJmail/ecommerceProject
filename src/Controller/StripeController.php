<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    // #[Route('commande/create-session/{reference}', name: 'stripe_create_session')]
    // public function index(EntityManagerInterface $entityManager, Cart $cart, $reference)
    // {
    //     $products_for_stripe = [];
    //     $YOUR_DOMAIN = 'http://127.0.0.1:8000';

    //     $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

    //     if (!$order) {
    //         new JsonResponse(['error' => 'order']);
    //     }

    //     foreach ($order->getOrderDetails()->getValues() as $product) {
    //         $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getPrpduct());
    //         $products_for_stripe[] = [
    //             'price_data' => [
    //                 'currency' => 'eur',
    //                 'unit_amount' => $product->getPrice(),
    //                 'product_data' => [
    //                     'name' => $product->getPrpduct(),
    //                     'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
    //                 ],
    //             ],
    //             'quantity' => $product->getQuantity(),
    //         ];
    //     }


    //     $products_for_stripe[] = [
    //         'price_data' => [
    //             'currency' => 'eur',
    //             'unit_amount' => $order->getCarrierPrice(),
    //             'product_data' => [
    //                 'name' => $order->getCarrierName(),
    //                 'images' => [$YOUR_DOMAIN],
    //             ],
    //         ],
    //         'quantity' => 1,
    //     ];

    //     Stripe::setApiKey('sk_test_51HWz8KGVnm98Up5GCMO5wIuNe4JDUMhEyzOjCkCDKbYj2GjDT0RIZIXDWZ0p75UH5Il2N9OkRsTWFgtVGkqGYdYr00gJZqlI0w');

    //     $checkout_session = Session::create([
    //         'customer_email' => $this->getUser()->getEmail(),
    //         'payment_method_types' => ['card'],
    //         'line_items' => [
    //             $products_for_stripe
    //         ],
    //         'mode' => 'payment',
    //         'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
    //         'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
    //     ]);
    //     dump($checkout_session->id);
    //     dd($checkout_session);

    //     // $order->setStripeSessionId($checkout_session->id);
    //     // $entityManager->flush();

    //     $response = new JsonResponse(['id' => $checkout_session->id]);
    //     // return $response;
    //     return $this->redirect($response->url, 303);
    // }

    #[Route('commande/create-session/{reference}', name: 'app_payment')]
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
        $products_for_stripe = [];
             $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
            //  dd($order);
             
        // Initialisation de la clé API de Stripe        
        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $YOUR_DOMAIN = $_ENV['DOMAIN'];


        foreach ($order->getOrderDetails()->getValues() as $product) {
            // dd($product);
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getPrpduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getPrpduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice()*100,
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        // On créer une session de paiement
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);
        if (!$order) {
            return $this->redirect($session->error, 303);
             }
$order->setStripeSessionId($session->id);
$entityManager->flush();

        return $this->redirect($session->url, 303);
    }

}
