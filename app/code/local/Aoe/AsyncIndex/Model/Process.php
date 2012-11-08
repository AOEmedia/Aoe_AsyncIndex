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

    /**
     * Process event with assigned indexer object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Index_Model_Process
     */
    public function processEvent(Mage_Index_Model_Event $event) {
        if (!$this->matchEvent($event)) {
            return $this;
        }
        if ($this->getMode() == self::MODE_ASYNC) {
            $this->changeStatus(self::STATUS_REQUIRE_REINDEX);

            if (!$event instanceof Aoe_AsyncIndex_Model_Event) {
                Mage::throwException('Invalid event class');
            }

            $event->dereferenceDataObject();

            $queue = Mage::getModel('aoe_queue/queue', 'aoe_asyncindex_' . $this->getIndexerCode()); /* @var $queue Aoe_Queue_Model_Queue */
            $queue->addTask('aoe_asyncindex/process::actuallyProcessEvent', array($this->getIndexerCode(), $event));
            return $this;
        }
        return parent::processEvent($event);
    }

    public function actuallyProcessEvent($indexerCode, Mage_Index_Model_Event $event) {
        $this->setIndexerCode($indexerCode);

        $this->_getResource()->updateProcessStartDate($this);
        $this->_setEventNamespace($event);
        $isError = false;

        try {
            $this->getIndexer()->processEvent($event);
        } catch (Exception $e) {
            $isError = true;
        }
        $event->resetData();
        $this->_resetEventNamespace($event);
        $this->_getResource()->updateProcessEndDate($this);
        $event->addProcessId($this->getId(), $isError ? self::EVENT_STATUS_ERROR : self::EVENT_STATUS_DONE);
    }

}