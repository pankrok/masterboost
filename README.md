# Masterboost V3
> Free web masterboost client.

## Table of contents
* [General info](#general-info)
* [Technologies](#technologies)
* [Setup](#setup)
* [Features](#features)
* [Status](#status)

## General info
Masterboost is a web client for the script masterboost by desire, it allows you to easily manage customer accounts and servers that are available in the boost.

## Technologies
* PHP 8
* Symfony 5

## Setup
* Download package and install libraries via composer `composer install`.
* Configure your DB and email in .env.local
* Run _php bin/console make:migration_
* Run _php bin/console doctrine:migrations:migrate_
* Run _php bin/console doctrine:fixtures:load_
* first login data: admin / admin
> [DEMO](https://boost.s89.eu) 

## Features

Ready features:
* RWD designe,
* tPay integration
* MicroSMS integration
* User cash transfer (between accounts)
* comments
* votes
* top servers of last week
* infopages
* promocodes

## Status
Project is: _abandoned_
