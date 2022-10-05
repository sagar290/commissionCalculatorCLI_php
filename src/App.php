<?php

namespace Sagar290\CommissionCalc;

use Sagar290\CommissionCalc\Service\CommissionCalculator;

class App
{

    public function commission()
    {
        return $this->resolve(CommissionCalculator::class);
    }

    public function resolve(string $class)
    {
        $reflectionClass = new \ReflectionClass($class);

        $constructor = $reflectionClass->getConstructor();

        $interfaces = $reflectionClass->getInterfaceNames();

        if ($interfaces) {
            foreach ($interfaces as $interface) {
                $reflectionClass->implementsInterface($interface);
            }
        }

        if ($constructor == null) {

            if ($reflectionClass->isInterface()) {
                return null;
            }

            return $reflectionClass->newInstance();
        }

        if (($params = $constructor->getParameters()) === []) {
            return $reflectionClass->newInstance();
        }

        $newInstanceParams = [];
        foreach ($params as $param) {

            $newInstanceParams[] = $param->getClass() === null ? $param->getDefaultValue() : $this->resolve(
                $param->getClass()->getName()
            );
        }

        return $reflectionClass->newInstanceArgs(
            $newInstanceParams
        );
    }

}
