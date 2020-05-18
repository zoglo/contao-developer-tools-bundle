<?php

declare(strict_types=1);

namespace Zoglo\ContaoDeveloperToolsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Zoglo\ContaoDeveloperToolsBundle\ContaoDeveloperToolsBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoDeveloperToolsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['developer-tools']),
        ];
    }
}
