# JukePi

## Description 
We are bringing the 90’s vibe back for the new millennials.

This simple application aims to entertain while still maintain the social distance measures.

With the continued rise in COVID 19 cases within the country, it has been difficult to keep businesses that rely on entertainment running, bars, restaurants and other social gathering dependent businesses have been hit  the hardest.

One of Tanzania’s main entertainment driver for all age groups, is Karaoke nights. However currently with all the Covid-19 safety precautions, it is not advisable to continue with the trend, and this is where JukePi comes to play.

JukePi is a digital Jukebox which turns any social entertainment spot into a digital JukeBox machine, no DJ required! just You playing what you want simply by sending an SMS.

The JukePi application runs on a [RaspberryPi] (https://www.raspberrypi.org) and is simple to integrate with any music system since it only requires a 3.5mm headphone jack to link.

This is the new era of entertainment. 

With this system assuring clients of continued entertainment while social distancing, we can estimate an increase of customers and also revenue towards businesses that would implement this.

## Deployment Steps
### Requirements:
    - Raspberry Pi ( Any version works )
        - Raspbian (http://www.raspbian.org)
        - Python version 3 (https://projects.raspberrypi.org/en/projects/generic-python-install-python3)
        - PiP3 (https://pip.pypa.io/en/stable/)
        -  Python VLC (https://wiki.videolan.org/Python_bindings/)

    - Web Server
        - Any web server stack publicly accessible 
        - PHP ver5 or greater
		
## Instructions:
### Server:
    - Pull folder JukePiServer into your web directory
    - Open folder
    - Create .env file on the root folder
    - Edit the following details
        APP_ENV=dev
        DATABASE_HOST=
        DATABASE_USER=
        DATABASE_PASSWORD=
        BEEM_API_KEY=
        BEEM_API_PASSWORD=
        ADMIN_CONTACTS=
        DATABASE=
    - Update your call back URL on BEEM Two Way Sms dashboard

### RaspberryPi:
	- Pull the folder JukePi to your RaspberryPi
	- Open the directory where the folder is downloaded
	- Open terminal in the directory
	- Run `nohup python play.py &`

## FAQ
If you find or notice any bugs please report them by sending us an e-mail: info [at] qroo [dot] co [dot] tz . We will fix bugs as soon as possible. If you have any feature suggestions, please create an issue with detailed information.

## License
JukePi is released under the GNU General Public License v3.0 (GPLv3)