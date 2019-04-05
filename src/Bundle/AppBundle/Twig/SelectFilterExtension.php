<?php

namespace AppBundle\Twig;

class SelectFilterExtension extends \Twig_Extension {
    const SELECTOR_TEMPLATE = '@App/template/partials/darkblue/filter-selector.html.twig';

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'selector', [$this, 'getSelector'], [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'selectorBoolean', [$this, 'getBooleanSelector'], [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function getSelector(\Twig_Environment $environment, $entityClass, $field, $method)
    {
        $emptyData = [null => ''];
        $data = call_user_func($entityClass . '::' . $method);

        return $environment->render(
            self::SELECTOR_TEMPLATE,
            [
                'options' => $emptyData + $data,
                'field' => $field,
            ]
        );
    }

    public function getBooleanSelector(\Twig_Environment $environment, $field)
    {
        $data = [
            null => '',
            true => 'Tak',
            false => 'Nie',
        ];

        return $environment->render(
            self::SELECTOR_TEMPLATE,
            [
                'options' => $data,
                'field' => $field,
            ]
        );
    }
}
