#Challenge #1

Create a web widget that displays the current weather for a user provided zip code.
You have complete control over the program design. This exercise is intended to get a feel for your problem solving ability, creativity, and code style.

##Weather API Details

You can use the following API for weather information.  Feel free to use a different API if you want to.

- URL: https://openweathermap.org/api
- API Key: a0b48e1390d21ffe9bd578d7428b9f00

##Minimum Requirements

- HTML page to display user zip code UI and weather information
- Backend code that will take the zip code and request weather information from the API.
- Validate user zip code input

##Extras

- Persist the user's zip code so that their zip code is remembered when returning to the widget.

---

#Boilerplate

This directory contains a very basic starting point for challenge 1. The instructions below should work for Linux/MacOS.

##Starting Symfony server
```
cd /path/to/boilerplate
composer install
yarn install
yarn encore dev
symfony server:start
```

Visit [http://localhost:8000](http://localhost:8000) to interact with code.

If the Symfony CLI tool is not available, visit https://symfony.com/download to install it.
