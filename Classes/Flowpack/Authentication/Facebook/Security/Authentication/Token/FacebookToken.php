<?php

namespace Flowpack\Authentication\Facebook\Security\Authentication\Token;

/*
 *
 * This script belongs to the Flow framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * An authentication token used for sso credentials coming from Facebook
 * @author Benoit NORRIN <benoit@norrin.fr>
 */
class FacebookToken extends \TYPO3\Flow\Security\Authentication\Token\AbstractToken {

    /**
     * @Flow\Inject
     * @var \Flowpack\Authentication\Facebook\Service\FacebookService
     */
    protected $facebookService;

    /**
     * Updates the user information from Facebook service
     * @param \TYPO3\Flow\MVC\RequestInterface $request The current request instance
     * @return void
     */
    public function updateCredentials(\TYPO3\Flow\Mvc\ActionRequest $actionRequest) {
        $user_profile = null;
        try {
            $user = $this->facebookService->getFaceBookObject()->getUser();
            if ($user) {
                // Proceed knowing you have a logged in user who's authenticated.
                $user_profile = $this->facebookService->getFaceBookObject()->api('/me');
            }
        } catch (\FacebookApiException $e) {
            //trigger_error($e->getMessage());
        }

        if ($user_profile) {
            $this->credentials = $user_profile;
            $this->setAuthenticationStatus(self::AUTHENTICATION_NEEDED);
        }
    }

    /**
     * Returns a string representation of the token for logging purposes.
     *
     * @return string The username credential
     */
    public function __toString() {
        return 'Username: "' . $this->credentials['email'] . '"';
    }

}

?>
