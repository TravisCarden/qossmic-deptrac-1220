<?php declare(strict_types=1);

namespace QossmicDeptrac1220\Infrastructure;

use QossmicDeptrac1220\Domain\_DomainClass;

final class Good_DependOnDomain
{
    public function call(): string
    {
        return _DomainClass::class;
    }
}
