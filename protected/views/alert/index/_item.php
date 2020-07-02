<div class="row">
    <div class="col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">
        <div class="alert-item">
            <p><?php echo nl2br($data->message); ?></p>
            <p class="alert-item-footer"><?php echo Yii::app()->format->date($data->created); ?></p>
        </div>
    </div>
</div>