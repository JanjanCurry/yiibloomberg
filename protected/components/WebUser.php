<?php class WebUser extends CWebUser {

    public function checkToolAccess ($tool, $itemName, $userId = null) {
        $userId = (empty($userid) ? $this->getId() : $userId);
        if ($this->checkAccess($itemName)) {
            return Yii::app()->authManager->checkToolAccess($tool, $itemName, $userId);
        }

        return false;
    }

    public function getRoles () {
        return Yii::app()->authManager->getRoles(Yii::app()->user->id);
    }

    public function isAssigned ($itemName) {
        return Yii::app()->authManager->isAssigned($itemName, Yii::app()->user->id);
    }

    public function isPromo ($tool, $userId = null) {
        $userId = (empty($userid) ? $this->getId() : $userId);

        return Yii::app()->authManager->isPromo($tool, $userId);
    }

    public function setFlash ($key, $value, $defaultValue = null) {
        $messages = array();
        $state = $this->getState(self::FLASH_KEY_PREFIX . $key);
        if (!empty($state)) {
            $messages = $this->getState(self::FLASH_KEY_PREFIX . $key);
        }
        $messages[] = $value;

        $this->setState(self::FLASH_KEY_PREFIX . $key, $messages, $defaultValue);
        $counters = $this->getState(self::FLASH_COUNTERS, array());
        if ($value === $defaultValue) {
            unset($counters[$key]);
        } else {
            $counters[$key] = 0;
        }
        $this->setState(self::FLASH_COUNTERS, $counters, array());
    }

}