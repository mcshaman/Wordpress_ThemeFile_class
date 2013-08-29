# Wordpress ThemeFile class

As a Wordpress theme developer I struggled with streamlining my workflow when it came to dynamically managing styles and scripts. 

* When developing, I would have to change the URLs of my styles and scripts to unminifide versions for easy debugging. 
* Because of the dynamic nature of my style and script management I often needed to confirm the existence of a file to prevent errors in enqueueing.
* Styles and scripts declaration section of my themes functions.php file bloated and full of repetitive logic.
* Using the wp_localize_script to help Wordpress communicate with my scripts created unnecessary complexity.  

I wrote the ThemeFile class to help combat these issues. 

* The instanciation declaration closely resembles the wp_register_style and wp_register_script function declarations so the concepts should seem farmiliar.
* The register method respects the SCRIPT_DEBUG constant and will attempt to find an unminified version of the source file
* The enqueue method takes an optional safe argument which conforms source files existence before proceeding
* The localize method automatically includes the required enqueue method.

## Class synopsis

## Example

```php

```

## Licence

Do with it what you wish.
