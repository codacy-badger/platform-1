<?php declare(strict_types=1);

namespace Shopware\Storefront\Navigation;

use Shopware\Core\Framework\Context;
use Shopware\Core\Content\Category\CategoryRepository;
use Shopware\Core\Content\Category\Struct\CategoryBasicStruct;
use Shopware\Core\Content\Category\Util\Tree\TreeBuilder;
use Shopware\Core\Framework\ORM\Search\Criteria;
use Shopware\Core\Framework\ORM\Search\Query\TermQuery;
use Shopware\Core\Framework\ORM\Search\Query\TermsQuery;

class NavigationService
{
    /**
     * @var CategoryRepository
     */
    private $repository;

    /**
     * @var Navigation[]
     */
    private $navigation;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function load(?string $categoryId, Context $context): ?Navigation
    {
        $applicationId = $context->getTouchpointId();

        if ($this->navigation[$applicationId]) {
            return $this->navigation[$applicationId];
        }

        $criteria = new Criteria();
        $criteria->addFilter(new TermQuery('category.parentId', null));
        $criteria->addFilter(new TermQuery('category.active', true));

        $rootCategories = $this->repository->search($criteria, $context);
        $rootIds = [];

        if ($categoryId) {
            $activeCategory = $this->repository->readBasic([$categoryId], $context)->get($categoryId);

            if ($activeCategory) {
                $rootIds = array_merge($activeCategory->getPathArray(), [$categoryId]);
            }
        }

        $criteria = new Criteria();
        $criteria->addFilter(new TermsQuery('category.parentId', $rootIds));
        $criteria->addFilter(new TermQuery('category.active', 1));

        $leafCategories = $this->repository->search($criteria, $context);

        $activeCategory = $rootCategories->filter(function (CategoryBasicStruct $category) use ($categoryId) {
            return $category->getId() === $categoryId;
        })->first();

        if (!$activeCategory) {
            $activeCategory = $leafCategories->filter(function (CategoryBasicStruct $category) use ($categoryId) {
                return $category->getId() === $categoryId;
            })->first();
        }

        $tree = TreeBuilder::buildTree(null, $rootCategories->sortByPosition()->sortByName());

        foreach ($tree as $index => $rootItem) {
            $rootItem->addChildren(...TreeBuilder::buildTree($rootItem->getCategory()->getId(), $leafCategories->sortByPosition()->sortByName()));
        }

        return $this->navigation[$applicationId] = new Navigation($activeCategory, $tree);
    }
}