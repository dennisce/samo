# Field Multiple Extended

Provides customizations per field instance for unlimited multiple value fields.

# Features

* Per field instance settings.
* Minimum number of field items required.
* Maximum number of field items allowed.
* Remove the extra empty item on the multiple value form.
* Adds a table row class (field-item-required) for required items in the
  field multiple table.

# Field Instance Settings

The following customizations work with fields that have an unlimited
cardinality.

* Enable Field Multiple Extended: If enabled then this module will alter the
  field multiple value form.
* Minimum required items: By default in Drupal, a required field requires only
  1 item to be completed. This setting adds a minimum number of items required
  that have to be completed for a required or optional field. Optional fields
  can be either empty or have the minimum number of items.
  Example: Minimum of 2 cards.
* Maximum allowed items: This setting can be changed per field instance since
  the field must be unlimited. If a field was stored with more items than the
  max allowed, then the items will be flagged as errors on the edit form only.
* Exclude the extra empty item: By default in Drupal, an unlimited field has an
  extra field item form displayed. This is not ideal when using large field
  collection fields.

# Installation

* Install as usual, see http://drupal.org/node/895232 for further information.
* Navigate to administer >> build >> modules. Enable Field Multiple Extended.
* Create / Edit a field:
  * Set the field setting "Number of values" to "Unlimited".
  * Populate the "Field Multiple Extended" settings.
