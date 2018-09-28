## includes

This directory contains hook samples, WP-CLI recipes, and debugging code that you might find useful in your plugin or theme. 

### Hooks 

Actions and filters should be used in a plugin rather than a theme. Why? [This post](http://webcraft.tools/stop-adding-code-functions-file/) has a great breakdown:

> Your theme’s functions.php file is powerful and has a purpose. It stores all the functions that are particular to that specific theme and only that theme. Plugins should be used whenever you want to add functionality that should remain regardless of your current theme. The reason is simple: if you add this functionality to your theme and then down the road decide to use a different theme then you will lose all those custom functions unless you copy and paste them all over to your new theme.

#### hooks-core-actions.php

This file contains actions that are run with the Login by Auth0 plugin. Actions run code at a specific place in the WordPress runtime and typically provide one or more variables used to determine the action to take. All current actions should have at least one example here. For more information on there, please see the [Extending docs page, Actions section](https://auth0.com/docs/cms/wordpress/extending#actions). 

#### hooks-core-filters.php

This file contains filters that are run with the Login by Auth0 plugin. Filters run code at a specific place in the WordPress runtime and return a value that's used in the code following. They typically provide one or more variables used to determine the value to return. All current filters should have at least one example here. For more information on there, please see the [Extending docs page, Filters section](https://auth0.com/docs/cms/wordpress/extending#filters). 

#### hooks-other.php

This file contains examples of other customizations possible using core WP or other plugin hooks. 