<?php
/**
 * Brain Station 23
 *
 * @category   BrainStation23
 * @package    EmiManagement
 * @author     Brain Station 23
 * @copyright  Copyright (c) 2026 Brain Station 23
 */

declare(strict_types=1);

namespace BrainStation23\EmiManagement\Setup\Patch\Data;

use BrainStation23\EmiManagement\Model\Product\Attribute\Source\Banks;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddEmiProductAttributes implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    public function apply(): self
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_has_emi',
            [
                'type' => 'int',
                'label' => 'Enable EMI',
                'input' => 'boolean',
                'source' => Boolean::class,
                'required' => false,
                'default' => '1',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'EMI Configuration',
                'sort_order' => 10,
                'visible' => true,
                'user_defined' => true,
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'emi_applicable_banks',
            [
                'type' => 'varchar',
                'label' => 'Available Banks',
                'input' => 'multiselect',
                'source' => Banks::class,
                'backend' => ArrayBackend::class,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'EMI Configuration',
                'sort_order' => 20,
                'visible' => true,
                'user_defined' => true,
                'used_in_product_listing' => false,
                'note' => 'Leave empty to allow all active banks.',
            ]
        );

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
