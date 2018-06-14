<?php declare(strict_types=1);

namespace Shopware\Core\System\Tax\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\NestedEventCollection;
use Shopware\Core\System\Tax\Aggregate\TaxAreaRule\Event\TaxAreaRuleBasicLoadedEvent;
use Shopware\Core\System\Tax\Collection\TaxDetailCollection;

class TaxDetailLoadedEvent extends NestedEvent
{
    public const NAME = 'tax.detail.loaded';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var TaxDetailCollection
     */
    protected $taxes;

    public function __construct(TaxDetailCollection $taxes, Context $context)
    {
        $this->context = $context;
        $this->taxes = $taxes;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getTaxes(): TaxDetailCollection
    {
        return $this->taxes;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->taxes->getAreaRules()->count() > 0) {
            $events[] = new TaxAreaRuleBasicLoadedEvent($this->taxes->getAreaRules(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}