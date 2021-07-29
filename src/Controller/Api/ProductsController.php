<?php

namespace App\Controller\Api;

use App\Entity\Merchants;
use App\Entity\Products;
use App\Form\ProductsType;
use App\Form\Type\ProductsFormType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProductsController extends AbstractFOSRestController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Rest\Get(path="/products")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(
        ProductsRepository $productRepository,
        Request $request
    ) {
        $totalItems = $productRepository->countAll();

        //review conditionals
        $currentPage = $request->get('page', 1);
        $order = $request->get('order', 'ASC');
        $orderby = $request->get('order_by', 'id');
        $pageCount = $request->get('per_page', 5);
        $q = $request->get('q', null);

        $offset = $pageCount * ($currentPage - 1);

        $result = $productRepository->findAllWithParams($pageCount, $offset, $order, $orderby, $q);


        $criteria = ['type' => 'employee'];

        $totalResult = $productRepository->countBy($q);

        $totalCount  = ceil($totalResult / $pageCount);
        $nextPage = (($currentPage < $totalCount) ? $currentPage + 1 : null);
        $prevPage = (($currentPage > 1) ? $currentPage - 1 : null);

        $result['prev'] = $prevPage;
        $result['next'] = $nextPage;
        $result['total_pages'] = $totalResult;

        return $result;
        // return $productRepository->findAll();0
    }
    /**
     * @Rest\Get(path="/products/{id<\d+>}")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function getOnlyOneAction(
        ProductsRepository $productRepository,
        Request $request
    ) {

        $id = $request->get('id');

        return $productRepository->find($id);
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
        $form = $this->createForm(ProductsType::class, $product);


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

    /**
     * @Rest\Put(path="/products/{id<\d+>}")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function updateAction(
        EntityManagerInterface  $em,
        Request $request
    ) {


        $product = new Products();
        $content = json_decode($request->getContent(), true);

        $form = $this->createForm(ProductsType::class, $product);

        $form->submit($content);

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


    /**
     * @Rest\Delete(path="/products/{id}")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteAction(string $id, ProductsRepository $productsRepository)
    {
        try {
            $productsRepository->deleteById($id);
        } catch (Throwable $t) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }
        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}
