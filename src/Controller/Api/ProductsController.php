<?php

namespace App\Controller\Api;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Pagination\PaginatedCollection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $route = 'api_products_collection';
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
        if (empty($result["rows"])) {
            return new JsonResponse(['message' => 'Item not found.'], JsonResponse::HTTP_NOT_FOUND);
        }
        $totalResult = $result['count'];
        $totalCount  = ceil($totalResult / $pageCount);
        $nextPage = (($currentPage < $totalCount) ? $currentPage + 1 : null);
        $prevPage = (($currentPage > 1) ? $currentPage - 1 : null);


        $createLinkUrl = function ($targetPage) use ($route, $query) {
            if (is_null($targetPage)) return null;
            return $this->generateUrl($route, array_merge(
                $query,
                array('page' => $targetPage)
            ));
        };

        $paginatedCollection = new PaginatedCollection($result['rows'], $totalCount);
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
        $result = $productRepository->find($id);

        if (is_null($result)) {
            return new JsonResponse(['message' => 'Item not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $result;
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
        $this->processForm($request, $form);

        if (!$form->isSubmitted()) {
            return new JsonResponse(['message' => 'Error submiting.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($form->isValid()) {
            $em->persist($product);
            $em->flush();
            return $product;
        } else {
            dump((string) $form->getErrors(true, false));
        }

        return $form;
    }

    /**
     * @Rest\Put(path="/products/{id<\d+>}")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function updateAction(
        EntityManagerInterface  $em,
        ProductsRepository $productRepository,
        Request $request
    ) {
        try {


            $id = $request->get('id');
            $product = $productRepository->find($id);
            $form = $this->createForm(ProductsType::class, $product);
            $this->processForm($request, $form);

            if (!$form->isSubmitted()) {
                return new JsonResponse(['message' => 'Error submiting.'], JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($form->isValid()) {
                $em->persist($product);
                $em->flush();
                return $product;
            } else {
                dump((string) $form->getErrors(true, false));
            }
            return $form;
        } catch (\Exception $e) {
            throw new BadRequestException($e->getMessage());
        }
    }


    /**
     * @Rest\Delete(path="/products/{id}")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteAction(string $id, ProductsRepository $productsRepository)
    {

        $result = $productsRepository->deleteById($id);
        if (is_null($result)) {

            return new JsonResponse(['message' => 'Item not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['message' => 'Item deleted succesfuly.'], JsonResponse::HTTP_GONE,  ['content-type' => 'application/json']);;
    }

    // private function getUriPage(
    //     int $page = null,
    //     Request $request
    // ) {

    //     if (is_null($page)) {
    //         return null;
    //     }

    //     $query_string = [];
    //     parse_str($_SERVER['QUERY_STRING'], $query_string);
    //     $query_string['page'] = $page;
    //     $rdr_str = http_build_query($query_string);

    //     if (null !== $qs = $rdr_str) {
    //         $qs = '?' . $qs;
    //     }
    //     $uri = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo() . $qs;

    //     return $uri;
    // }

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
    private function processForm(Request $request, FormInterface $form)
    {

        $data = json_decode($request->getContent(), true);
        $form->submit($data);
    }
}
