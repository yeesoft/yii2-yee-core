<?php

include Yii::getAlias('@yii/rbac/migrations/m140506_102106_rbac_init.php');

/**
 * Extends default Yii2 RBAC migration.
 */
class m150319_152141_rbac_init extends m140506_102106_rbac_init
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
        
        //$this->addColumn($authManager->ruleTable, 'class_name', $this->string(255));

        $this->createTable($authManager->groupTable, [
            'name' => $this->string(64)->notNull(),
            'title' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ], $tableOptions);
        
        $this->createTable($authManager->itemGroupTable, [
            'group_name' => $this->string(64)->notNull(),
            'item_name' => $this->string(64)->notNull(),
            'PRIMARY KEY (group_name, item_name)',
        ], $tableOptions);
        
        $this->addForeignKey('fk_auth_group_table_group_name', $authManager->itemGroupTable, ['group_name'], $authManager->groupTable, ['name'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_auth_group_table_item_name', $authManager->itemGroupTable, ['item_name'], $authManager->itemTable, ['name'], 'CASCADE', 'CASCADE');
    
        $this->createTable($authManager->routeTable, [
            'id' => $this->primaryKey(),
            'bundle' => $this->string(64)->notNull()->defaultValue(''),
            'controller' => $this->string(128)->notNull(),
            'action' => $this->string(64),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
        
        $this->createIndex('idx_uniq_route', $authManager->routeTable, ['bundle', 'controller', 'action'], true);
        
        $this->createTable($authManager->itemRouteTable, [
            'item_name' => $this->string(64)->notNull(),
            'route_id' => $this->integer()->notNull(),
            'PRIMARY KEY (item_name, route_id)',
        ], $tableOptions);
        
        $this->addForeignKey('fk_auth_item_route_item', $authManager->itemRouteTable, 'item_name', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_auth_item_route_route', $authManager->itemRouteTable, 'route_id', $authManager->routeTable, 'id', 'CASCADE', 'CASCADE');

        $this->createTable($authManager->filterTable, [
            'name' => $this->string(64)->notNull(),
            'title' => $this->string(64)->notNull(),
            'class_name' => $this->string(255)->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ], $tableOptions);
        
        $this->createIndex('idx_uniq_class_name', $authManager->filterTable, ['class_name'], true);
        
        $this->createTable($authManager->itemFilterTable, [
            'item_name' => $this->string(64)->notNull(),
            'filter_name' => $this->string(64)->notNull(),
            'PRIMARY KEY (item_name, filter_name)',
        ], $tableOptions);
        
        $this->addForeignKey('fk_auth_item_filter_item', $authManager->itemFilterTable, 'item_name', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_auth_item_filter_filter', $authManager->itemFilterTable, 'filter_name', $authManager->filterTable, 'name', 'CASCADE', 'CASCADE');
        
        $this->createTable($authManager->modelTable, [
            'name' => $this->string(64)->notNull(),
            'title' => $this->string(64)->notNull(),
            'class_name' => $this->string(255)->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ], $tableOptions);
        
        $this->createIndex('idx_uniq_class_name', $authManager->modelTable, ['class_name'], true);
        
        $this->createTable($authManager->modelFilterTable, [
            'model_name' => $this->string(64)->notNull(),
            'filter_name' => $this->string(64)->notNull(),
            'PRIMARY KEY (model_name, filter_name)',
        ], $tableOptions);
        
        $this->addForeignKey('fk_auth_model_filter_model', $authManager->modelFilterTable, 'model_name', $authManager->modelTable, 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_auth_model_filter_filter', $authManager->modelFilterTable, 'filter_name', $authManager->filterTable, 'name', 'CASCADE', 'CASCADE');
 
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;
        
        //TODO: Update down method

        $this->dropForeignKey('fk_auth_model_filter_filter', $authManager->modelFilterTable);
        $this->dropForeignKey('fk_auth_model_filter_model', $authManager->modelFilterTable);
        $this->dropTable($authManager->modelFilterTable);
        $this->dropTable($authManager->modelTable);
        
        $this->dropForeignKey('fk_auth_item_filter_filter', $authManager->itemFilterTable);
        $this->dropForeignKey('fk_auth_item_filter_item', $authManager->itemFilterTable);
        $this->dropTable($authManager->itemFilterTable);
        $this->dropTable($authManager->filterTable);
        
        $this->dropForeignKey('fk_auth_item_route_item', $authManager->itemRouteTable);
        $this->dropForeignKey('fk_auth_item_route_route', $authManager->itemRouteTable);
        $this->dropTable($authManager->itemRouteTable);
        $this->dropTable($authManager->routeTable);
        
        $this->dropForeignKey('fk_auth_group_table_group_name', $authManager->itemGroupTable);
        $this->dropForeignKey('fk_auth_group_table_item_name', $authManager->itemGroupTable);
        $this->dropTable($authManager->itemGroupTable);
        $this->dropTable($authManager->groupTable);

        parent::down();
    }

}
