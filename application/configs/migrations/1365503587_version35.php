<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version35 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('products_tranches', 'setupfee', 'float', '10', array(
             'default' => '0.00',
             'notnull' => '1',
             ));
    }

    public function down()
    {
        $this->removeColumn('products_tranches', 'setupfee');
    }
}