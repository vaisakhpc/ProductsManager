### Coding Challenge

# Steps to launch the application
  - Symfony 4 is the framework used.
  - Used docker to implement the env.
  - ```docker-compose build```
  - ```docker-compose up -d```
  - ```docker-compose run php composer install```
  - You will be able to launch it in *http://localhost:8088* from your browser or Rest API client(like POSTMAN)

# Structure of API
  * Used Symfony 4 as provided
  * Followed Domain driven development (DDD)
  * Controllers will be found in src/Controller directory
  * Domain files will be found in src/Domain directory
  * Service files will be found in src/Service directory
  * Repository files will be found in src/Service directory
  * Entity files will be found in src/Service directory
  * Model files will be found in src/Model directory
  * Event Listener files will be found in src/EventListener directory
  * Unit tests are written for Models, Domain, Service, Security and EventListeners
  * Functional test is written for Controllers using WebtestCase. Please make sure that env variable named FUNCTIONAL_TEST_ENABLE should be set as true in phpunit.xml
  * Achieved Code coverage of 100% wherever applicable

# Steps to perform unit tests
  - ```docker-compose run php composer test``` For testing
  - ```docker-compose run php composer test-coverage``` For test coverage
  - ```"test": "php /www/vendor/bin/phpunit --colors=always --stderr",
       "test-coverage": "php /www/vendor/bin/phpunit -d memory_limit=512M --colors=always --coverage-html reports/phpunit/html --coverage-clover reports/phpunit/clover.xml --log-junit reports/phpunit/junit.xml"``` These are the underlying scripts. For easiness, shorted those in composer.json scripts part.

# Coding Standard
   * Followed PSR2 standard here.

# Sample endpoints
  * Sample endpoints are added in the postman collection. Visit here, [Postman Collection](./docs/Saloodo-Collection.postman_collection.json)

# Mysql Structure
 - The dump is added in this path [Dump file](./docs/saloodo.sql).

# Unit Tests
 - Used PHPUnit
 - version used : PHPUnit 7.4.5
 - 100 % code coverage is achieved. Added that also to git just for reference(normally reports directory shouldn't be added, just adding here since it could reveal the reports without forcing on your side to run it)
 - coverage reports will be available here, [report](./symfony/reports/phpunit/html/index.html)
 - Functional test cases should run only after the DB is imported

# Authentication
 - JWT authentication is used here
 - User provider used is [here](./symfony/src/Security/UserProvider.php)
 - PUBLIC, PRIVATE key and JWT PASSPHRASE for JWT generation is added in the .env variables for the easiness usage.

# Steps to run application & test
 * follow the steps for docker container setup as given above
 * run ```composer install```
 * ```cp .env.dist .env```
 * ```cp phpunit.xml.dist phpunit.xml```
 * import the DB
 * use Get JWT token - Login endpoint given in the [postman collection](./docs/Saloodo-Collection.postman_collection.json) to obtain valid JWT. User tokens can be only used for submitOrder, getOrder, fetchProduct & fetchProducts endpoints. Admin tokens should only be used for Create, update and delete endpoints. All the get points will accept both User and admin tokens
 * run ```composer test``` & ```composer test-coverage``` to run the tests and to get the coverage details respectively.
 * Refer this file for feature test to know about the endpoint structures. [ShopFeatureTest](./symfony/tests/Controller/ShopFeatureTest.php)
