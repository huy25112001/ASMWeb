<?php

namespace App\Controller;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index() {
        return $this->render(
            'main/index.html.twig', 
        );
    }
}
