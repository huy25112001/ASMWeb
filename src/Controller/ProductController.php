<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Form\ProductType;
use Doctrine\DBAL\Abstraction\Result;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use function PHPUnit\Framework\throwException;

class ProductController extends AbstractController
{
    /**
   * @Route("/product", name="product_list")
   */
  public function listProduct (){
    $products = $this->getDoctrine()
                       ->getRepository(Product::class)
                       ->findAll();
    return $this->render(
      "product/index.html.twig",
      [
        'products' => $products
      ]
      );
  }

  /**
   * @Route("/product/detail/{id}", name="product_detail")
   */
  public function detailProduct ($id) {
    $product = $this->getDoctrine()
                       ->getRepository(Product::class)
                       ->find($id);
    if ($product == null){
      $this->addFlash("Warning", "Product ID is invalid!");
      return $this->redirectToRoute("product_list");    
    }
    return $this->render(
      "product/detail.html.twig",
      [
        'product' => $product
      ]
      );
  }

  /**
   * @Route("/product/delete/{id}", name="product_delete")
   */
  public function deleteProduct ($id) {
    $product = $this->getDoctrine()
                     ->getRepository(Product::class)
                     ->find($id);
    if ($product == null){
      $this->addFlash("Warning", "Product ID is invalid!");
      return $this->redirectToRoute("product_list");
    }
    $manager = $this->getDoctrine()
                    ->getManager();
    $manager->remove($product);
    $manager->flush();
    $this->addFlash("Success", "Product has been deleted!");
    return $this->redirectToRoute("product_list");
  }

  /**
   * @Route("/product/create", name="product_create")
   */
  public function createProduct (Request $request) {
    $product = new Product();
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){

      $image = $product->getImage();

      // create unique image name
      $fileName = md5(uniqid());
      $fileExtension = $image->getExtension();
      $imageName = $fileName . '.' . $fileExtension;

      // move uploaded image to defined location
      try {
        $image->move(
          $this->getParameter('product_image'), $imageName
        );
      } catch (FileException $e) {
        throwException($e);
      }
      
      $product->setImage($imageName);

      $manager = $this->getDoctrine() 
                      ->getManager();
      $manager->persist($product);
      $manager->flush();
      $this->addFlash("Success", "Add product successfully!!");
      return $this->redirectToRoute("product_list");
    }

    return $this->render(
      "product/create.html.twig",
      [
        "form" => $form->createView()
      ]
    );
  }

  /**
   * @Route("/product/update/{id}", name="product_update")
   */
  public function updateProduct (Request $request, $id) {
    $product = $this->getDoctrine()
                     ->getRepository(Product::class)
                     ->find($id);
    
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $uploadedFile = $form['Image']->getData();
      if ($uploadedFile != null){
        $image = $product->getImage();

        // create unique image name
        $fileName = md5(uniqid());
        $fileExtension = $image->getExtension();
        $imageName = $fileName . '.' . $fileExtension;

        // move uploaded image to defined location
        try {
          $image->move(
            $this->getParameter('product_image'), $imageName
          );
        } catch (FileException $e) {
          throwException($e);
        }
        
        $product->setImage($imageName);
      }
      $manager = $this->getDoctrine() 
                      ->getManager();
      $manager->persist($product);
      $manager->flush();
      $this->addFlash("Success", "Update product successfully!!");
      return $this->redirectToRoute("product_list");
    }

    return $this->render(
      "product/update.html.twig",
      [
        "form" => $form->createView()
      ]
    );
  }
}
