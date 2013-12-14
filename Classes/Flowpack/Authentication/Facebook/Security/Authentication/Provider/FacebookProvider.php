<?php

namespace Flowpack\Authentication\Facebook\Security\Authentication\Provider;

/* *
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
 * An authentication provider that authenticates throw Facebook 
 */
class FacebookProvider extends \TYPO3\Flow\Security\Authentication\Provider\AbstractProvider {

    /**
     * @var \TYPO3\Flow\Security\AccountRepository
     * @Flow\Inject
     */
    protected $accountRepository;

    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Security\AccountFactory
     */
    protected $accountFactory;

    /**
     * @Flow\Inject
     * @var \Flowpack\Authentication\Facebook\Service\FacebookService
     */
    protected $facebookService;

    /**
     * @Flow\Inject
     * @var \TYPO3\Party\Domain\Repository\PartyRepository
     */
    protected $partyRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
     * Returns the classnames of the tokens this provider is responsible for.
     *
     * @return string The classname of the token this provider is responsible for
     */
    public function getTokenClassNames() {
        return array('\Flowpack\Authentication\Facebook\Security\Authentication\Token\FacebookToken');
    }

    /**
     * Sets isAuthenticated to TRUE for all tokens.
     *
     * @param \TYPO3\Flow\Security\Authentication\TokenInterface $authenticationToken The token to be authenticated
	 *
     * @return void
     */
    public function authenticate(\TYPO3\Flow\Security\Authentication\TokenInterface $authenticationToken) {
        if (!($authenticationToken instanceof \Flowpack\Authentication\Facebook\Security\Authentication\Token\FacebookToken)) {
            throw new \TYPO3\Flow\Security\Exception\UnsupportedAuthenticationTokenException('This provider cannot authenticate the given token.', 1217339840);
        }

        // FacebookToken
        $credentials = $authenticationToken->getCredentials();
        
        if (is_array($credentials) && isset($credentials['email'])) {
            $account = $this->accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName($credentials['email'], $this->name);
            
            // Account does not exist
            if (is_object($account) == false) {
                $account = $this->accountFactory->createAccountWithPassword($credentials['email'], md5(time()), array('Flowpack.Authentication.Facebook:UserLambda'), $this->name);
                $this->accountRepository->add($account);

                if ($credentials['last_name'] && $credentials['first_name']) {
                    $personEmail = new \TYPO3\Party\Domain\Model\ElectronicAddress();
                    $personEmail->setIdentifier($credentials['email']);
                    $personEmail->setType(\TYPO3\Party\Domain\Model\ElectronicAddress::TYPE_EMAIL);
                    $personEmail->setUsage(\TYPO3\Party\Domain\Model\ElectronicAddress::USAGE_HOME);

                    $person = new \TYPO3\Party\Domain\Model\Person();
                    $person->addElectronicAddress($personEmail);
                    $person->addAccount($account);
                    $person->setName(new \TYPO3\Party\Domain\Model\PersonName('', $credentials['first_name'], '', $credentials['last_name']));
                    $this->partyRepository->add($person);
                }
            }
            if (is_object($account)) {
                $authenticationToken->setAuthenticationStatus(\TYPO3\Flow\Security\Authentication\TokenInterface::AUTHENTICATION_SUCCESSFUL);
                $authenticationToken->setAccount($account);
            } elseif ($authenticationToken->getAuthenticationStatus() !== \TYPO3\Flow\Security\Authentication\TokenInterface::AUTHENTICATION_SUCCESSFUL) {
                $authenticationToken->setAuthenticationStatus(\TYPO3\Flow\Security\Authentication\TokenInterface::NO_CREDENTIALS_GIVEN);
            }
			$this->persistenceManager->persistAll();
        } else {
            $authenticationToken->setAuthenticationStatus(\TYPO3\Flow\Security\Authentication\TokenInterface::WRONG_CREDENTIALS);
        }
    }
}

?>