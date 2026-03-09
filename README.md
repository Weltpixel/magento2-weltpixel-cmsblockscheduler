# magento2-weltpixel-cmsblockscheduler

### Installation

Dependencies:
- magento2-weltpixel-backend (see https://github.com/Weltpixel/magento2-weltpixel-backend)

With composer:

```sh
$ composer config repositories.weltpixel-magento2-weltpixel-backend git https://github.com/Weltpixel/magento2-weltpixel-backend.git
$ composer require weltpixel/magento2-weltpixel-backend:dev-master

$ composer config repositories.weltpixel-magento2-weltpixel-cms-block-scheduler git https://github.com/Weltpixel/magento2-weltpixel-cmsblockscheduler.git
$ composer require weltpixel/magento2-weltpixel-cmsblockscheduler:dev-master
```

Manually:

Important: Ensure you also install the shared Backend module. If it's already installed, you can skip this. Details in the repo at https://github.com/Weltpixel/magento2-weltpixel-backend.

Copy the zip into the app/code/WeltPixel/CmsBlockScheduler directory.


#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable WeltPixel_CmsBlockScheduler --clear-static-content
$ php bin/magento setup:upgrade
```

### Documentation

For detailed documentation, please visit: https://weltpixel.com/resources/ModuleDoc/Magento2/CMSBlockScheduler/User-Guide-WeltPixel-Cms-Block-Scheduler-Targeting-Magento2.html
