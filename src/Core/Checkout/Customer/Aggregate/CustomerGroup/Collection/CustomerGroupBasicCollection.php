<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\Collection;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\Struct\CustomerGroupBasicStruct;
use Shopware\Core\Framework\ORM\EntityCollection;

class CustomerGroupBasicCollection extends EntityCollection
{
    /**
     * @var \Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\Struct\CustomerGroupBasicStruct[]
     */
    protected $elements = [];

    public function get(string $id): ? CustomerGroupBasicStruct
    {
        return parent::get($id);
    }

    public function current(): CustomerGroupBasicStruct
    {
        return parent::current();
    }

    protected function getExpectedClass(): string
    {
        return CustomerGroupBasicStruct::class;
    }
}