## How to make sure template tags exist
As template tags are provided by modules, some are only present when the 
respective module is loaded. When creating layouts you need to make sure the 
template tags you are using are acessible.

### Method 1 (recommended)
Just check if the template tag you want to use is defined somewhere:
```
if( function_exists( 'the_template_tag' ) ) {
  the_template_tag();
}
```
### Method 2 
If you know the id of the module providing the template tag you can also check
if the module is in the list of active module like this:
```
if( in_array( $this->modules, 'MODULE_ID' ) ) {
 the_template_tag();
}
```
