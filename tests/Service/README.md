# Technical Choices
- I am writing this URL shortener to minimize the length of the URL and maximize the size of the available namespace for possible links.  This is a hard tradeoff that I would normally defer to a product owner:
  - Do we want a greater degree of randomization in these link OR do we prefer very short URLs.
  - Since I’m envisioning that the URLs need to be as short as possible, this system will insert new URLs into the database and utilize the database’s auto increment to determine the integer that will be BASE62 encoded into the short URL.
  - An alternate approach would be too choose a random number within the namespace but this would risk contention on writes, with a larger number of writes within the namespace requiring a larger amount of “guesses” before we found an available unused id. 
- I wanted to dockerize this but didn’t get to that task.  It would be easy to create a docker compose configuration so that there are less manual steps to run to get this set up and working.


# Steps to initialize project
- Install PHP 8.3 `brew install php@8.3`
- Install Redis for PHP: `pecl install redis`
- Install MySQL `brew install mysql
- Install Redis `brew install redis`
- Run MySQL service `brew services start mysql`
- Run Redis service `brew services start redis`
- Install Symfony CLI `brew install symfony-cli/tap/symfony-cli`
- Clone Repo: https://github.com/stephenmayer/urlshortener
- Run `composer install`
- Run `php bin/console doctrine:database:create`
- Run `php bin/console doctrine:migrations:migrate`
- Run `symfony serve`
- Run message consumer:  `php bin/console messenger:consume async -vv`
- Browser: http://localhost:8000

To run automated tests:
- Run `php bin/console doctrine:database:create --env=test`
- Run `php bin/console doctrine:migrations:migrate --env=test`
- Run `php bin/phpunit`

# CSV Uploader
- web interface with web response: http://localhost:8000/

# Redirect:
- http://localhost:8000/{shortUrl}
  - redirects user to the target URL if it exists
  - responds with a 404 if it does not exist

# Analytics
- http://localhost:8000/analytics/{shortUrl}
  - endpoint will return the link url, the short link url and the number of clicks into the link

# API
- OpenAPI Swagger interface: http://localhost:8000/api
  - Enables direct access to the link table, allows inserting using an API interface and getting shortUrls, including analytics directly from this interface


