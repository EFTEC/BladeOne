<?php
// Set language to German
putenv('LC_ALL=de_DE');
setlocale(LC_ALL, 'de_DE');
// Specify location of translation tables
bindtextdomain("myPHPApp", "./locale");
// Choose domain
textdomain("myPHPApp");
// Translation is looking for in ./locale/de_DE/LC_MESSAGES/myPHPApp.mo now
// Print a test message
echo gettext("Welcome to My PHP Application");
// Or use the alias _() for gettext()
echo _("Have a nice day");
