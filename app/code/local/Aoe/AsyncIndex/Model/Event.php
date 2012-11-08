<?php

class Aoe_AsyncIndex_Model_Event extends Mage_Index_Model_Event {

    public function getDataObject() {
        if (!$this->hasData('data_object')) {
            // reconstruct data object
            $dataObject = Mage::getModel($this->getData('data_object_model'));
            $dataObject->load($this->getData('data_object_id'));
            $this->setData('data_object', $dataObject);
        }
        return $this->getData('data_object');
    }

    public function dereferenceDataObject() {
        if ($this->hasData('data_object')) {
            $dataObject = $this->getData('data_object'); /* @var $dataObject Mage_Core_Model_Abstract */

            $model = get_class($dataObject);
            $id = $dataObject->getId();

            $this->setData('data_object_model', $model);
            $this->setData('data_object_id', $id);

            $this->unsetData('data_object');
        }
        return $this;
    }

}