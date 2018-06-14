<?php declare(strict_types=1);

namespace Shopware\Core\System\Touchpoint;

use Shopware\Core\System\Touchpoint\Collection\TouchpointBasicCollection;
use Shopware\Core\System\Touchpoint\Collection\TouchpointDetailCollection;
use Shopware\Core\System\Touchpoint\Event\TouchpointDeletedEvent;
use Shopware\Core\System\Touchpoint\Event\TouchpointWrittenEvent;
use Shopware\Core\System\Touchpoint\Struct\TouchpointBasicStruct;
use Shopware\Core\System\Touchpoint\Struct\TouchpointDetailStruct;
use Shopware\Core\System\Language\LanguageDefinition;
use Shopware\Core\Checkout\Payment\PaymentMethodDefinition;
use Shopware\Core\Checkout\Shipping\ShippingMethodDefinition;
use Shopware\Core\Framework\ORM\EntityDefinition;
use Shopware\Core\Framework\ORM\EntityExtensionInterface;
use Shopware\Core\Framework\ORM\Field\BoolField;
use Shopware\Core\Framework\ORM\Field\DateField;
use Shopware\Core\Framework\ORM\Field\FkField;
use Shopware\Core\Framework\ORM\Field\IdField;
use Shopware\Core\Framework\ORM\Field\JsonField;
use Shopware\Core\Framework\ORM\Field\ListField;
use Shopware\Core\Framework\ORM\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\ORM\Field\ReferenceVersionField;
use Shopware\Core\Framework\ORM\Field\StringField;
use Shopware\Core\Framework\ORM\Field\TenantIdField;
use Shopware\Core\Framework\ORM\FieldCollection;
use Shopware\Core\Framework\ORM\Write\Flag\PrimaryKey;
use Shopware\Core\Framework\ORM\Write\Flag\Required;

class TouchpointDefinition extends EntityDefinition
{
    /**
     * @var FieldCollection
     */
    protected static $primaryKeys;

    /**
     * @var FieldCollection
     */
    protected static $fields;

    /**
     * @var EntityExtensionInterface[]
     */
    protected static $extensions = [];

    public static function getEntityName(): string
    {
        return 'touchpoint';
    }

    public static function getFields(): FieldCollection
    {
        if (self::$fields) {
            return self::$fields;
        }

        self::$fields = new FieldCollection([
            new TenantIdField(),
            (new IdField('id', 'id'))->setFlags(new PrimaryKey(), new Required()),
            (new FkField('language_id', 'languageId', LanguageDefinition::class))->setFlags(new Required()),
            (new FkField('currency_id', 'currencyId', \Shopware\Core\System\Currency\CurrencyDefinition::class))->setFlags(new Required()),
            new ReferenceVersionField(\Shopware\Core\System\Currency\CurrencyDefinition::class),
            (new FkField('payment_method_id', 'paymentMethodId', PaymentMethodDefinition::class))->setFlags(new Required()),
            new ReferenceVersionField(PaymentMethodDefinition::class),
            (new FkField('shipping_method_id', 'shippingMethodId', ShippingMethodDefinition::class))->setFlags(new Required()),
            new ReferenceVersionField(\Shopware\Core\Checkout\Shipping\ShippingMethodDefinition::class),
            (new FkField('country_id', 'countryId', \Shopware\Core\System\Country\CountryDefinition::class))->setFlags(new Required()),
            new ReferenceVersionField(\Shopware\Core\System\Country\CountryDefinition::class),
            (new StringField('type', 'type'))->setFlags(new Required()),
            (new StringField('name', 'name'))->setFlags(new Required()),
            (new StringField('access_key', 'accessKey'))->setFlags(new Required()),
            (new StringField('secret_access_key', 'secretAccessKey'))->setFlags(new Required()),
            (new ListField('catalog_ids', 'catalogIds', IdField::class))->setFlags(new Required()),
            (new ListField('currency_ids', 'currencyIds', IdField::class))->setFlags(new Required()),
            (new ListField('language_ids', 'languageIds', IdField::class))->setFlags(new Required()),
            new JsonField('configuration', 'configuration'),
            new BoolField('active', 'active'),
            new StringField('tax_calculation_type', 'taxCalculationType'),
            new DateField('created_at', 'createdAt'),
            new DateField('updated_at', 'updatedAt'),
            new ManyToOneAssociationField('language', 'language_id', LanguageDefinition::class, true),
            new ManyToOneAssociationField('currency', 'currency_id', \Shopware\Core\System\Currency\CurrencyDefinition::class, true),
            new ManyToOneAssociationField('paymentMethod', 'payment_method_id', PaymentMethodDefinition::class, false),
            new ManyToOneAssociationField('shippingMethod', 'shipping_method_id', ShippingMethodDefinition::class, false),
            new ManyToOneAssociationField('country', 'country_id', \Shopware\Core\System\Country\CountryDefinition::class, false),
        ]);

        foreach (self::$extensions as $extension) {
            $extension->extendFields(self::$fields);
        }

        return self::$fields;
    }

    public static function getRepositoryClass(): string
    {
        return TouchpointRepository::class;
    }

    public static function getBasicCollectionClass(): string
    {
        return TouchpointBasicCollection::class;
    }

    public static function getDeletedEventClass(): string
    {
        return TouchpointDeletedEvent::class;
    }

    public static function getWrittenEventClass(): string
    {
        return TouchpointWrittenEvent::class;
    }

    public static function getBasicStructClass(): string
    {
        return TouchpointBasicStruct::class;
    }

    public static function getTranslationDefinitionClass(): ?string
    {
        return null;
    }

    public static function getDetailStructClass(): string
    {
        return TouchpointDetailStruct::class;
    }

    public static function getDetailCollectionClass(): string
    {
        return TouchpointDetailCollection::class;
    }
}