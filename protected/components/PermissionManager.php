<?php
//************************************************************
// Permissions Manager component to control permissions
// @param string $id - permission name
// @return Permissions the static component class
//************************************************************
class PermissionManager extends CDbAuthManager {
		
	public $model;
	public $id;
	public $itemTable = 'db_auth_item';
	public $itemChildTable = 'db_auth_item_child';
	public $assignmentTable = 'db_auth_assignment';
	private $checked = [];

	public function checkAccess ($itemName, $userId, $params = array()) {
	    if (empty($params)) {
            if (empty($this->checked[$userId])) {
                $this->checked[$userId] = [];
            }
            if (isset($this->checked[$userId][$itemName])) {
                return $this->checked[$userId][$itemName];
            }
        }

        $valid = parent::checkAccess($itemName, $userId, $params);

        if (empty($params)) {
            $this->checked[$userId][$itemName] = $valid;
        }

        return $valid;
    }

    public function checkToolAccess($tool, $itemName, $userId){
	    if(empty($this->checked[$userId])) {
            $this->checked[$userId] = [];
        }
        if(!empty($this->checked[$userId][$tool]) && isset($this->checked[$userId][$tool][$itemName])){
            return $this->checked[$userId][$tool][$itemName];
        }

        if($this->checkAccess($itemName, $userId)){
            $roles = $this->getRoles($userId);
            if(!empty($roles)){
                foreach($roles as $role){
                    if(strpos($role->name, 'sub-'.$tool) !== false){
                        if($this->checkToolAccessRecursive($itemName, $role)){
                            if(empty($this->checked[$userId][$tool])){
                                $this->checked[$userId][$tool] = [];
                            }
                            $this->checked[$userId][$tool][$itemName] = true;
                            return true;
                        }
                    }
                }
            }
        }
        $this->checked[$userId][$tool][$itemName] = false;
        return false;
    }

    private function checkToolAccessRecursive($match, $item){
        $valid = false;
        if(is_object($item)){
            $item = $item->name;
        }

        if($item == $match){
            $valid = true;
        }else {
            $children = $this->getItemChildren($item);
            if (!empty($children)) {
                foreach ($children as $child) {
                    $valid = $this->checkToolAccessRecursive($match, $child->name);
                    if ($valid) {
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    public function clearCache($userId = null){
	    var_dump($this->checked, $userId);
	    if(empty($userId)){
	        $this->checked = [];
        }else if (!empty($this->checked[$userId])) {
            $this->checked[$userId] = [];
        }
        var_dump($this->checked);
    }

	/*
	 * Checks and returns pages which are visible to a specific user
	 */
	public function visibleMenuItems($menuItems){
		$visibleItems = array();
		foreach($menuItems as $item){
			if(!isset($item['items'])){
				if(Yii::app()->user->checkAccess($item['url'][0])){
					$visibleItems[] = $item;
				}
			}elseif(is_array($item)){
				$item['items'] = $this->visibleMenuItems($item['items']);
				if(!empty($item['items'])){
					$visibleItems[] = $item;
				}
			}
		}
		return $visibleItems;
	}

	/**
	 * @param $item CAuthItem
	 * @return Array
	 */
	public function getPossibleChildren($item) {
		$allItems = Yii::app()->authManager->getAuthItems();

		$returnItems = array(
			"Roles"=>array(),
			"Tasks"=>array(),
			"Operations"=>array(),
		);

		foreach ($allItems as $singleItem) {
			if ($singleItem->type <= $item->type && $singleItem->name != $item->name) {
				$returnItems[ucfirst(self::getTypeLabel($singleItem->type)).'s'][$singleItem->name] = $singleItem->name;
			}
		};

		foreach($returnItems as $key => $val){
            asort($returnItems[$key]);
        }

		return $returnItems;
	}


	/**
	 * @param $type integer CAuthItem item
	 * @return string
	 */
	public static function getTypeLabel($type) {
		switch($type) {
			case 0:
				return "operation";
			case 1:
				return "task";
			case 2:
				return "role";
		}
	}

    public function isPromo($tool, $userId){
        $ref = 'promo-'.$tool;
        if(empty($this->checked[$userId])) {
            $this->checked[$userId] = [];
        }
        if(isset($this->checked[$userId][$ref])){
            return $this->checked[$userId][$ref];
        }

        $valid = DbUserService::model()->isPromo($tool, $userId);
        $this->checked[$userId][$ref] = $valid;

        return $valid;
    }

}

?>