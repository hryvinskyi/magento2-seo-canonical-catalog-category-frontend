<?php
/**
 * Copyright (c) 2021. All rights reserved.
 * @author: Volodymyr Hryvinskyi <mailto:volodymyr@hryvinskyi.com>
 */

declare(strict_types=1);

namespace Hryvinskyi\SeoCanonicalCatalogCategoryFrontend\Model;

use Hryvinskyi\SeoCanonicalFrontend\Model\AbstractCanonicalUrlProcess;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\HttpRequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class CanonicalUrlProcess extends AbstractCanonicalUrlProcess
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $actions
     */
    public function __construct(
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        array $actions = [],
        UrlInterface $url = null
    ) {
        parent::__construct($actions);

        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
        $this->url = $url ?: \Magento\Framework\App\ObjectManager::getInstance()->get(UrlInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function execute(HttpRequestInterface $request): ?string
    {
        $category = $this->registry->registry('current_category');
        if (!$category) {
            return null;
        }
        // Need load because amasty shopby-seo module change registry category
        $category = $this->categoryRepository->get($category->getId());

        if ($customCanonical = $category->getData('custom_canonical_url')) {
            return rtrim($this->url->getBaseUrl(),  '/') . '/' . ltrim($customCanonical, '/');
        }

        return $category->getUrl();
    }
}
