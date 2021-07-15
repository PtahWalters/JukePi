# JukePi

## Description 


## Deployment Steps
    Requirements:
        - Raspberry Pi ( Any version works )
            - Raspbian [Click here for Installation guide] (http://www.raspbian.org)
            - Python version 3 [Click here for Installation guide] (https://projects.raspberrypi.org/en/projects/generic-python-install-python3)
            - PiP3 [Click here for Installation guide] (https://pip.pypa.io/en/stable/)
            -  Python VLC [Click here for Installation guide] (https://wiki.videolan.org/Python_bindings/)

        - Web Server
            - Any web server stack publicly accessible 
            - PHP ver5 or greater
		
## Instructions:
    Server
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

## RaspberryPi
	- Pull the folder JukePi to your RaspberryPi
	- Open the directory where the folder is downloaded
	- Open terminal in the directory
	- Run `nohup python play.py &`