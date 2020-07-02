<div id="breadcrumbs">

    <?php
    if($this->showHome) {
        echo '<span class="crumb home">' . CHtml::link('Dashboard', Yii::app()->baseUrl) . '</span>';
    }
    ?>

    <?php
    if (!empty($this->start)) {
        foreach($this->start as $key => $val) {
            echo ' &raquo; <span class="crumb">' . CHtml::link($key, $val) . '</span>';
        }
    }
    ?>

    <?php
    if($this->showController) {
        echo '&raquo; <span class="crumb">' . ucfirst($this->controller) . '</span>';
    }
    ?>

    <?php
    if (!empty($this->middle)) {
        foreach($this->start as $key => $val) {
            echo ' &raquo; <span class="crumb">' . CHtml::link($key, $val) . '</span>';
        }
    }
    ?>

    <?php
    if($this->showAction && $this->action != 'index') {
        echo ' &raquo; <span class="crumb">' . ucfirst($this->action) . '</span>';
    }
    ?>

    <?php
    if (!empty($this->end)) {
        foreach($this->start as $key => $val) {
            echo ' &raquo; <span class="crumb">' . CHtml::link($key, $val) . '</span>';
        }
    }
    ?>

</div>