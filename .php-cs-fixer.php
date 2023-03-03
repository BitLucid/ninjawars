<?php

$finder = PhpCsFixer\Finder::create()
    // ->exclude('somedir')
    // ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true, // This contains a ton of rules here: https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16|fixer:psr12
    // 'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
    'braces' => [
        'allow_single_line_closure' => true,
        'position_after_functions_and_oop_constructs' => 'same'
    ],
])
    ->setFinder($finder);
