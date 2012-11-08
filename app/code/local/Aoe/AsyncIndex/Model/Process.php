<?php

/**
 * Process
 *
 * @author Fabrizio Branca
 * @since 2012-11-08
 */
class Aoe_AsyncIndex_Model_Process extends Mage_Index_Model_Process {

    const MODE_ASYNC = 'async';

    /**
     * Adding new otion to the modes options
     *
     * @return array
     */
    public function getModesOptions() {
        $modesOptions = parent::getModesOptions();
        $modesOptions[self::MODE_ASYNC] = Mage::helper('index')->__('Async Queue');
        return $modesOptions;
    }

}