<div class="alert alert-info text-center">
    <h4 class="h3">Need Some Help?</h4>
    <p>Check out our helpful How-To Videos or Interactive Tutorial for help and tips on using our features.</p>
    <div class="padding-top-20">
        <?php
        if(!empty($cat)){
            echo CHtml::link('How To Videos', ['site/howTo', '#' => 'videos-'.$cat], ['class' => 'btn btn-accent']);
        }else {
            echo CHtml::link('How To Videos', ['site/howTo'], ['class' => 'btn btn-accent']);
        }
        ?>
        <?php echo CHtml::link('Interactive Tutorial', ['user/tourToggle', 'returnUrl' => base64_encode(Yii::app()->request->requestUri)], ['class' => 'btn btn-accent']); ?>
    </div>
</div>