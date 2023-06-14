# Reproducer for Issue #1220 · qossmic/deptrac

This repository is an attempt to reproduce the issue from https://github.com/qossmic/deptrac/issues/1220.

## Setup

```shell
git clone git@github.com:TravisCarden/qossmic-deptrac-1220.git
cd qossmic-deptrac-1220
composer install
```

## Overview

### Project files

```shell
$ tree --gitignore
.
├── README.md
├── composer.json
├── composer.lock
├── deptrac.yaml
├── src
│   ├── Domain
│   │   ├── Bad_DependOnInfrastructure.php
│   │   ├── Bad_DependOnTest.php
│   │   ├── Bad_DependOnVendor.php
│   │   ├── Good_DependOnDomain.php
│   │   └── _DomainClass.php
│   └── Infrastructure
│       ├── Bad_DependOnTest.php
│       ├── Good_DependOnDomain.php
│       ├── Good_DependOnInfrastructure.php
│       ├── Good_DependOnVendor.php
│       └── _InfrastructureClass.php
└── tests
    ├── Good_DependOnDomain.php
    ├── Good_DependOnInfrastructure.php
    ├── Good_DependOnTest.php
    ├── Good_DependOnVendor.php
    └── _TestClass.php

5 directories, 19 files
```

### Deptrac configuration

[`deptrac.yaml`](deptrac.yaml)

```shell
$ cat deptrac.yaml
deptrac:

  paths:
    - ./src
    - ./tests
    - ./vendor

  layers:
    -
      name: Domain
      collectors:
        - type: directory
          value: '%currentWorkingDirectory%/src/Domain/.*'
    -
      name: Infrastructure
      collectors:
        - type: directory
          value: '%currentWorkingDirectory%/src/Infrastructure/.*'
    -
      name: Tests
      collectors:
        - type: directory
          value: '%currentWorkingDirectory%/tests/.*'
    -
      name: Vendor
      collectors:
        - type: directory
          value: '%currentWorkingDirectory%/vendor/.*'

  ruleset:

    Domain: ~

    Infrastructure:
      - Domain
      - Vendor

    Tests:
      - Domain
      - Infrastructure
      - Vendor

    Vendor: ~
```

## Results

```shell
$ php vendor/qossmic/deptrac/deptrac.php
 2437/2437 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 -------------------------- -----------------------------------------------------------------------------------------------------------
  Reason                     Infrastructure
 -------------------------- -----------------------------------------------------------------------------------------------------------
  DependsOnDisallowedLayer   QossmicDeptrac1220\Infrastructure\Bad_DependOnTest must not depend on QossmicDeptrac1220\Tests\_TestClass
                             You are depending on token that is a part of a layer that you are not allowed to depend on. (Tests)
                             /Users/traviscarden/Projects/other/qossmic-deptrac-1220/src/Infrastructure/Bad_DependOnTest.php:12
 -------------------------- -----------------------------------------------------------------------------------------------------------

 -------------------------- --------------------------------------------------------------------------------------------------------------------------------
  Reason                     Domain
 -------------------------- --------------------------------------------------------------------------------------------------------------------------------
  DependsOnDisallowedLayer   QossmicDeptrac1220\Domain\Bad_DependOnVendor must not depend on PHP_CodeSniffer\Runner
                             You are depending on token that is a part of a layer that you are not allowed to depend on. (Vendor)
                             /Users/traviscarden/Projects/other/qossmic-deptrac-1220/src/Domain/Bad_DependOnVendor.php:11
  DependsOnDisallowedLayer   QossmicDeptrac1220\Domain\Bad_DependOnInfrastructure must not depend on QossmicDeptrac1220\Infrastructure\_InfrastructureClass
                             You are depending on token that is a part of a layer that you are not allowed to depend on. (Infrastructure)
                             /Users/traviscarden/Projects/other/qossmic-deptrac-1220/src/Domain/Bad_DependOnInfrastructure.php:11
  DependsOnDisallowedLayer   QossmicDeptrac1220\Domain\Bad_DependOnTest must not depend on QossmicDeptrac1220\Tests\_TestClass
                             You are depending on token that is a part of a layer that you are not allowed to depend on. (Tests)
                             /Users/traviscarden/Projects/other/qossmic-deptrac-1220/src/Domain/Bad_DependOnTest.php:11
 -------------------------- --------------------------------------------------------------------------------------------------------------------------------

 ------------------------ --------------------------------------------------------------------------------------------------------------------------------
  Reason                   Tests
 ------------------------ --------------------------------------------------------------------------------------------------------------------------------
  DependsOnInternalToken   QossmicDeptrac1220\Tests\Good_DependOnInfrastructure must not depend on QossmicDeptrac1220\Infrastructure\_InfrastructureClass
                           You are depending on a token that is internal to the layer and you are not part of that layer. (Infrastructure)
                           /Users/traviscarden/Projects/other/qossmic-deptrac-1220/tests/Good_DependOnInfrastructure.php:11
 ------------------------ --------------------------------------------------------------------------------------------------------------------------------


 -------------------- ------
  Report
 -------------------- ------
  Violations           5
  Skipped violations   0
  Uncovered            2601
  Allowed              4
  Warnings             0
  Errors               0
 -------------------- ------
```

## Debug

```shell
$ ./vendor/bin/deptrac debug:layer
 ------------------------------------------------------
  Domain
 ------------------------------------------------------
  QossmicDeptrac1220\Domain\Bad_DependOnInfrastructure
  QossmicDeptrac1220\Domain\Bad_DependOnTest
  QossmicDeptrac1220\Domain\Bad_DependOnVendor
  QossmicDeptrac1220\Domain\Good_DependOnDomain
  QossmicDeptrac1220\Domain\_DomainClass
 ------------------------------------------------------

 ---------------------------------------------------------------
  Infrastructure
 ---------------------------------------------------------------
  QossmicDeptrac1220\Infrastructure\Bad_DependOnTest
  QossmicDeptrac1220\Infrastructure\Good_DependOnDomain
  QossmicDeptrac1220\Infrastructure\Good_DependOnInfrastructure
  QossmicDeptrac1220\Infrastructure\Good_DependOnVendor
  QossmicDeptrac1220\Infrastructure\_InfrastructureClass
 ---------------------------------------------------------------

 ------------------------------------------------------
  Tests
 ------------------------------------------------------
  QossmicDeptrac1220\Tests\Good_DependOnDomain
  QossmicDeptrac1220\Tests\Good_DependOnInfrastructure
  QossmicDeptrac1220\Tests\Good_DependOnTest
  QossmicDeptrac1220\Tests\Good_DependOnVendor
  QossmicDeptrac1220\Tests\_TestClass
 ------------------------------------------------------

 ----------------------------------------------------------------------------------------------
  Vendor
 ----------------------------------------------------------------------------------------------
  ComposerAutoloaderInitef51b699a850cf2c8a5714e8cb48e8f9
  Composer\Autoload\ClassLoader
  Composer\Autoload\ComposerStaticInitef51b699a850cf2c8a5714e8cb48e8f9
  Composer\InstalledVersions
  DeepCopy\DeepCopy
  DeepCopy\Exception\CloneException
  DeepCopy\Exception\PropertyException
  ...
```
