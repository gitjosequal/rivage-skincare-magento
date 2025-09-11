<?php
/**
 * Profile Avatar
 * 
 * @author Slava Yurthev
 */
namespace Ds\Customeratt\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
		$connection = $setup->getConnection();
		$table = $setup->getTable('customer_entity');
		$connection->addColumn(
				$table,
				'my_skin',
				['type' => Table::TYPE_TEXT, 'nullable' => true, 'length' => '255', 'comment' => 'My Skin']
		);
		$connection->addColumn(
				$table,
				'my_hair_type',
				['type' => Table::TYPE_TEXT, 'nullable' => true, 'length' => '255', 'comment' =>'My Hair Type']
		);
		$setup->endSetup();
	}
}