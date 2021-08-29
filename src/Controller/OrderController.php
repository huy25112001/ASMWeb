<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order_list")
     */
    public function listOrder () {
        $orders = $this->getDoctrine()
                        ->getRepository(Order::class)
                        ->findAll();
    
        return $this->render(
                        "order/index.html.twig",
                        [
                            'orders' => $orders
                        ]
                     );
    }

    /**
     * @Route("/order/detail/{id}", name="order_detail")
     */
    public function detailOrder ($id) {
        $order = $this->getDoctrine()
                       ->getRepository(Order::class)
                       ->find($id);

        if ($order == null) {
            $this->addFlash("Error", "Order ID in invalid");
            return $this->redirectToRoute("order_list");
        }

        return $this->render(
                        "order/detail.html.twig",
                        [
                          'order' => $order
                        ]
        );
    }

    
    /**
     * @Route("/order/delete/{id}", name="order_delete")
     */
    public function deleteOrder ($id)
    {
        $order = $this->getDoctrine()
                          ->getRepository(Order::class)
                          ->find($id);
        if($order == null) {
            $this->addFlash("Error", "Invalid Order ID");
            return $this->redirectToRoute("order_list");
        }
        $manager = $this->getDoctrine()
                        ->getManager();
        $manager->remove($order);
        $manager->flush();
        $this->addFlash("Info", "Order has been deleted");    
        return $this->redirectToRoute("order_list");
    }

    /**
     * @Route("/order/create", name="order_create")
     */
    public function createOrder (Request $request)
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class,$order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()
                            ->getManager();
            $manager->persist($order);
            $manager->flush();

            $this->addFlash("Info", "Order has been added!");
            return $this->redirectToRoute("order_list");
        }
        return $this->render(
            "order/create.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }
}