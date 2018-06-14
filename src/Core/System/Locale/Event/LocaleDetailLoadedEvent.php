<?php declare(strict_types=1);

namespace Shopware\Core\System\Locale\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\NestedEventCollection;
use Shopware\Core\System\Locale\Aggregate\LocaleTranslation\Event\LocaleTranslationBasicLoadedEvent;
use Shopware\Core\System\Locale\Collection\LocaleDetailCollection;

class LocaleDetailLoadedEvent extends NestedEvent
{
    public const NAME = 'locale.detail.loaded';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var LocaleDetailCollection
     */
    protected $locales;

    public function __construct(LocaleDetailCollection $locales, Context $context)
    {
        $this->context = $context;
        $this->locales = $locales;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getLocales(): LocaleDetailCollection
    {
        return $this->locales;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->locales->getTranslations()->count() > 0) {
            $events[] = new LocaleTranslationBasicLoadedEvent($this->locales->getTranslations(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}