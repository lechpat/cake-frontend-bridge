<?php
namespace FrontendBridge\Lib;

trait FrontendBridgeTrait {

/**
 * jsonActionResponse
 *
 * @param \Cake\Network\Response $response the response
 * @return \FrontendBridge\Lib\ServiceResponse
 */
	protected function jsonActionResponse(\Cake\Network\Response $response) {
		// get the frontendData set by the Frontend plugin and remove unnecessary data
		$frontendData = $this->viewVars['frontendData'];
		unset($frontendData['Types']);
		$response = array(
			'code' => 'success',
			'data' => array(
				'frontendData' => $frontendData,
				'html' => $response->body()
			)
		);
		return new \FrontendBridge\Lib\ServiceResponse($response);
	}

/**
 * renderJsonAction
 *
 * @param string $view   the view to render
 * @param string $layout the layout to render
 * @return \FrontendBridge\Lib\ServiceResponse
 */
	public function renderJsonAction($view, $layout) {
		if ($layout === null) {
			$layout = 'FrontendBridge.json_action';
		}
		$this->getView()->subDir = null;
		$response = parent::render($view, $layout);
		return $this->jsonActionResponse($response);
	}
}