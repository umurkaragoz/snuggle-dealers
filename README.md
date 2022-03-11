# Snuggle Dealers
_"A shop for all the needs of your snugly friends!"_


## About

Snuggle Dealers is a sample app for [Buckhill](https://www.buckhill.co.uk/).

It is an API developed with Laravel 8.x, according to [The Pet Shop Project](https://pet-shop.buckhill.com.hr/api/swagger) specifications.



## Installation & Configuration


### Docker

- Clone the project.
- Copy `.env.example` to `.env`.
- Run;
  - `docker-compose build`
  - `docker-compose up -d`
  - `docker-compose exec web php artisan key:generate`



## Usage


### Swagger

- Use the swagger UI to interact with the API.
- Swagger definition is provided in `.json` format, on `/openapi` endpoint. It can be imported to and inspected with any [Swagger UI online](https://petstore.swagger.io/).


### Scripts

Utility scripts for the project setup and development.

 | Script                            | Description       
 |                               --- | ---
 | `composer db:fresh`               | Seeds and migrates the database.      
 | `composer generate-ide-helpers`   | Truncates, migrates and seeds the database.      
 | `composer setup`                  | Used for initial setup on a server.



## Technical

Notes about technical quirks and design decisions.


### Data Transfer Objects

Data transfer objects are simple objects to carry data.
They allow better IDE support for moving data, as well as parameter type checking.

- All DTOs extend `BaseDto`.
- By default, DTOs will try to populate its properties from the `request`.
- Preferred way of usage is returning DTOs from `requests` (Classes extending `FormRequest`) with a `getDto()` method.


### JWT Auth

- To save time, no refresh token system was implemented. It would exist in a production project.
- For the same reasons, no `refreshing` existing token entities was implemented. New tokens are created and old ones are invalidated instead.
- Private and public keys which added to the git repository are there for testing purposes. They meant to speed up the deploying and testing the app.
- For we have a really simple authorization rules, consisting only of a `is_admin` check, `restrictions` and `permissions` fields are not present on `jwt_tokens` table. There is `is_admin` field instead.
