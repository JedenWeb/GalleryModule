# Category Module

Note that this module is supposed to be a skeleton and further functionality is to be implemented as needed.

## Dependencies

### Nette Extensions
- kdyby/doctrine
- brabijan/images

### Our code specifics
- App\AppModule\AdminModule\SecuredPresenter
- App\IRouterFactory
- app/AppModule/AdminModule/templates/@layout.latte

## Installation

In config.neon

```
extensions:
	- App\GalleryModule\DI\CategoryExtension
```
