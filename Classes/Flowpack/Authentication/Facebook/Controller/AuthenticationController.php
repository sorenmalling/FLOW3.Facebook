<?php

namespace Flowpack\Authentication\Facebook\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Authentication\Controller\AbstractAuthenticationController;

class AuthenticationController extends AbstractAuthenticationController {

	/**
	 * Inject Facebook service
	 *
	 * @var \Flowpack\Authentication\Facebook\Service\FacebookService
	 *
	 * @Flow\Inject
	 */
	protected $facebookService;

	/**
	 * Authenticate the request
	 *
	 * @return void
	 */
	public function authenticateAction() {
		$facebookObject = $this->facebookService->getFaceBookObject();
		if ($facebookObject->getUser() > 0) {
			$user = $facebookObject->getUser();
			\TYPO3\Flow\var_dump($user);
			$user_profile = $facebookObject->api('/me');
			\TYPO3\Flow\var_dump($user_profile);
		} else {
			$loginUrl = $facebookObject->getLoginUrl(
				array(
					'client_id' => $this->settings['API']['appId'],
					'redirect_uri' => 'http://scoutrace.sma/flowpack.authentication.facebook/authentication/authenticate',
					'scope' => 'email'
				)
			);
			$this->redirectToUri($loginUrl);
		}

	}


	/**
	 * Is called if authentication was successful. If there has been an
	 * intercepted request due to security restrictions, you might want to use
	 * something like the following code to restart the originally intercepted
	 * request:
	 *
	 * if ($originalRequest !== NULL) {
	 *     $this->redirectToRequest($originalRequest);
	 * }
	 * $this->redirect('someDefaultActionAfterLogin');
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
	 * @return string
	 */
	protected function onAuthenticationSuccess(\TYPO3\Flow\Mvc\ActionRequest $originalRequest = NULL) {
		// TODO: Implement onAuthenticationSuccess() method.
	}

}

?>