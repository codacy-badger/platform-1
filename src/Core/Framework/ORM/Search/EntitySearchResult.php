<?php declare(strict_types=1);

namespace Shopware\Core\Framework\ORM\Search;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\ORM\EntityCollection;

class EntitySearchResult
{
    /**
     * @var int
     */
    protected $total;

    /**
     * @var EntityCollection
     */
    protected $entities;

    /**
     * @var AggregatorResult
     */
    protected $aggregations;

    /**
     * @var Criteria
     */
    protected $criteria;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        int $total,
        EntityCollection $entities,
        AggregatorResult $aggregations,
        Criteria $criteria,
        Context $context
    ) {
        $this->total = $total;
        $this->entities = $entities;
        $this->aggregations = $aggregations;
        $this->criteria = $criteria;
        $this->context = $context;
    }
}