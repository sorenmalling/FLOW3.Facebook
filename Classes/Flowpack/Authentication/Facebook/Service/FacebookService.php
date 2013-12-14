<?php

namespace Flowpack\Authentication\Facebook\Service;

use TYPO3\Flow\Annotations as Flow;

require_once FLOW_PATH_PACKAGES . 'Application/Flowpack.Authentication.Facebook/Resources/Private/PHP/facebook-sdk/facebook.php';

/**
 * Service Facebook
 *
 * @author Benoit NORRIN <benoit@norrin.fr>
 * @Flow\Scope("prototype")
 */
class FacebookService {

    /**
     * @var array
     */
    protected $options;

    /**
     *
     * @var \Facebook 
     */
    protected $object;

    /**
     * Settings
     * @param array $settings 
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * Return a Facebook object
     * @return \Facebook 
     */
    public function getFaceBookObject() {
        if (is_object($this->object) == false) {
            $this->object = new \Facebook(array(
                        'appId' => $this->settings['application']['id'],
                        'secret' => $this->settings['application']['secret']
                    ));
        }
        return $this->object;
    }

}

?>
