CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Recommended modules
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

 * Node field module allows you to add unique extra fields to single drupal nodes.
   It's not connected to fields module, so different nodes of one content type can have absolutely different sets of fields.


REQUIREMENTS
------------
 * This module requires the Libraries module
   https://www.drupal.org/project/Libraries

 * Submodule Node Field Gmap requires the Sheetnode module
   https://www.drupal.org/project/sheetnode


RECOMMENDED MODULES
-------------------

 * You can use date field, if you activate date and date_popup module:
   http://drupal.org/project/date

 * You can use node reference field, if you activate node_reference module:
   https://drupal.org/project/references

 * You can use select or other field, if you activate select_or_other module:
   https://drupal.org/project/select_or_other

 * You can add node fields as a field in Views (v.3) for the node content.


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. See:
   https://www.drupal.org/documentation/install/modules-themes/modules-7
   for further information.


CONFIGURATION
-------------

 * Go to admin/config/node-field. Configure Google Maps Api Key.


MAINTAINERS
-----------

 * This project is created by ADCI Solutions http://drupal.org/node/1542952 team.
