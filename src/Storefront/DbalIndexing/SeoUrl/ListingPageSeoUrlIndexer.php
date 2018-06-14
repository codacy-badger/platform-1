<?php declare(strict_types=1);

namespace Shopware\Storefront\DbalIndexing\SeoUrl;

use Cocur\Slugify\SlugifyInterface;
use Doctrine\DBAL\Connection;
use Shopware\Core\System\Touchpoint\TouchpointRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Content\Category\CategoryRepository;
use Shopware\Core\Content\Category\Struct\CategorySearchResult;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Doctrine\MultiInsertQueryQueue;
use Shopware\Core\Framework\Event\ProgressAdvancedEvent;
use Shopware\Core\Framework\Event\ProgressFinishedEvent;
use Shopware\Core\Framework\Event\ProgressStartedEvent;
use Shopware\Core\Content\Product\Util\EventIdExtractor;
use Shopware\Core\Framework\ORM\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\ORM\Dbal\Indexing\IndexerInterface;
use Shopware\Core\Framework\ORM\Search\Criteria;
use Shopware\Core\Framework\ORM\Write\GenericWrittenEvent;
use Shopware\Core\Framework\Struct\Uuid;
use Shopware\Storefront\Api\Seo\Definition\SeoUrlDefinition;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

class ListingPageSeoUrlIndexer implements IndexerInterface
{
    public const ROUTE_NAME = 'listing_page';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var SlugifyInterface
     */
    private $slugify;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TouchpointRepository
     */
    private $applicationRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \Shopware\Core\Content\Product\Util\EventIdExtractor
     */
    private $eventIdExtractor;

    public function __construct(
        Connection $connection,
        SlugifyInterface $slugify,
        RouterInterface $router,
        CategoryRepository $categoryRepository,
        TouchpointRepository $applicationRepository,
        EventDispatcherInterface $eventDispatcher,
        EventIdExtractor $eventIdExtractor
    ) {
        $this->connection = $connection;
        $this->slugify = $slugify;
        $this->router = $router;
        $this->applicationRepository = $applicationRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->categoryRepository = $categoryRepository;
        $this->eventIdExtractor = $eventIdExtractor;
    }

    public function index(\DateTime $timestamp, string $tenantId): void
    {
        $applications = $this->applicationRepository->search(new Criteria(), Context::createDefaultContext($tenantId));

        foreach ($applications as $application) {
            $context = Context::createFromTouchpoint($application);

            $iterator = new RepositoryIterator($this->categoryRepository, $context);

            $this->eventDispatcher->dispatch(
                ProgressStartedEvent::NAME,
                new ProgressStartedEvent(
                    sprintf('Start indexing listing page seo urls for application %s', $application->getName()),
                    $iterator->getTotal()
                )
            );

            /* @var CategorySearchResult $categories */
            while ($ids = $iterator->fetchIds()) {
                $this->updateCategories($ids, $context);

                $this->eventDispatcher->dispatch(
                    ProgressAdvancedEvent::NAME,
                    new ProgressAdvancedEvent(count($ids))
                );
            }

            $this->eventDispatcher->dispatch(
                ProgressFinishedEvent::NAME,
                new ProgressFinishedEvent(sprintf('Finished indexing listing page seo urls for application %s', $application->getName()))
            );
        }
    }

    public function refresh(GenericWrittenEvent $event): void
    {
        $ids = $this->eventIdExtractor->getCategoryIds($event);

        $this->updateCategories($ids, $event->getContext());
    }

    private function fetchCanonicals(array $categoryIds, string $applicationId, string $tenantId): array
    {
        $categoryIds = array_map(function ($id) {
            return Uuid::fromStringToBytes($id);
        }, $categoryIds);

        $query = $this->connection->createQueryBuilder();

        $query->select([
            'HEX(seo_url.foreign_key) as categoryId',
            'seo_url.id as id',
            'seo_url.is_modified as isModified',
        ]);
        $query->from('seo_url', 'seo_url');

        $query->andWhere('seo_url.name = :name');
        $query->andWhere('seo_url.touchpoint_id = :application');
        $query->andWhere('seo_url.is_canonical = 1');
        $query->andWhere('seo_url.tenant_id = :tenant');
        $query->andWhere('seo_url.foreign_key IN (:ids)');

        $query->setParameter('ids', $categoryIds, Connection::PARAM_STR_ARRAY);
        $query->setParameter('name', self::ROUTE_NAME);
        $query->setParameter('application', Uuid::fromStringToBytes($applicationId));
        $query->setParameter('tenant', Uuid::fromStringToBytes($tenantId));

        return $query->execute()->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_UNIQUE);
    }

    private function updateCategories(array $ids, Context $context): void
    {
        if (empty($ids)) {
            return;
        }

        $categories = $this->categoryRepository->readBasic($ids, $context);

        $canonicals = $this->fetchCanonicals($categories->getIds(), $context->getTouchpointId(), $context->getTenantId());

        $liveVersionId = Uuid::fromStringToBytes(Defaults::LIVE_VERSION);
        $insertQuery = new MultiInsertQueryQueue($this->connection, 250, false, true);

        $timestamp = new \DateTime();

        foreach ($categories as $category) {
            $existing = [
                'id' => Uuid::uuid4()->getBytes(),
                'isModified' => 0,
            ];
            if (array_key_exists($category->getId(), $canonicals)) {
                $existing = $canonicals[$category->getId()];
            }

            if ($existing['isModified']) {
                continue;
            }

            $pathInfo = $this->router->generate(self::ROUTE_NAME, ['id' => $category->getId()]);

            $names = $category->getPathNamesArray();
            $names[] = $category->getName();

            $seoUrl = array_map(function (string $name) {
                return $this->slugify->slugify($name);
            }, $names);

            if (empty($seoUrl)) {
                continue;
            }

            $seoUrl = implode('/', $seoUrl);

            $data = [
                'id' => $existing['id'],
                'tenant_id' => Uuid::fromStringToBytes($context->getTenantId()),
                'version_id' => $liveVersionId,
                'touchpoint_id' => Uuid::fromStringToBytes($context->getTouchpointId()),
                'touchpoint_tenant_id' => Uuid::fromStringToBytes($context->getTenantId()),
                'name' => self::ROUTE_NAME,
                'foreign_key' => Uuid::fromStringToBytes($category->getId()),
                'foreign_key_version_id' => $liveVersionId,
                'path_info' => $pathInfo,
                'seo_path_info' => $seoUrl,
                'is_canonical' => 1,
                'is_modified' => 0,
                'created_at' => $timestamp->format('Y-m-d H:i:s'),
                'updated_at' => $timestamp->format('Y-m-d H:i:s'),
            ];

            $insertQuery->addInsert(SeoUrlDefinition::getEntityName(), $data);
        }

        $insertQuery->execute();
    }
}