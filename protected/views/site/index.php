<div class="container">
    <h3 class="text-center wow zoomIn">Hello <?php echo $this->user->fName; ?>

        <?php
        $iHateThis = DbCheesyCompliment::model()->find(array('order' => 'RAND()'));
        if (!empty($iHateThis)) {
            echo '<br /><small>' . $iHateThis->cheese . '</small>';
        }
        ?>
    </h3>

    <div class="sep sep-xs sep-blue wow pulse"></div>

    <h3 class="text-center wow zoomIn">
        <small>What would you like to explore?</small>
    </h3>
    <div class="row">

        <div class="col-sm-6 col-sm-offset-3 wow zoomIn">
            <h4 class="text-center">Markets</h4>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6 col-xs-12 margin-bottom-10">
                            <?php echo CHtml::link('<span class="fa fa-tint"></span> Commodities', array('commodity/index'), array('class' => 'btn btn-accent btn-block',)); ?>
                        </div>
                        <div class="col-md-6 col-xs-12 margin-bottom-10">
                            <?php echo CHtml::link('<span class="fa fa-usd"></span> Currencies', array('currency/index'), array('class' => 'btn btn-accent btn-block',)); ?>
                        </div>
                        <div class="col-md-6 col-xs-12 margin-bottom-10">
                            <?php echo CHtml::link('<span class="fa fa-balance-scale"></span> Equities', array('equity/index'), array('class' => 'btn btn-accent btn-block',)); ?>
                        </div>
                        <div class="col-md-6 col-xs-12 margin-bottom-10">
                            <?php echo CHtml::link('<span class="fa fa-cubes"></span> Market Reports <span class="badge badge-primary">BETA</span>', array('report/markets'), array('class' => 'btn btn-accent btn-block',)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="sep sep-md sep-blue wow pulse"></div>

</div>

<div class="container">
    <div class="text-center">
        <h4>Dashboard Modules <span class="badge badge-primary">BETA</span></h4>
    </div>
    <?php $this->renderPartial('dashboard', ['withNav' => true]); ?>
    <div class="text-center">
        <?php echo CHtml::link('View Full Dashboard <span class="badge badge-primary">BETA</span>', ['site/dashboard'], ['class' => 'btn btn-accent btn-sm']); ?>
    </div>
</div>

<div class="container">
    <?php
    $types = [];
    if (Yii::app()->user->checkAccess('tool-trade')) {
        $types[] = 'trade';
        $types[] = 'macro';
    }
    if (Yii::app()->user->checkAccess('tool-com')) {
        $types[] = 'commodity';
    }
    if (Yii::app()->user->checkAccess('tool-cur')) {
        $types[] = 'currency';
    }
    if (Yii::app()->user->checkAccess('tool-equ')) {
        $types[] = 'equity';
    }
    if (!empty($types)) {

        $slideCount = 0;
        $criteria = new CDbCriteria();
        $criteria->order = 'RAND()';
        $criteria->limit = 20;
        $criteria->compare('userId', $this->user->id);
        $criteria->compare('type', $types);
        $favorites = DbUserFavorites::model()->findAll($criteria);
        if (!empty($favorites)) {
            ?>
            <div class="sep sep-md sep-blue wow pulse"></div>

            <div class="wow slideInUp">
                <h4 class="text-center"><span class="fa fa-star"></span> your favorite reports <span class="fa fa-star"></span></h4>
                <p class="help-block text-center">Manage your favorites using the <span class="fa fa-bars"></span> button.</p>
                <div id="home-fav" class="carousel slide home-fav margin-top-20" data-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        foreach ($favorites as $i => $favorite) {
                            $data = $favorite->data;
                            $data['view'] = 'group';
                            $data['init'] = true;
                            $data['showTable'] = false;
                            $data['showModal'] = false;
                            $data['showLegend'] = false;
                            $data['showEmpty'] = true;
                            $data['editable'] = false;
                            $data['requireEditing'] = false;

                            switch ($favorite->type) {
                                case 'commodity':
                                case 'currency':
                                case 'equity':
                                    $chart = $this->widget('application.widgets.MarketWidget', $data, true);
                                    break;

                                case 'macro':
                                    $chart = $this->widget('application.widgets.MacroWidget', $data, true);
                                    break;

                                case 'trade':
                                    $chart = $this->widget('application.widgets.TradeWidget', $data, true);
                                    break;
                            }

                            if (!empty($chart)) {
                                if ($i == 0) {
                                    echo '<div class="item ' . ($i == 0 ? 'active' : '') . '">';
                                } else if ($i % 3 == 0) {
                                    echo '</div><div class="item">';
                                    $slideCount++;
                                }
                                echo $chart;
                            }
                        }
                        echo '</div>';

                        $this->widget('application.widgets.HtmlWidget', array(
                            'view' => 'chart/share',
                        ));
                        ?>
                    </div>

                    <ol class="carousel-indicators">
                        <li data-target="#home-fav" data-slide-to="0" class="active"></li>
                        <?php for ($i = 1; $i <= $slideCount; $i++) {
                            echo '<li data-target="#home-fav" data-slide-to="' . $i . '"></li>';
                        } ?>
                    </ol>
                </div>
            </div>

        <?php }
    } ?>




    <?php /*
    <div class="sep sep-md sep-blue"></div>

    <div class="panel panel-default">
        <?php echo CHtml::link('My Macroeconomic favorites <span class="fa fa-caret-'.($this->user->preferences['home-macro-fav-tab'] == 'closed' ? 'right' : 'down').'"></span>', '#', array('class' => 'h4 toggleBtn btn btn-white btn-block margin-0 favorites-list-toggle', 'data-toggle' => 'macro-favorites')); ?>
        <div class="panel-body macro-favorites <?php echo ($this->user->preferences['home-macro-fav-tab'] == 'closed' ? 'hidden" data-state="closed"' : '" data-state="open"'); ?>">
            <?php $chart = $this->widget('application.widgets.MacroWidget', array(
                'view' => 'favorites',
                'col' => 'col-sm-6',
                'user' => $this->user,
                'init' => true,
                'showTable' => false,
                'editable' => true,
            )); ?>
        </div>
    </div>

    <div class="sep sep-md sep-blue"></div>

    <div class="panel panel-default">
        <?php echo CHtml::link('My trade favorites <span class="fa fa-caret-'.($this->user->preferences['home-trade-fav-tab'] == 'closed' ? 'right' : 'down').'"></span>', '#', array('class' => 'h4 toggleBtn btn btn-white btn-block margin-0 favorites-list-toggle', 'data-toggle' => 'trade-favorites')); ?>
        <div class="panel-body trade-favorites <?php echo ($this->user->preferences['home-trade-fav-tab'] == 'closed' ? 'hidden" data-state="closed"' : '" data-state="open"'); ?>">
            <?php $chart = $this->widget('application.widgets.TradeWidget', array(
                'view' => 'favorites',
                'user' => $this->user,
                'init' => true,
                'showTable' => false,
                'editable' => true,
            )); ?>
        </div>
    </div>

    <div class="sep sep-md sep-blue"></div>

    <div class="panel panel-default">
        <?php echo CHtml::link('My Commodity favorites <span class="fa fa-caret-'.($this->user->preferences['home-commodity-fav-tab'] == 'closed' ? 'right' : 'down').'"></span>', '#', array('class' => 'h4 toggleBtn btn btn-white btn-block margin-0 favorites-list-toggle', 'data-toggle' => 'commodity-favorites')); ?>
        <div class="panel-body commodity-favorites <?php echo ($this->user->preferences['home-commodity-fav-tab'] == 'closed' ? 'hidden" data-state="closed"' : '" data-state="open"'); ?>">
            <?php $chart = $this->widget('application.widgets.CommodityWidget', array(
                'view' => 'favorites',
                'user' => $this->user,
                'init' => true,
                'showTable' => false,
                'editable' => true,
            )); ?>
        </div>
    </div>
*/ ?>
</div>