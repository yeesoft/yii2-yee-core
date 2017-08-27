<?php

include Yii::getAlias('@yii/rbac/migrations/m140506_102106_rbac_init.php');

/**
 * Extends default Yii2 RBAC migration.
 */
class m150319_152141_init_rbac extends m140506_102106_rbac_init
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        parent::up();

        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($authManager->itemGroupTable, [
            'code' => $this->string(64)->notNull(),
            'name' => $this->string(255)->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (code)',
        ], $tableOptions);

        $this->addColumn($authManager->itemTable, 'group_code', $this->string(64));
        $this->addForeignKey('fk_auth_item_table_group_code', $authManager->itemTable, ['group_code'], $authManager->itemGroupTable, ['code'], 'SET NULL', 'CASCADE');
    
        $this->addColumn($authManager->ruleTable, 'class_name', $this->string(255));
        
        $this->createTable($authManager->routeTable, [
            'id' => $this->primaryKey(),
            'base_url' => $this->string(63),
            'controller' => $this->string(127)->notNull(),
            'action' => $this->string(63),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
               
        $this->createIndex('idx_uniq_route', $authManager->routeTable, ['base_url', 'controller', 'action'], true);
        
        $this->createTable($authManager->itemRouteTable, [
            'id' => $this->primaryKey(),
            'item_name' => $this->string(64)->notNull(),
            'route_id' => $this->integer()->notNull(),
        ], $tableOptions);
        
        $this->createIndex('idx_auth_item_route', $authManager->itemRouteTable, ['item_name', 'route_id'], true);
        $this->addForeignKey('fk_auth_item_route_item', $authManager->itemRouteTable, 'item_name', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_auth_item_route_route', $authManager->itemRouteTable, 'route_id', $authManager->routeTable, 'id', 'CASCADE', 'CASCADE');

        $this->createTable($authManager->filterTable, [
            'id' => $this->primaryKey(),
            'name' => $this->string(127)->notNull(),
            'class_name' => $this->string(255)->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
               
        $this->createIndex('idx_uniq_class_name', $authManager->filterTable, ['class_name'], true);
        
        $this->createTable($authManager->itemFilterTable, [
            'id' => $this->primaryKey(),
            'item_name' => $this->string(64)->notNull(),
            'filter_id' => $this->integer()->notNull(),
        ], $tableOptions);
        
        $this->createIndex('idx_auth_item_filter', $authManager->itemFilterTable, ['item_name', 'filter_id'], true);
        $this->addForeignKey('fk_auth_item_filter_item', $authManager->itemFilterTable, 'item_name', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_auth_item_filter_filter', $authManager->itemFilterTable, 'filter_id', $authManager->filterTable, 'id', 'CASCADE', 'CASCADE');
        
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->dropForeignKey('fk_auth_item_filter_filter', $authManager->itemFilterTable);
        $this->dropForeignKey('fk_auth_item_filter_item', $authManager->itemFilterTable);
        $this->dropTable($authManager->itemFilterTable);
        $this->dropTable($authManager->filterTable);
        
        $this->dropForeignKey('fk_auth_item_route_item', $authManager->itemRouteTable);
        $this->dropForeignKey('fk_auth_item_route_route', $authManager->itemRouteTable);
        $this->dropTable($authManager->itemRouteTable);
        $this->dropTable($authManager->routeTable);
        
        $this->dropTable($authManager->itemGroupTable);

        parent::down();
    }

}
