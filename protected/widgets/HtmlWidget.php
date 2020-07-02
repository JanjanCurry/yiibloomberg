<?php
class HtmlWidget extends CWidget {
	public $view;
	public $vars;

	public function init() {
		if ($this->getViewFile($this->view)) {
			$this->render($this->view, $this->vars);
		}
	}
}