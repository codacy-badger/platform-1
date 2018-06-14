<?php declare(strict_types=1);

namespace Shopware\Core\Content\Product\Aggregate\ProductStreamAssignment;

use Shopware\Core\Content\Product\Aggregate\ProductStream\ProductStreamDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductStreamAssignment\Event\ProductStreamAssignmentDeletedEvent;
use Shopware\Core\Content\Product\Aggregate\ProductStreamAssignment\Event\ProductStreamAssignmentWrittenEvent;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\ORM\Field\DateField;
use Shopware\Core\Framework\ORM\Field\FkField;
use Shopware\Core\Framework\ORM\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\ORM\Field\ReferenceVersionField;
use Shopware\Core\Framework\ORM\FieldCollection;
use Shopware\Core\Framework\ORM\MappingEntityDefinition;
use Shopware\Core\Framework\ORM\Write\Flag\Required;

class ProductStreamAssignmentDefinition extends MappingEntityDefinition
{
    /**
     * @var FieldCollection
     */
    protected static $fields;

    /**
     * @var FieldCollection
     */
    protected static $primaryKeys;

    public static function getEntityName(): string
    {
        return 'product_stream_assignment';
    }

    public static function getFields(): FieldCollection
    {
        if (self::$fields) {
            return self::$fields;
        }

        return self::$fields = new FieldCollection([
            (new FkField('product_stream_id', 'productStreamId', ProductStreamDefinition::class))->setFlags(new Required()),
            (new ReferenceVersionField(ProductStreamDefinition::class))->setFlags(new Required()),

            (new FkField('product_id', 'productId', ProductDefinition::class))->setFlags(new Required()),
            (new ReferenceVersionField(ProductDefinition::class))->setFlags(new Required()),

            new DateField('created_at', 'createdAt'),
            new DateField('updated_at', 'updatedAt'),
            new ManyToOneAssociationField('productStream', 'product_stream_id', ProductStreamDefinition::class, false),
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, false),
        ]);
    }

    public static function getWrittenEventClass(): string
    {
        return ProductStreamAssignmentWrittenEvent::class;
    }

    public static function getDeletedEventClass(): string
    {
        return ProductStreamAssignmentDeletedEvent::class;
    }
}