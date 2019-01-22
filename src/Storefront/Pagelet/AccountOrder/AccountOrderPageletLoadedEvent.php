<?php declare(strict_types=1);

namespace Shopware\Storefront\Pagelet\AccountOrder;

use Shopware\Core\Checkout\CheckoutContext;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;

class AccountOrderPageletLoadedEvent extends NestedEvent
{
    public const NAME = 'account-order.pagelet.loaded.event';

    /**
     * @var AccountOrderPageletStruct
     */
    protected $pagelet;

    /**
     * @var CheckoutContext
     */
    protected $context;

    /**
     * @var AccountOrderPageletRequest
     */
    protected $request;

    public function __construct(
        AccountOrderPageletStruct $pagelet,
        CheckoutContext $context,
        AccountOrderPageletRequest $request
    ) {
        $this->pagelet = $pagelet;
        $this->context = $context;
        $this->request = $request;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getCheckoutContext(): CheckoutContext
    {
        return $this->context;
    }

    public function getPagelet(): AccountOrderPageletStruct
    {
        return $this->pagelet;
    }

    public function getRequest(): AccountOrderPageletRequest
    {
        return $this->request;
    }
}