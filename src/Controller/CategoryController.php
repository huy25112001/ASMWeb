<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use Doctrine\DBAL\Abstraction\Result;
use Doctrine\ORM\Mapping\PostUpdate;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use function PHPUnit\Framework\throwException;

class CategoryController extends AbstractController
{
  /**
  * @Route("/category/api/viewall", methods={"GET"}, name="view_all_category_api")
  */
  public function viewALLCategoryAPI (SerializerInterface $serializer){
      $categories = $this->getDoctrine()
                         ->getRepository(Category::class)
                         ->findAll();
      /* SQL: "SELECT * FROM Category" */
      $data = $serializer->serialize($categories,'json');
      return new Response(
          $data,
          Response::HTTP_OK,
          [
            "content-type" => "application/json"
          ]
      );
  } 
  /**
  * @Route("/category/api/view/{id}", methods={"GET"}, name="view_category_by_id_api")
  */
  public function viewCategoryByIdAPI (SerializerInterface $serializer, $id){
    $category = $this->getDoctrine()
                     ->getRepository(Category::class)
                     ->find($id);
    if ($category == null){
      $error = "ERROR: Invalid Category ID";
      return new Response(
        $error,
        Response::HTTP_NOT_FOUND
      );
    }
    /* if category is not null */
    $data = $serializer->serialize($category, 'xml');
    return new Response(
      $data,
      200,
      [
        "content-type" => "application/xml"
      ]
    );
  }  

  /**
   * @Route("/category/api/delete/{id}", methods={"DELETE"}, name="delete_category_api")
   */
  public function deleteCategoryAPI ($id) {
    try{
        $category = $this->getDoctrine()
                       ->getRepository(Category::class)
                       ->find($id);
        if ($category == null){
          return new Response(
            null,
            Response::HTTP_BAD_REQUEST
          );
        }
        $manager = $this->getDoctrine()
                        ->getManager();
        $manager->remove($category);
        $manager->flush();
        return new Response(
          "Category has been deleted",
          Response::HTTP_OK
        );

    }catch (\Exception $e){
      return new Response(
        json_encode(["ERROR " => $e->getMessage()]),
        Response::HTTP_BAD_REQUEST,
        [
          "content-type" => "application/json"
        ]
      );
    }
  }

  /**
   * @Route("/category/api/create", methods={"POST"}, name="create_category_api")
   */
  public function createCategoryAPI(Request $request){
    try {
      $category = new Category();
      $data = json_decode($request->getContent(), true);
      $category->setName($data['name']);
      $category->setDescription($data['description']);
      $manager = $this->getDoctrine()
                      ->getManager();
      $manager->persist($category);
      $manager->flush();
      return new Response(
        "Category has been created",
        Response::HTTP_OK
      );
    } catch (\Exception $e) {
      return new Response(
        json_encode(["ERROR " => $e->getMessage()]),
        Response::HTTP_BAD_REQUEST,
        [
          "content-type" => "application/json"
        ]
      );
    }
  }


  /**
   * @Route("/category/api/update/{id}", methods={"PUT"}, name="update_category_api")
   */
  public function updateCategoryAPI(Request $request, $id){
    try {
      $category = $this->getDoctrine()
                       ->getRepository(Category::class)
                       ->find($id);
      $data = json_decode($request->getContent(), true);
      $category->setName($data['name']);
      $category->setDescription($data['description']);
      $manager = $this->getDoctrine()
                      ->getManager();
      $manager->persist($category);
      $manager->flush();
      return new Response(
        "Category has been updated",
        Response::HTTP_OK
      );
    } catch (\Exception $e) {
      return new Response(
        json_encode(["ERROR " => $e->getMessage()]),
        Response::HTTP_BAD_REQUEST,
        [
          "content-type" => "application/json"
        ]
      );
    }
  }

  /**
   * @Route("/category", name="category_list")
   */
  public function listCategory (){
    $categories = $this->getDoctrine()
                       ->getRepository(Category::class)
                       ->findAll();
    return $this->render(
      "category/index.html.twig",
      [
        'categories' => $categories
      ]
      );
  }

  /**
   * @Route("/category/detail/{id}", name="category_detail")
   */
  public function detailCategory ($id) {
    $category = $this->getDoctrine()
                       ->getRepository(Category::class)
                       ->find($id);
    if ($category == null){
      $this->addFlash("Warning", "Category ID is invalid!");
      return $this->redirectToRoute("category_list");
    }
    return $this->render(
      "category/detail.html.twig",
      [
        'category' => $category
      ]
      );
  }

  /**
   * @Route("/category/delete/{id}", name="category_delete")
   */
  public function deleteCategory ($id) {
    $category = $this->getDoctrine()
                     ->getRepository(Category::class)
                     ->find($id);
    /* SQL: "DELETE FROM Category WHERE id = '$id'" */  
    if($category == null)
    {
        $this->addFlash("Warning", "Category ID is invalid!");
        return $this->redirectToRoute("category_list");
      
    }
    $manager = $this->getDoctrine()
                    ->getManager();         
    $manager->remove($category);
    $manager->flush();
    $this->addFlash("Success", "Category has been deleted!");
    return $this->redirectToRoute("category_list");
  
  }

  /**
   * @Route("/category/create", name="category_create")
   */
  public function createCategory (Request $request) {
    $category = new Category();
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      // $image = $category->getImage();

      // // create unique image name
      // $fileName = md5(uniqid());
      // $fileExtension = $image->guessExtension();
      // $imageName = $fileName . '.' . $fileExtension;

      // // move uploaded image to defined location
      // try {
      //   $image->move(
      //     $this->getParameter('category_image'), $imageName
      //   );
      // } catch (FileException $e) {
      //   throwException($e);
      // }
      
      // $category->setImage($imageName);

      $manager = $this->getDoctrine() 
                      ->getManager();
      $manager->persist($category);
      $manager->flush();
      $this->addFlash("Success", "Add category successfully!!");
      return $this->redirectToRoute("category_list");
    }

    return $this->render(
      "category/create.html.twig",
      [
        "form" => $form->createView()
      ]
    );
  }

  /**
   * @Route("/category/update/{id}", name="category_update")
   */
  public function updateCategory (Request $request, $id) {
    $category = $this->getDoctrine()
                     ->getRepository(Category::class)
                     ->find($id);
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $manager = $this->getDoctrine() 
                      ->getManager();
      $manager->persist($category);
      $manager->flush();
      $this->addFlash("Success", "Update category successfully!!");
      return $this->redirectToRoute("category_list");
    }

    return $this->render(
      "category/update.html.twig",
      [
        "form" => $form->createView()
      ]
    );
  }
  
}