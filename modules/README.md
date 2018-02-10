# Creating modules

This documentation is intended for developers only!

## Basic concept
Originally I created the modular concept to make it easier to create code for
optional functionality. For example to add metadata to recipes that probably not 
everybody is going to use.
Quite soon however it got obvious that the concept of having modules is much
more powerful. Basically a module is a bundle of code that is pretty much 
independent. It is just hooked into at some important points of the recipe 
creation.
The first modules all have been providing a metabox for the editor screen, a 
function to save the metaboxes' code, some template tags to render the specific
metadata and a function to provide these data as structured metadata. As some of
these boxes already had some options I've also added an options hook.

Modules are being automatically loaded and can then call their own hooks.

## Creating a module

All modules have to be of class `RPR_Module` or one of it's subclasses. 
Each module has ist own directory inside the modules directory. Loading in core 
is done automatically.

### Prefixes 
- `mb_` indicates a metabox module of class `RPR_Module_Metabox`

### Options
To be documented. Have a look at the demo-module