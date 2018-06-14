<?php declare(strict_types=1);

namespace Shopware\Core\System\Tax\Aggregate\TaxAreaRule;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupDefinition;
use Shopware\Core\Framework\ORM\EntityDefinition;
use Shopware\Core\Framework\ORM\EntityExtensionInterface;
use Shopware\Core\Framework\ORM\Field\BoolField;
use Shopware\Core\Framework\ORM\Field\DateField;
use Shopware\Core\Framework\ORM\Field\FkField;
use Shopware\Core\Framework\ORM\Field\FloatField;
use Shopware\Core\Framework\ORM\Field\IdField;
use Shopware\Core\Framework\ORM\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\ORM\Field\ReferenceVersionField;
use Shopware\Core\Framework\ORM\Field\StringField;
use Shopware\Core\Framework\ORM\Field\TenantIdField;
use Shopware\Core\Framework\ORM\Field\TranslatedField;
use Shopware\Core\Framework\ORM\Field\TranslationsAssociationField;
use Shopware\Core\Framework\ORM\Field\VersionField;
use Shopware\Core\Framework\ORM\FieldCollection;
use Shopware\Core\Framework\ORM\Write\Flag\CascadeDelete;
use Shopware\Core\Framework\ORM\Write\Flag\PrimaryKey;
use Shopware\Core\Framework\ORM\Write\Flag\Required;
use Shopware\Core\Framework\ORM\Write\Flag\SearchRanking;
use Shopware\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use Shopware\Core\System\Country\CountryDefinition;
use Shopware\Core\System\Tax\Aggregate\TaxAreaRule\Collection\TaxAreaRuleBasicCollection;
use Shopware\Core\System\Tax\Aggregate\TaxAreaRule\Collection\TaxAreaRuleDetailCollection;
use Shopware\Core\System\Tax\Aggregate\TaxAreaRule\Event\TaxAreaRuleDeletedEvent;
use Shopware\Core\System\Tax\Aggregate\TaxAreaRule\Event\TaxAreaRuleWrittenEvent;
use Shopware\Core\System\Tax\Aggregate\TaxAreaRule\Struct\TaxAreaRuleBasicStruct;
use Shopware\Core\System\Tax\Aggregate\TaxAreaRule\Struct\TaxAreaRuleDetailStruct;
use Shopware\Core\System\Tax\Aggregate\TaxAreaRuleTranslation\TaxAreaRuleTranslationDefinition;
use Shopware\Core\System\Tax\TaxDefinition;

class TaxAreaRuleDefinition extends EntityDefinition
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
        return 'tax_area_rule';
    }

    public static function getFields(): FieldCollection
    {
        if (self::$fields) {
            return self::$fields;
        }

        self::$fields = new FieldCollection([
            new TenantIdField(),
            (new IdField('id', 'id'))->setFlags(new PrimaryKey(), new Required()),
            new VersionField(),

            (new FkField('tax_id', 'taxId', TaxDefinition::class))->setFlags(new Required()),
            (new ReferenceVersionField(TaxDefinition::class))->setFlags(new Required()),

            new FkField('country_area_id', 'countryAreaId', \Shopware\Core\System\Country\Aggregate\CountryArea\CountryAreaDefinition::class),
            new ReferenceVersionField(\Shopware\Core\System\Country\Aggregate\CountryArea\CountryAreaDefinition::class),

            new FkField('country_id', 'countryId', CountryDefinition::class),
            new ReferenceVersionField(\Shopware\Core\System\Country\CountryDefinition::class),

            new FkField('country_state_id', 'countryStateId', CountryStateDefinition::class),
            new ReferenceVersionField(CountryStateDefinition::class),

            (new FkField('customer_group_id', 'customerGroupId', CustomerGroupDefinition::class))->setFlags(new Required()),
            (new ReferenceVersionField(CustomerGroupDefinition::class))->setFlags(new Required()),

            (new FloatField('tax_rate', 'taxRate'))->setFlags(new Required()),
            (new TranslatedField(new StringField('name', 'name')))->setFlags(new SearchRanking(self::HIGH_SEARCH_RANKING)),
            new BoolField('active', 'active'),
            new DateField('created_at', 'createdAt'),
            new DateField('updated_at', 'updatedAt'),
            new ManyToOneAssociationField('countryArea', 'country_area_id', \Shopware\Core\System\Country\Aggregate\CountryArea\CountryAreaDefinition::class, false),
            new ManyToOneAssociationField('country', 'country_id', \Shopware\Core\System\Country\CountryDefinition::class, false),
            new ManyToOneAssociationField('countryState', 'country_state_id', CountryStateDefinition::class, false),
            new ManyToOneAssociationField('tax', 'tax_id', TaxDefinition::class, false),
            new ManyToOneAssociationField('customerGroup', 'customer_group_id', CustomerGroupDefinition::class, false),
            (new TranslationsAssociationField('translations', TaxAreaRuleTranslationDefinition::class, 'tax_area_rule_id', false, 'id'))->setFlags(new Required(), new CascadeDelete()),
        ]);

        foreach (self::$extensions as $extension) {
            $extension->extendFields(self::$fields);
        }

        return self::$fields;
    }

    public static function getRepositoryClass(): string
    {
        return TaxAreaRuleRepository::class;
    }

    public static function getBasicCollectionClass(): string
    {
        return TaxAreaRuleBasicCollection::class;
    }

    public static function getDeletedEventClass(): string
    {
        return TaxAreaRuleDeletedEvent::class;
    }

    public static function getWrittenEventClass(): string
    {
        return TaxAreaRuleWrittenEvent::class;
    }

    public static function getBasicStructClass(): string
    {
        return TaxAreaRuleBasicStruct::class;
    }

    public static function getTranslationDefinitionClass(): ?string
    {
        return TaxAreaRuleTranslationDefinition::class;
    }

    public static function getDetailStructClass(): string
    {
        return TaxAreaRuleDetailStruct::class;
    }

    public static function getDetailCollectionClass(): string
    {
        return TaxAreaRuleDetailCollection::class;
    }
}