<?php

declare(strict_types=1);

namespace HealthCheckBundle\Service;

use HealthCheckBundle\Entity\SqlChecker;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 健康检查菜单服务
 */
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('系统管理')) {
            $item->addChild('系统管理');
        }

        $systemMenu = $item->getChild('系统管理');
        if (null === $systemMenu) {
            return;
        }

        // SQL健康检查菜单
        $systemMenu->addChild('SQL健康检查')
            ->setUri($this->linkGenerator->getCurdListPage(SqlChecker::class))
            ->setAttribute('icon', 'fas fa-heartbeat')
        ;
    }
}
