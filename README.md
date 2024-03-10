# Product Requirements
1. Users should be able to upload a batch of Long URLs to be shortened via a CSV file.
   - Should we have a web page where the user can upload the CSV file?
     - Assuming that yes, it would be likely they would want a simple HTML upload form to make this straightforward to use.
     - At a minimum we should have an API that can receive this file
       - POST /urls
     - How many URLs should this system be able to host?
       - Billions?
     - Should the URL redirect eventually expire?  If so, how long should it remain valid before it expires?
       - For enterprise use we need to consider maintaining the system over time and preventing the scenario of running out of possible URLs
     - Maximum length of a link?
       - Assuming 4096 should be long enough
     - Once the links have been uploaded via CSV, what should the response be?
       - CSV with short and long urls?
       - ASSUMED: A webpage showing the short and long urls mapping?
     - Can a particular link be included in the database multiple times?
       - Assuming the answer is “NO”
   

2. Short URLs should redirect the user to the Long URL destination.
   - What should the format requirements of the URL be?
     - Assuming a string  
   - Should the URL be unpredictable?
     - Assuming that this doesn’t matter, optimizing for namespace and simplicity initially.
   - What should the URL format look like?
     - Perhaps something like: http://localhost/abcdefg
   - How many requests should we expect the system to process per second?
     - Perhaps 1 million per day?


3. Each visit to a Short URL should be tracked for analytics.
- What analytics should we track here?  A few possible options come to mind:
  - Number of clicks
- Assuming that they will want the number of clicks
  - A log of clicks so that we can see the time
    - It’s unlikely that they would need this level of definition, assuming no
- Request per second requirements?
  - The number of requests per second could cause some sort of writes per second constraints on the database server due to the need to track these analytics.  In an enterprise system I would suggest perhaps a separate database server to accept these writes, along with a write queue to allow the database to process these requests in an eventual consistency manner …
  - Assuming that the number of reads would far exceed the number of requests to create new URLs … so we should heavily optimize for reads over writes.
    - Since a read will cause a write (eg update the statistics for a URL) we should build a simple message queue to handle these elegantly

4. An endpoint to retrieve data about the Short URL, like the visit analytics.
   - Can I request a URL using its short code?
     - Assuming yes
   - Should all of the data be exposed using a restful API?
     - Assuming yes, exposing using OpenAPI/swagger interface with API framework


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


