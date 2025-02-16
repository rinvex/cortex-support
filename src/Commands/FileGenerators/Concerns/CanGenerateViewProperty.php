<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\FileGenerators\Concerns;

use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\ClassType;

trait CanGenerateViewProperty
{
    protected function addViewPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('view', $this->getView())
            ->setProtected()
            ->setType('string');
        $this->configureViewProperty($property);
    }

    protected function configureViewProperty(Property $property): void {}
}
