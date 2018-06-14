<?php declare(strict_types=1);

namespace Shopware\Storefront\Api\Seo\Event\SeoUrl;

use Shopware\Core\System\Touchpoint\Event\TouchpointBasicLoadedEvent;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\NestedEventCollection;
use Shopware\Storefront\Api\Seo\Collection\SeoUrlDetailCollection;

class SeoUrlDetailLoadedEvent extends NestedEvent
{
    public const NAME = 'seo_url.detail.loaded';

    /**
     * @var \Shopware\Core\Framework\Context
     */
    protected $context;

    /**
     * @var SeoUrlDetailCollection
     */
    protected $seoUrls;

    public function __construct(SeoUrlDetailCollection $seoUrls, Context $context)
    {
        $this->context = $context;
        $this->seoUrls = $seoUrls;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getSeoUrls(): SeoUrlDetailCollection
    {
        return $this->seoUrls;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->seoUrls->getApplications()->count() > 0) {
            $events[] = new TouchpointBasicLoadedEvent($this->seoUrls->getApplications(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}