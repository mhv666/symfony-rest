<?php

namespace App\Controller\Api;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Pagination\PaginatedCollection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use ReflectionClass;
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
     * @Rest\Get(path="/products",name="api_products_collection")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(
        ProductsRepository $productRepository,
        Request $request
    ) {
        $totalItems = $productRepository->countAll();
        $wrappedFields = null;
        $currentPage = $request->get('page', 1);
        $order = $request->get('order', 'ASC');
        $orderby = $request->get('order_by', 'id');
        $pageCount = $request->get('per_page', 5);
        $fields = $request->get('fields', null);
        $q = $request->get('q', null);


        $reflect = new \ReflectionClass(Products::class);
        $properties = array_map(function ($property) {
            return $property->getName();
        }, $reflect->getProperties());

        $query = $request->query->all();

        $filter_by_fields = array_intersect_key($query, array_flip($properties));

        if (!is_null($fields)) {
            $fields_arr = explode(",", $fields);
            $wrappedFields = $this->wrapFields($fields_arr, $properties);
        }

        $offset = $pageCount * ($currentPage - 1);

        $result = $productRepository->findAllWithParams($pageCount, $offset, $order, $orderby, $q, $wrappedFields, $filter_by_fields);

        $totalResult = $productRepository->countBy($q);

        $totalCount  = ceil($totalResult / $pageCount);
        $nextPage = (($currentPage < $totalCount) ? $currentPage + 1 : null);
        $prevPage = (($currentPage > 1) ? $currentPage - 1 : null);

        $route = 'api_products_collection';

        $createLinkUrl = function ($targetPage) use ($route, $query) {
            return $this->generateUrl($route, array_merge(
                $query,
                array('page' => $targetPage)
            ));
        };

        $paginatedCollection = new PaginatedCollection($result, $totalCount);
        $paginatedCollection->addLink('self', $createLinkUrl($currentPage));
        $paginatedCollection->addLink('prev', $createLinkUrl($prevPage));
        $paginatedCollection->addLink('next', $createLinkUrl($nextPage));

        return $paginatedCollection;
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
        $errors = $form->getErrors();
        print_r($errors->__toString());
        die;
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

    private function getUriPage(
        int $page = null,
        Request $request
    ) {

        if (is_null($page)) {
            return null;
        }

        $query_string = [];
        parse_str($_SERVER['QUERY_STRING'], $query_string);
        $query_string['page'] = $page;
        $rdr_str = http_build_query($query_string);

        if (null !== $qs = $rdr_str) {
            $qs = '?' . $qs;
        }
        $uri = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo() . $qs;

        return $uri;
    }

    private function wrapFields($fields, $properties)
    {

        if (is_array($fields)) {
            $wrappedFields = array_map(function ($field) use ($properties) {
                if (!in_array($field, $properties)) {
                    throw new BadRequestException("field param '$field' doesn't exist");
                }
                return "p.{$field}";
            }, $fields);
            return $wrappedFields;
        }
        return null;
    }
}
