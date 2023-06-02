

Runs on bootstrap 3, Apache, and PHP7. 


This code has been heavily modified from Ryan Christensen's original project for his OSX server: [https://bitbucket.org/ryanchristensen/d4rk.co](https://github.com/d4rk22/Network-Status-Page). 
Original credit goes to Ryan, thanks for the framework!

Runs on bootstrap 3, Apache, and PHP7. 

Preview:

[Imgur](https://i.imgur.com/m7xceb8.png)

Home Lab DASHBOARD

I have made a  number of changes:
- Updated to use PHP7 so ssh login functionality now works with newer systems. PHPSeclib was getting long in the tooth.
- Changed the weather API to VisualCrossing as DarkSky is now defunct :(
- Created a weather 7 day forcast
- Added Google calander API functionality so that I can get the next 5 days of my Cal.
- Added functionality to pull data from influxdb, getting this like temperature and humidity in house.
- Added new functionality to pull from SabNZB queue and list was is being pulled and whats recently completed. 
- Removed Plex Play
- Removed services I don't have on my server and added others.
- Moved user credentials to a config.ini you can place outside of your web root. Just make sure it is correctly included in the relevant functions that require it

You will need to run this on Apache or NGINX, using PHP7 and have a google developer account to get an API key. 



