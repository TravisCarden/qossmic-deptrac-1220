<?php declare(strict_types=1);

namespace QossmicDeptrac1220\Tests;

use QossmicDeptrac1220\Infrastructure\_InfrastructureClass;

final class Good_DependOnInfrastructure
{
    public function call(): string
    {
        return _InfrastructureClass::class;
    }
}
