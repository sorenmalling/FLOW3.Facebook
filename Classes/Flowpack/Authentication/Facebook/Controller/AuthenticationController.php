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
		* @var \TYPO3\Flow\Log\SystemLoggerInterface
		* @Flow\Inject
		*/
		protected $systemLogger;

	/**
	 * Authenticate the request
	 *
	 * @return void
	 */
	public function authenticateAction() {
		$facebookObject = $this->facebookService->getFaceBookObject();
		if ($facebookObject->getUser() > 0) {
			$this->authenticationManager->authenticate();
			if ($this->authenticationManager->isAuthenticated() === TRUE) {
				$this->onAuthenticationSuccess($this->request);
			}
		} else {
			$redirectUriParameters = $this->settings['application']['redirect_uri'];
			$loginUrl = $facebookObject->getLoginUrl(
				array(
					'redirect_uri' => $this->uriBuilder->setCreateAbsoluteUri(TRUE)->uriFor($redirectUriParameters['@action'], $redirectUriParameters, $redirectUriParameters['@controller'], $redirectUriParameters['@package'], $redirectUriParameters['@subpackage']),
					'scope' => $this->settings['application']['scope']
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
		$redirectUriParameters = $this->settings['application']['success_redirect_uri'];
		$uri = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->uriFor($redirectUriParameters['@action'], array(), $redirectUriParameters['@controller'], $redirectUriParameters['@package'], $redirectUriParameters['@subpackage']);
		$this->redirectToUri($uri);
	}

}

?>
