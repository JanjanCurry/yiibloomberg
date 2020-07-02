<?php class AutoCompleteController extends Controller {

    public function actions () {
        return array(

            'commodity' => array(
                'class' => 'application.components.AutoComplete',
                'display' => array(array('code'), array('name')),
                'model' => 'DbCommodities',
                'notOptions' => array('code' => (!empty($_REQUEST['restrict'])? explode(',',$_REQUEST['restrict']) : '')),
                'strictOptions' => array('code' => (!empty($_REQUEST['filter'])? explode(',',$_REQUEST['filter']) : '')),
                'likeOptions' => array('code' => (!empty($_REQUEST['filterLike'])? explode(',',$_REQUEST['filterLike']) : '')),
                'search' => array('name', 'code', 'aka', 'figi'),
                'return' => array(
                    'id', 'name', 'code', 'aka',
                ),
            ),

            'currency' => array(
                'class' => 'application.components.AutoComplete',
                'display' => array(array('name')),
                'model' => 'DbCurrencies',
                'notOptions' => array('code' => (!empty($_REQUEST['restrict'])? explode(',',$_REQUEST['restrict']) : '')),
                'strictOptions' => array('code' => (!empty($_REQUEST['filter'])? explode(',',$_REQUEST['filter']) : '')),
                'likeOptions' => array('code' => (!empty($_REQUEST['filterLike'])? explode(',',$_REQUEST['filterLike']) : '')),
                'search' => array('name', 'code', 'aka', 'figi'),
                'return' => array(
                    'id', 'name', 'code', 'aka',
                ),
            ),

            'equity' => array(
                'class' => 'application.components.AutoComplete',
                'display' => array(array('code'), array('name')),
                'model' => 'DbEquities',
                'notOptions' => array('code' => (!empty($_REQUEST['restrict'])? explode(',',$_REQUEST['restrict']) : '')),
                'strictOptions' => array('code' => (!empty($_REQUEST['filter'])? explode(',',$_REQUEST['filter']) : '')),
                'likeOptions' => array('code' => (!empty($_REQUEST['filterLike'])? explode(',',$_REQUEST['filterLike']) : '')),
                'search' => array('name', 'code', 'aka', 'figi'),
                'return' => array(
                    'id', 'name', 'code', 'aka',
                ),
            ),

            'partner' => array(
                'class' => 'application.components.AutoComplete',
                'display' => array(array('ccode3'), array('country')),
                'model' => 'DbPartners',
                'notOptions' => array('ccode3' => (!empty($_REQUEST['restrict'])? explode(',',$_REQUEST['restrict']) : '')),
                'strictOptions' => array('ccode3' => (!empty($_REQUEST['filter'])? explode(',',$_REQUEST['filter']) : '')),
                'search' => array('country', 'ccode3', 'aka'),
                'return' => array(
                    'id', 'country', 'ccode3', 'color', 'searchDef', 'code', 'name',
                ),
            ),

            'reporterMacro' => array(
                'class' => 'application.components.AutoComplete',
                'display' => array(array('ccode3'), array('country')),
                'model' => 'DbReporters',
                'notOptions' => array('ccode3' => (!empty($_REQUEST['restrict'])? explode(',',$_REQUEST['restrict']) : '')),
                'strictOptions' => array('ccode3' => (!empty($_REQUEST['filter'])? explode(',',$_REQUEST['filter']) : '')),
                'likeOptions' => array('type' => ',macro,'),
                'search' => array('country', 'ccode3', 'aka'),
                'order' => 'ccode3 ASC',
                'return' => array(
                    'id', 'country', 'ccode3', 'color', 'searchDef', 'code', 'name',
                ),
            ),

            'reporterTrade' => array(
                'class' => 'application.components.AutoComplete',
                'display' => array(array('ccode3'), array('country')),
                'model' => 'DbReporters',
                'notOptions' => array('ccode3' => (!empty($_REQUEST['restrict'])? explode(',',$_REQUEST['restrict']) : '')),
                'strictOptions' => array('ccode3' => (!empty($_REQUEST['filter'])? explode(',',$_REQUEST['filter']) : '')),
                'likeOptions' => array('type' => ',trade,'),
                'search' => array('country', 'ccode3', 'aka'),
                'order' => 'ccode3 ASC',
                'return' => array(
                    'id', 'country', 'ccode3', 'color', 'searchDef', 'code', 'name',
                ),
            ),

            'search' => array(
                'class' => 'application.components.AutoCompleteSearch',
            ),

            'sector' => array(
                'class' => 'application.components.AutoComplete',
                'display' => array(array('code'), array('name')),
                'model' => 'DbSectors',
                'search' => array('code', 'name'),
                'return' => array(
                    'id', 'code', 'name',
                ),
            ),

            'user' => array(
                'class' => 'application.components.AutoComplete',
                'display' => array('fName', 'sName'),
                'model' => 'DbUser',
                'search' => array('company', 'fName', 'sName', 'email', 'phone'),
                'return' => array(
                    'fName', 'sName', 'company',
                    'email', 'phone',
                    'format:dateUpdated','format:dateCreated',
                ),
            ),

        );
    }

}