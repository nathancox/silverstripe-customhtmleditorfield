SilverStripe 3 CustomHTMLEditorField
===================================

Overview
--------------

This module allows you to set a custom body class on each editor field (for styling the content differently).

I'm working on allowing you to assign different HTMLEditorConfigs to each HTMLEditorField but 


Maintainer Contacts
-------------------
*  Nathan Cox (<nathan@flyingmonkey.co.nz>)


Requirements
------------
* SilverStripe 3.0+


Installation Instructions
-------------------------

1. Place the files in a directory called customhtmleditorfield in the root of your SilverStripe installation
2. Visit yoursite.com/dev/build to rebuild the database


Documentation
-------------
[GitHub Wiki](https://github.com/nathancox/silverstripe-customhtmleditorfield/wiki)


Example code:
```php
<?php


// make an HtmlEditorField
$fields->addFieldToTab('Root.Footer', $footerField = new HtmlEditorField('FooterText', 'Footer'));

// set the editor's body class.  This will make it class="typography footer-content"
$footerField->setBodyClass('footer-content');

```


Known Issues
------------
[Issue Tracker](https://github.com/nathancox/silverstripe-customhtmleditorfield/issues)