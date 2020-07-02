<?php
/* @var DbPartners[] $partners */
/* @var array $list */
?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'htmlOptions' => array(
        'class' => 'form-horizontal',
        'autocomplete' => 'off',
    ),
)); ?>

<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Partner Groups</h3>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" aria-label="Save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <input type="hidden" name="processing-trigger" value="1"/>

    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php
            $alphas = range('a', 'z');
            foreach ($alphas as $alpha) {
                echo '<li>' . CHtml::link($alpha, ['admin/partnerGroups', 'page' => $alpha]) . '</li>';
            }
            ?>
        </ul>
    </nav>

    <table class="table table-striped">
        <tr>
            <th>Code</th>
            <th>Partner</th>
            <th class="text-center">Partner Children</th>
        </tr>
        <?php
        if (!empty($partners)) {
            foreach ($partners as $partner) {
                $children = [];
                if (!empty($partner->children)) {
                    foreach ($partner->children as $child) {
                        $children[] = $child->id;
                    }
                }
                echo '<tr>';
                echo '<th>' . $partner->code . '</th>';
                echo '<th>' . $partner->name . '</th>';
                echo '<td>' . CHtml::dropDownList('groups[' . $partner->id . ']', $children, $list, array(
                        'class' => 'form-control selectpicker-count',
                        'empty' => 'Nothing Selected',
                        'multiple' => 'multiple',
                    )) . '</td>';
                echo '</tr>';
            }
        }
        ?>

        <?php
        if (!empty($macros)) {
            $i = 0;
            $total = round(count($macros) / 3);
            $letter = '';
            foreach ($macros as $macro) {
                if ($i == $total || $i == ($total * 2)) {
                    //echo '</div>';
                }
                if ($letter != $macro->category) {
                    $letter = $macro->category;
                    echo '<div class="clearfix"></div>';
                    echo '<h4>' . strtoupper($letter) . '</h4>';
                }
                if ($i == $total || $i == ($total * 2)) {
                    //echo '<div class="col-sm-4">';
                }
                ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="col-xs-8 control-label" for="DbMacroList_<?php echo $macro->assetId; ?>_color"><?php echo $macro->assetName; ?></label>
                        <div class="col-xs-4">
                            <span class="input-group">
                                <span class="input-group-addon"><span class="fa fa-circle" style="color:<?php echo(!empty($macro->color) ? '#' . $macro->color : 'transparent'); ?>"></span></span>
                                <?php echo $form->textField($macro, '[' . $macro->assetId . ']color', array('class' => 'form-control colorPicker')); ?>
                            </span>
                            <?php echo $form->error($macro, '[' . $macro->assetId . ']color'); ?>
                        </div>
                    </div>
                </div>
                <?php
                $i++;
            }
        }
        ?>

    </table>

</div>
<?php $this->endWidget(); ?>
