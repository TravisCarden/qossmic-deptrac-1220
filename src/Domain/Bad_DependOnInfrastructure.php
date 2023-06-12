<?php declare(strict_types=1);

namespace QossmicDeptrac1220\Domain;

use QossmicDeptrac1220\Infrastructure\_InfrastructureClass;

final class Bad_DependOnInfrastructure
{
    public function call(): string
    {
        return _InfrastructureClass::class;
    }
}
