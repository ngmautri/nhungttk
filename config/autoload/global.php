<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

// SQL: 2nyAf8CT3hujzGLM
return array(
    
    'locale' => array(
        'default' => 'en_US',
        'available' => array(
            'en_US' => 'English',
            'vi_VN' => 'Tieng Viet',
            'lo_LA' => 'Lao',
            'de_DE' => 'Deutsch'
        
        )
    ),
    
    'service_manager' => array(
        
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory'
        
        )
    
    )

);
