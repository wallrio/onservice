# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [1.0.3] - 2018-11-16
### Added
- implementation in the HTTP service: approach for organizing multiple routes.

### Changed

### Removed

## [1.0.4] - 2018-11-19
### Changed
- Fixed problem with route

## [1.0.6] - 2018-12-31
### Changed

- Implementation of authentication
- multiple routes in annotations
- Fixed problem json driver

## [1.0.8] - 2019-1-24

### Changed
- Fixed problem json driver

### Added
- Implementation of Mongo Driver to database
- Implementation of Settings Service


## [1.2.0] - 2019-1-31

### Added
- Implementation of Console Service
- Implementation of Stream TCP
- Implementation of Stream Http Server
- Implementation of Stream Http Client


## [1.4.0] - 2019-2-20

### Changed
- Modified HTTP Service for Router
- Separate HTTP Container for RouterClass
- Created PushRoute

## [1.5.0] - 2019-2-22

### Changed
- changed methods on CreateServer
- changed service Mysql

### Added
- created ORM mapper
- created PIPE service

## [1.5.1] - 2019-2-23

### Changed
- changed all references from namespaces to tyne.

### Added
- created delete method to ORM mapper

## [1.6.0] - 2019-2-23

### Changed
- various modifications in Mongo Driver
- various modifications in JSON Driver
- various modifications in Mysql Driver

### Added
- created ODM mapper
- created OJM mapper

## [1.6.1] - 2019-2-24

### Changed
- fixed database in update register on json driver

## [1.6.2] - 2019-3-10

### Changed
- change attachService on CreateServer
- implemented sub-nivel on Document Json Database

## [1.6.3] - 2019-3-10

### Changed
- fixed essentials/Http
- implemented GitLab support in Git Service

## [1.6.4] - 2019-3-10

### Changed
- fixed Git Service

## [1.6.5] - 2019-3-11

### Changed
- fixed Document Json Driver Database

## [1.6.6] - 2019-3-11

### Changed
- changed ignoreVerbsOptions value to default true 


## [1.6.8] - 2019-4-1

### Added
- implemented OPTIONS in select on json driver


## [2.0.0]

### Added
- implemented fallback parameter to essentials/Http
- implemented timeout parameter to essentials/Http
- implemented FileStream Class to services/Process

### Changed
- services/Process, changed with breaking change
- services/Settings, some changes

## [2.0.1]

### Added
- implemented parameter METHOD on annotation of class router
