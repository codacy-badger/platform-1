<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\Collection\CustomerAddressDetailCollection;
use Shopware\Core\Checkout\Customer\Event\CustomerBasicLoadedEvent;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\NestedEventCollection;
use Shopware\Core\System\Country\Aggregate\CountryState\Event\CountryStateBasicLoadedEvent;
use Shopware\Core\System\Country\Event\CountryBasicLoadedEvent;

class CustomerAddressDetailLoadedEvent extends NestedEvent
{
    public const NAME = 'customer_address.detail.loaded';

    /**
     * @var \Shopware\Core\Framework\Context
     */
    protected $context;

    /**
     * @var \Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\Collection\CustomerAddressDetailCollection
     */
    protected $customerAddresses;

    public function __construct(CustomerAddressDetailCollection $customerAddresses, Context $context)
    {
        $this->context = $context;
        $this->customerAddresses = $customerAddresses;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getCustomerAddresses(): CustomerAddressDetailCollection
    {
        return $this->customerAddresses;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->customerAddresses->getCustomers()->count() > 0) {
            $events[] = new CustomerBasicLoadedEvent($this->customerAddresses->getCustomers(), $this->context);
        }
        if ($this->customerAddresses->getCountries()->count() > 0) {
            $events[] = new CountryBasicLoadedEvent($this->customerAddresses->getCountries(), $this->context);
        }
        if ($this->customerAddresses->getCountryStates()->count() > 0) {
            $events[] = new CountryStateBasicLoadedEvent($this->customerAddresses->getCountryStates(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}