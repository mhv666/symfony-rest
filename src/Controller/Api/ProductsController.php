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
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
// use OpenAPI\Annotations\Response as Res;
// use OpenAPI\Annotations\JsonContent as JC;
// use OpenAPI\Annotations\Items as Items;

use AppBundle\Entity\User;
use AppBundle\Entity\Reward;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;


use Throwable;


class ProductsController extends AbstractFOSRestController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }







    /**
     * List the products.
     *
     * 
     * @Rest\Get(path="/products",name="api_products_collection")
     * 
     * 
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of products",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Products::class, groups={"products"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="order",
     *     in="query",
     *     example = "ASC",
     *     description="The field  used to specify the order ASC or DESC",
     *     @OA\Schema(type="string")
     * )
     * 
     * @OA\Parameter(
     *     name="order_by",
     *     in="query",
     *     example = "color",
     *     description="The field  used to order",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     example = "1",
     *     description="Actual page",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     example = "5",
     *     description="Quantity of products per request",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="fields",
     *     in="query",
     *     example = "name,color",
     *     description="The field/s  you want to recive from the api",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="q",
     *     in="query",
     *     example = "qu",
     *     description="Field used to search products by name",
     *     @OA\Schema(type="string")
     * )
     * 
     * @OA\Tag(name="Products")
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
     * Retrive a single product.
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retrive a single product.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Products::class, groups={"products"}))
     *     )
     * )
     * 
     * @OA\Tag(name="Products")
     * 
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
     * Insert a product.
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns product inserted with id.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Products::class, groups={"products"}))
     *     )
     * )
     * 
     * 
     *    @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     * 
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of the product",
     *                     example = "Lorem",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="A brief description of the product",
     *                      example = "Qui optio consectetur ad ullam perspiciatis",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="image",
     *                     description="url of the product image",
     *                      example = "/tmp/d63fde10266c48b744b78e2f2762ef71.png",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="color",
     *                     description="Main color of the product",
     *                     example = "blue",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="merchant",
     *                     description="Id of an existent merchant",
     *                     example="223",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="category",
     *                     description="Id of an existent category",
     *                      example="130",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     description="Price of the product",
     *                      example="290.28",
     *                     type="double"
     *                 ),
     *                 @OA\Property(
     *                     property="ean13",
     *                     description="Ean13 code",
     *                     example="6938832514614",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="stock",
     *                     description="Actual stock of the product",
     *                      example="15",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="tax_percentage",
     *                     description="Percetage of tax applied",
     *                      example="4",
     *                     type="int"
     *                 )
     * 
     *             )
     *         )
     *     )
     * 

     * @OA\Tag(name="Products")
     * 
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
        try {
            if ($form->isValid()) {
                $em->persist($product);
                $em->flush();
                return $product;
            } else {
                dump((string) $form->getErrors(true, false));
            }
        } catch (\Exception $e) {
            throw new BadRequestException("Error while trying to save a new product.");
        }


        return $form;
    }

    /**
     * 
     * 
     * Update one or more fields of a product.
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a product.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Products::class, groups={"products"}))
     *     )
     * )
     *    @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     * 
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of the product",
     *                     example = "Lorem",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="A brief description of the product",
     *                      example = "Qui optio consectetur ad ullam perspiciatis",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="image",
     *                     description="url of the product image",
     *                      example = "/tmp/d63fde10266c48b744b78e2f2762ef71.png",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="color",
     *                     description="Main color of the product",
     *                     example = "blue",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="merchant",
     *                     description="Id of an existent merchant",
     *                     example="223",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="category",
     *                     description="Id of an existent category",
     *                      example="130",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     description="Price of the product",
     *                      example="290.28",
     *                     type="double"
     *                 ),
     *                 @OA\Property(
     *                     property="ean13",
     *                     description="Ean13 code",
     *                     example="6938832514614",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="stock",
     *                     description="Actual stock of the product",
     *                      example="15",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="tax_percentage",
     *                     description="Percetage of tax applied",
     *                      example="4",
     *                     type="int"
     *                 )
     * 
     *             )
     *         )
     *     )
     * 

     * @OA\Tag(name="Products")
     * 
     * 
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
     * 
     * 
     * 
     * Delete a single product.
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retrive a single product.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(
     *                  @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Lorem ipsum"
     *                      ),)
     *      )
     *     )
     * )
     * 
     * @OA\Tag(name="Products")
     * 
     * @Rest\Delete(path="/products/{id}")
     * @Rest\View(serializerGroups={"products"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteAction(string $id, ProductsRepository $productsRepository)
    {

        $result = $productsRepository->deleteById($id);
        if (is_null($result)) {

            return new JsonResponse(['message' => 'Item not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['message' => 'Item deleted succesfuly.'], JsonResponse::HTTP_OK,  ['content-type' => 'application/json']);;
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
