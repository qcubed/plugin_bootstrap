# QCubed Bootstrap Plugin
QCubed plugin to simplify the integration of Twitter Bootstrap

## Installation
1. Install both the plugin and twitter bootstrap using Composer.
To install, add the following to the corresponding sections of your composer.json root file:
```
	"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/qcubed/plugin_bootstrap"
        }
    ],
```
and
```
	"require": {
		"qcubed/plugin/bootstrap": "dev-master"
		"twbs/bootstrap": "~3.3@dev"
	},

```
2. Next, you might want to set up some configuration settings in your configuration.inc.php file.

### __BOOTSTRAP_CSS__
The default setting for this file is:
```
	define ('__BOOTSTRAP_CSS__', __VENDOR_ASSETS__. '/twbs/bootstrap/dist/css/bootstrap.min.css');
```
If you are compiling your own custom version of the bootstrap css file, simply set that define to point to your own version.

3. Point the base class to Bootstrap classes so that they add their functionality.

In your project/includes/controls/QControl.class.php file, have your QControl inherit from the base class. For example,
you should change the first line to:

```
abstract class QControl extends QCubed\Plugin\Bootstrap\Control {
```

## Usage

See the examples pages for details.
