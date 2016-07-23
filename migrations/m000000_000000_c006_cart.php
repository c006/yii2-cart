<?php


/**
 * Class m000000_000000_c006_cart
 */
class m000000_000000_c006_cart extends \yii\db\Migration
{
    /**
     *  ~CONSOLE~
     *  php yii migrate --migrationPath=@vendor/c006/yii2-alias-url/migrations
     */

    /**
     *
     */
    public function up()
    {

        self::down();

        $tables = Yii::$app->db->schema->getTableNames();
        $dbType = $this->db->driverName;
        $tableOptions_mysql = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";
        $tableOptions_mssql = "";
        $tableOptions_pgsql = "";
        $tableOptions_sqlite = "";
        /* MYSQL */
        if (!in_array('cart', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%cart}}', [
                    'id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`id`)',
                    'session_id' => 'CHAR(26) NULL',
                    'product_id' => 'INT(10) UNSIGNED NULL',
                    'image' => 'VARCHAR(100) NULL',
                    'model' => 'VARCHAR(20) NULL',
                    'name' => 'VARCHAR(100) NULL',
                    'quantity' => 'SMALLINT(6) NULL',
                    'price' => 'DECIMAL(10,2) NULL',
                    'discount' => 'DECIMAL(10,2) NULL',
                    'discount_type_id' => 'SMALLINT(5) UNSIGNED NULL',
                    'auto_ship' => 'VARCHAR(45) NULL',
                    'shipping_id' => 'SMALLINT(5) UNSIGNED NOT NULL',
                ], $tableOptions_mysql);
            }
        }
    }


    /**
     * @return bool
     */
    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `cart`');
        $this->execute('SET foreign_key_checks = 1;');
    }
}
