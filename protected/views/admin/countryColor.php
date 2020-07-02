<?php $form=$this->beginWidget('CActiveForm', array(
    'enableAjaxValidation'=> true,
    'enableClientValidation' => false,
    'htmlOptions' => array(
        'class'=>'form-horizontal',
        'autocomplete' => 'off',
    ),
)); ?>

<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Country Color Picker</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">

    <div class="row">
        <div class="col-sm-4">
    <?php
    if(!empty($countries)){
        $i = 0;
        $total = round(count($countries) / 3);
        $letter = '';
        foreach ($countries as $country){
            if($i == $total || $i == ($total*2)){
                echo '</div><div class="col-sm-4">';
            }
            if($letter != $country->country[0]){
                $letter = $country->country[0];
                echo '<h4>'.strtoupper($letter).'</h4>';
            }
            ?>

                <div class="form-group">
                    <label class="col-xs-8 control-label" for="DbPartners_<?php echo $country->ccode3; ?>_color"><?php echo $country->country; ?></label>
                    <div class="col-xs-4">
                        <span class="input-group">
                            <span class="input-group-addon"><span class="fa fa-circle" style="color:<?php echo (!empty($country->color) ? '#'.$country->color : 'transparent'); ?>"></span></span>
                            <?php echo $form->textField($country, '['.$country->ccode3.']color', array('class'=>'form-control colorPicker')); ?>
                        </span>
                        <?php echo $form->error($country, '['.$country->ccode3.']color'); ?>
                    </div>
                </div>
            <?php
            $i++;
        }
    }
    ?>
        </div>
    </div>

</div>
<?php $this->endWidget(); ?>
