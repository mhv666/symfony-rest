<?php

namespace App\Controller\Api;

use App\Entity\Products;
use App\Form\Type\ProductsFormType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/products")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(
        ProductsRepository $productRepository
    ) {
        return $productRepository->findAll();
    }
    /**
     * @Rest\Post(path="/products")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function postAction(
        EntityManagerInterface  $em,
        Request $request
    ) {
        $product = new Products();
        $form = $this->createForm(ProductsFormType::class, $product);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if ($form->isValid()) {
            $em->persist($product);
            $em->flush();
            return $product;
        }

        return $form;
    }
}
