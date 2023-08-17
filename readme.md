# Outline VPN - Telegram Bot

## Share your Outline VPN access keys through your bot on DigitalOcean Functions (serverless)

## Summary

A simple Telegram bot with which your users can generate a new personal key to access your Outline VPN server.

## Features

- Generate a new private key
- Get notified when a new key is generated

## Setup

Set up your Outline VPN server and create a new bot with Bot [@BotFather](https://t.me/BotFather). More information about Outline VPN can be found [here](https://getoutline.org/). Then write hard-coded (temporary) variables (I'll move them to env files soon):
* TELEGRAM_BOT_TOKEN
* TELEGRAM_BOT_ADMIN_CHAT_ID
* API_PORT
* API_URL
Dont forget to set up your bot's webhook. You can use [this get request](https://api.telegram.org/bot<token>/setWebhook?url=<url>) to do so - put your token and URL of deployed function. 
## Deploy

## TODO

* Refactor the code to use env files instead of hard-coded variables.
* Don't use default php CURL
* Rewrite this readme file


## References

[DigitalOcean Functions](https://docs.digitalocean.com/products/functions/)

[DigitalOcean example PHP repo](https://github.com/digitalocean/sample-functions-php-numberstowords)
