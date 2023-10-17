<?php

namespace App\Controller;

use App\DBAL\Types\Enum\PropertyTypeEnum;
use App\DBAL\Types\Enum\ReservedWordsEnum;
use App\Form\ItemFormType;
use App\Service\ItemService;
use Exception;
use Psr\Log\LoggerInterface;
use Recombee\RecommApi\Requests\AddItem;
use Recombee\RecommApi\Requests\DeleteItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests as Reqs;
use Recombee\RecommApi\Exceptions as Ex;

class ItemController extends AbstractController
{
    private Client $client;
    private ParameterBagInterface $parameterBag;
    private ItemService $itemService;
    private array $fileContent;
    private LoggerInterface $logger;

    /**
     * @throws Exception
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        ItemService $itemService,
        LoggerInterface $logger
    )
    {
        $this->parameterBag = $parameterBag;
        $this->itemService = $itemService;
        $this->client = new Client(
            'egov-sac-dev',
            $parameterBag->get('RECOMBEE_DEV_DB_PRIVATE_TOKEN'),
            [
                'region' => 'eu-west'
            ]
        );
        $this->fileContent = $this->itemService->parseDatasetCSV();
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     */
    #[Route('/item/{itemId}/upload', name: 'app_item_upload', methods: ['PUT'])]
    public function uploadItem(Request $request): JsonResponse
    {
        $itemId = $request->get('itemId');

        $catalogueIds = json_decode($this->listItems(), true);
        if (in_array($itemId, $catalogueIds)) {
            return new JsonResponse(["message" => 'Item ID already exists!'], 409);
        }

        $pattern = '/^[a-zA-Z0-9_\-:@.]+$/';
        if (!preg_match($pattern, $itemId)) {
            return new JsonResponse('The itemId does not match the given pattern', 400);
        }

        $this->client->send(new AddItem($itemId));
        return new JsonResponse(["message" => 'Item added successfully'], 201);
    }

    /**
     * @throws Ex\ApiTimeoutException
     * @throws Ex\ResponseException
     */
    #[Route('/item/add-property/{propertyName}/{type}', name: 'app_item_add_property', methods: ['PUT'])]
    public function addItemProperty(Request $request): JsonResponse
    {
        $propertyName = $request->get('propertyName');
        $type = $request->get('type');

        $pattern = '/^[a-zA-Z0-9_\-:]+$/';
        if (!preg_match($pattern, $propertyName) || strlen($propertyName) > 63){
            return new JsonResponse(['message' => 'The property does not match the given pattern or exceeds the given limit'], 400);
        }

        if ($propertyName === ReservedWordsEnum::ID || $propertyName === ReservedWordsEnum::ITEM_ID) {
            return new JsonResponse(['message' => 'The property name is a reserved word'], 400);
        }

        if (!$type) {
            return new JsonResponse(['message' => 'Type information is missing'], 400);
        }

        if (!in_array($type, PropertyTypeEnum::getChoices())) {
            return new JsonResponse(['message' => 'Given type is invalid'], 400);
        }

        $this->client->send(new Reqs\AddItemProperty($propertyName, $type));
        return new JsonResponse(['message' => 'Property added successfully'], 201);
    }

    /**
     * @throws Ex\ResponseException
     * @throws Ex\ApiTimeoutException
     */
    #[Route('item/{itemId}/set-values', name: 'app_item_set_values', methods: ['POST'])]
    public function setItemValues(Request $request): JsonResponse
    {
        $itemId = $request->get('itemId');
        $form = $this->createForm(ItemFormType::class);
        $form->submit($request->request->all());
        $values = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $values['bookID'] = $item->getBookID();
            $values['title'] = $item->getTitle();
            $values['authors'] = $item->getAuthors();
            $values['average_rating'] = $item->getAverageRating();
            $values['isbn'] = $item->getIsbn();
            $values['isbn13'] = $item->getIsbn13();
            $values['language_code'] = $item->getLanguageCode();
            $values['num_pages'] = $item->getNumPages();
            $values['ratings_count'] = $item->getRatingsCount();
            $values['text_reviews_count'] = $item->getTextReviewsCount();
            $values['publication_date'] = $item->getPublicationDate();
            $values['publisher'] = $item->getPublisher();

            $this->client->send(new Reqs\SetItemValues($itemId, $values, [
                'cascadeCreate' => true
            ]));
            return new JsonResponse(['message' => 'Values set successfully'], 200);
        }
        return new JsonResponse(['message' => 'Something went wrong'], 400, [
            'cascadeCreate' => true
        ]);

    }

    /**
     * @throws Ex\ResponseException
     * @throws Ex\ApiTimeoutException
     */
    #[Route('item/{itemId}/delete', name: 'app_item_delete', methods: ['DELETE'])]
    public function deleteItem(Request $request): JsonResponse
    {
        $itemId = $request->get('itemId');

        $catalogueIds = json_decode($this->listItems()->getContent(), true);
        if (!in_array($itemId, $catalogueIds)) {
            return new JsonResponse(["message" => 'Item ID does not exist!'], 404);
        }

        $pattern = '/^[a-zA-Z0-9_\-:@.]+$/';
        if (!preg_match($pattern, $itemId)) {
            return new JsonResponse(['message' => 'The itemId does not match the given pattern'], 400);
        }

        $this->client->send(new DeleteItem($itemId));
        return new JsonResponse(['message' => 'Item deleted successfully'], 200);
    }

    /**
     * @throws Ex\ResponseException
     * @throws Ex\ApiTimeoutException
     */
    #[Route('item/all', name: 'app_item_all', methods: ['GET'])]
    public function listItems(): JsonResponse
    {
        $items = $this->client->send(new Reqs\ListItems());
        return new JsonResponse($items, 200);
    }

    /**
     * @throws Ex\ApiTimeoutException
     * @throws Ex\ResponseException
     */
    #[Route('/item/delete-property/{propertyName}', name: 'app_item_delete_property', methods: ['DELETE'])]
    public function deleteItemProperty(Request $request): JsonResponse
    {
        $propertyName = $request->get('propertyName');
        $properties = ($this->itemService->array_value_recursive('name',
            json_decode($this->listItemProperties()->getContent(), true)));

        if (!in_array($propertyName, $properties)) {
            return new JsonResponse(["message" => 'Item property does not exist!'], 404);
        }

        $pattern = '/^[a-zA-Z0-9_\-:]+$/';
        if (!preg_match($pattern, $propertyName)){
            return new JsonResponse(['message' => 'The property does not match the given pattern'], 400);
        }

        $this->client->send(new Reqs\DeleteItemProperty($propertyName));
        return new JsonResponse(['message' => 'Property deleted successfully'], 200);
    }

    /**
     * @throws Ex\ApiTimeoutException
     * @throws Ex\ResponseException
     */
    #[Route('/item/{property}/property-info', name: 'app_item_property_info', methods: ['GET'])]
    public function getItemPropertyInfo(Request $request): JsonResponse
    {
        $propertyName = $request->get('property');
        $properties = ($this->itemService->array_value_recursive('name',
            json_decode($this->listItemProperties()->getContent(), true)));

        if (!in_array($propertyName, $properties)) {
            return new JsonResponse(["message" => 'Item property does not exist!'], 404);
        }

        $pattern = '/^[a-zA-Z0-9_\-:]+$/';
        if (!preg_match($pattern, $propertyName)){
            return new JsonResponse(['message' => 'The property does not match the given pattern'], 400);
        }

        $propertyInfo = $this->client->send(new Reqs\GetItemPropertyInfo($propertyName));
        return new JsonResponse($propertyInfo, 200);
    }

    /**
     * @throws Ex\ResponseException
     * @throws Ex\ApiTimeoutException
     */
    #[Route('item/properties/all', name: 'app_item_properties_all', methods: ['GET'])]
    public function listItemProperties(): JsonResponse
    {
        $itemProperties = $this->client->send(new Reqs\ListItemProperties());
        return new JsonResponse($itemProperties,  200);
    }

    /**
     * @throws Ex\ApiTimeoutException
     * @throws Ex\ResponseException
     */
    #[Route('/item/{itemId}/values', name: 'app_item_values', methods: ['GET'])]
    public function getItemValues(Request $request): JsonResponse
    {
        $itemId = $request->get('itemId');

        $catalogueIds = json_decode($this->listItems()->getContent(), true);
        if (!in_array($itemId, $catalogueIds)) {
            return new JsonResponse(["message" => 'Item ID does not exist!'], 404);
        }

        $pattern = '/^[a-zA-Z0-9_\-:@.]+$/';
        if (!preg_match($pattern, $itemId)) {
            return new JsonResponse('The itemId does not match the given pattern', 400);
        }

        $itemValues = $this->client->send(new Reqs\GetItemValues($itemId));
        return new JsonResponse($itemValues, 200);
    }
}
