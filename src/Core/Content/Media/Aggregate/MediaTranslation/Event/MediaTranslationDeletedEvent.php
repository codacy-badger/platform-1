<?php declare(strict_types=1);

namespace Shopware\Core\Content\Media\Aggregate\MediaTranslation\Event;

use Shopware\Core\Content\Media\Aggregate\MediaTranslation\MediaTranslationDefinition;
use Shopware\Core\Framework\ORM\Write\DeletedEvent;
use Shopware\Core\Framework\ORM\Write\WrittenEvent;

class MediaTranslationDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'media_translation.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return MediaTranslationDefinition::class;
    }
}