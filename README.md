This is copied from Ryan Christensen's original project for his OSX server: https://bitbucket.org/ryanchristensen/d4rk.co

Runs on bootstrap 3, Apache, and PHP7. 

I have made a number of changes:

Modification of functions to support Linux shell commands
Removed minecraft functionality
Removed functionality specific to Ryan's setup (Hard disk names etc) and added my own
Removed services I don't have on my server and added one (Subsonic)
Modified the plex token function to pull this automatically from myplex (this does slow down page loading a fair bit)
Moved user credentials to a config.ini you can place outside of your web root. Just make sure it is correctly included in the relevant functions that require it
Amended IP Addresses to reflect my LAN

