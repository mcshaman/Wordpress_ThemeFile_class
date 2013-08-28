# Wordpress ThemeFile class

As a Wordpress theme developer I struggled with streamlining my workflow when it came to dynamically managing styles and scripts. When developing I wanted to easily be able to unminifide versions of my styles and scripts for easy debugging. Because of the dynamic nature of my style and script management I also often need to confirm the existence of a file to prevent errors in enqueueing. I also often found myself using the wp_localize_script to help Wordpress communicate with my scripts. All this left my styles and scripts declaration section of my themes functions.php file bloated and full of repetitive logic.

I wrote the ThemeFile class to help combat this issue. The instanciation declaration closely resembles the wp_register_style and wp_register_script function declarations. 

## Example

```php

```

## Licence

Do with it what you wish.
