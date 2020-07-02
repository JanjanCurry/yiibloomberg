<div class="pageFooter">
        <div class="col-sm-6 pull-left">
            <?php $this->widget('application.widgets.BreadCrumb', array(
                'controller' => $this->getId(),
                'action' => Yii::app()->controller->action->id,
                'start' => $this->breadcrumbs['start'],
                'middle' => $this->breadcrumbs['middle'],
                'end' => $this->breadcrumbs['end'],
                'showHome' => $this->breadcrumbs['home'],
                'showController' => $this->breadcrumbs['controller'],
                'showAction' => $this->breadcrumbs['action'],
            )); ?>
        </div>
    <div class="copyright col-sm-6 pull-right">
		<p>Copyright &copy; <?php echo Yii::app()->name. ' ' . date('Y'); ?></p>
	</div>
    <div class="clearfix"></div>
</div>