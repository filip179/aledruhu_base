<?php

namespace AppBundle\Util;

use Component\Doctrine\EntityInterface;
use Component\Entity\DeletableInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class DatatableDataProvider {
    /**
     * Fields templates file declaration
     */
    const TEMPLATE_FIELD_BOOL = 'AppBundle:template/datatables/fields:bool.html.twig';
    const TEMPLATE_FIELD_COLLECTION = 'AppBundle:template/datatables/fields:collection.html.twig';
    const TEMPLATE_FIELD_OBJECT = 'AppBundle:template/datatables/fields:object.html.twig';

    /**
     * Other templates
     */
    const TEMPLATE_BUTTON = 'AppBundle:template/datatables/buttons:%type%.html.twig';

    protected $actions = [];

    protected $excludedFields = [];

    protected $fields = [];

    /** @var Paginator */
    private $paginator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        RouterInterface $router,
        Paginator $paginator,
        EngineInterface $templating,
        RequestStack $requestStack
    )
    {
        $this->router = $router;
        $this->paginator = $paginator;
        $this->templating = $templating;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param QueryBuilder $query
     * @param int $recordsTotal
     * @param array $actions
     * @param array $fields
     *
     * @return array
     */
    public function getData(QueryBuilder $query, int $recordsTotal, array $actions = [], array $fields = [])
    {
        $start = (int)$this->request->get('start');
        $limit = (int)$this->request->get('length');

        $orderData = $this->request->get('order');
        if ($orderData) {
            $query = $this->addOrderData($query, $fields, $orderData);
        }

        $query = $query->getQuery();

        $this->actions = $actions;
        $this->fields = $fields;

        $page = ($start > 0) ? $start / 10 + 1 : 1;

        if ($limit == -1) {
            $limit = 10000;
        }

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $page, $limit);

        $items = $this->prepareItems($pagination->getItems());

        $data = [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $pagination->getTotalItemCount(),
            'data' => $items,
        ];

        return $data;
    }

    /**
     * @param QueryBuilder $query
     * @param array $fields
     * @param $orderData
     *
     * @return QueryBuilder
     */
    private function addOrderData(QueryBuilder $query, array $fields, $orderData)
    {
        $orderField = null;
        foreach ($fields as $index => $field) {
            if ($orderData[0]['column'] == $index) {
                $orderField = $field;
            }
        }
        if ($orderField) {
            $orderDir = $orderData[0]['dir'];
            $aliases = $query->getAllAliases();
            $alias = $aliases[0];
            $query->addOrderBy("$alias.$orderField", $orderDir);
        }

        return $query;
    }

    private function prepareItems(array $items)
    {
        $data = [];
        foreach ($items as $object) {
            $row = [];
            foreach ($this->fields as $field) {
                $fieldUpper = ucfirst($field);
                $getter = "get{$fieldUpper}";
                if (method_exists($object, $getter)) {
                    $row = $this->renderValue($getter, $object, $row);
                } else {
                    $boolGetter = "is{$fieldUpper}";
                    if (method_exists($object, $boolGetter)) {
                        $row = $this->renderValue($boolGetter, $object, $row);
                    }
                }

            }
            if ($this->actions) {
                $row[] = $this->renderActions($object);
            } else {
                $row[] = $this->renderEmptyValue();
            }
            $data[] = array_values($row);
        }

        return array_values($data);
    }

    /**
     * @param $getter
     * @param EntityInterface $object
     * @param $row
     *
     * @return array
     */
    private function renderValue($getter, EntityInterface $object, $row): array
    {
        $value = $object->$getter();
        if (is_bool($value)) {
            $row[] = $this->renderBoolValue($value);
        } elseif ($value instanceof \DateTime) {
            $row[] = $this->renderDateTime($value);
        } elseif ($value instanceof PersistentCollection) {
            $row[] = $this->renderCollection($value);
        } elseif (is_object($value)) {
            $row[] = $this->renderObject($value);
        } else {
            $row[] = $value;
        }

        return $row;
    }

    private function renderBoolValue($item)
    {
        return $this->templating->render(self::TEMPLATE_FIELD_BOOL, ['item' => $item]);
    }

    private function renderDateTime(\DateTime $dateTime)
    {
        return $dateTime->format('Y-m-d');
    }

    private function renderCollection(PersistentCollection $collection)
    {
        return $this->templating->render(self::TEMPLATE_FIELD_COLLECTION, ['collection' => $collection]);
    }

    private function renderObject($object)
    {
        return $this->templating->render(self::TEMPLATE_FIELD_OBJECT, ['object' => $object]);
    }

    private function renderActions(EntityInterface $object)
    {
        $field = '';
        $actions = $this->actions;

        foreach ($actions as $name => $data) {
            if ($name == 'delete' && $object instanceof DeletableInterface && $object->isDeletable() === false) {
                continue;
            }
            $fieldUpper = ucfirst($data['params']['value']);
            $getter = "get{$fieldUpper}";

            if (isset($data['params']['association']) && $data['params']['association']) {
                $associatedObject = $object->$getter();
                $associationUpper = ucfirst($data['params']['association']);
                $associationGetter = "get{$associationUpper}";
                $value = $associatedObject->$associationGetter();
            } else {
                $value = $object->$getter();
            }

            $url = $this->router->generate(
                $data['route'],
                [
                    $data['params']['key'] => $value,
                ]
            );

            $field .= $this->templating->render(
                str_replace("%type%", $name, self::TEMPLATE_BUTTON),
                ['url' => $url, 'formId' => 'delete_form_' . $object->getId()]
            );
        }

        return $field;
    }

    /**
     * @return array
     */
    private function renderEmptyValue()
    {
        $row[] = '';

        return $row;
    }
}
