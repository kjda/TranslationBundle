TranslationBundle
=================

A symfony2 translation bundle 

Goal
====
 Easier Testing &amp; Translation management

Warning
=======
not complete! Early stage development 

What can it be used for?
=======================
right now you can use this bundle for easier testing. since it highjacks the default translator and always returns your translation keys. which means: if you update your translations, your tests will still pass.

What else can it do?
===================
When enabled it will catch all your translation keys and store them in the database. so that you can translate them using a web interface, you can have a look at it:  app_dev.php/kjda_translation/


What is missing?
===============
exporting translations to resource files.  
importing resource files to the database.  
a better domain management & domain detection while capturing keys.  
