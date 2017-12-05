<?php

namespace App;
/**
 * Description of CustomApp
 *
 * @author Etudiant
 */
class CustomApp extends \Silex\Application
{
    use \Silex\Application\SecurityTrait;
    use \Silex\Application\MonologTrait;
    use \Silex\Application\FormTrait;
    use \Silex\Application\TranslationTrait;
    use \Silex\Application\TwigTrait;
    use \Silex\Application\UrlGeneratorTrait;
}
