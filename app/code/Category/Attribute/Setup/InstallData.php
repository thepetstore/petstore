<?php
 
namespace Category\Attribute\Setup;
 
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
 
class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
 
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
	    $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]); 
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_description',
            [
                'type' => 'text',
                'label' => 'Category Display Description',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 4,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
                'visible'  => true,
                'group' => 'General Information',
            ]
        );

         $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'is_small_banner', [
            'type'     => 'int',
            'label'    => 'Is Small Banner',
            'input'    => 'boolean',
            'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'visible'  => true,
            'default'  => '0',
            'required' => false,
            'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group'    => 'Display Settings',
        ]);
 
        $setup->endSetup();
    }
}