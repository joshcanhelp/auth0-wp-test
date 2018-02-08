### `hooks.php`

Actions and filters, like the examples in `hooks.php`, should be used in a plugin rather than a theme. Why? [This post](http://webcraft.tools/stop-adding-code-functions-file/) has a great breakdown:

> Your theme’s functions.php file is powerful and has a purpose. It stores all the functions that are particular to that specific theme and only that theme. Plugins should be used whenever you want to add functionality that should remain regardless of your current theme. The reason is simple: if you add this functionality to your theme and then down the road decide to use a different theme then you will lose all those custom functions unless you copy and paste them all over to your new theme.