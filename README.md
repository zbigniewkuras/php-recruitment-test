# Snowdog PHP Recruitment test

Test scenario is cache warming application.
When website uses full page caching (like Varnish Cache) there may be requirement to periodically warm cache contents.
This consists of purgin cache contents for given address and visiting given address.
Following application allows for multiple users to define multiple websites and for those websites define multiple URLs to visit.

Application uses:
* [PDO](http://php.net/manual/en/book.pdo.php) for MySql access
* [Silly](http://mnapoli.fr/silly/) for CLI commands
* [FastRoute](https://github.com/nikic/FastRoute) for routing
* [PHP-DI](http://php-di.org/) as dependency injection container

Following diagram shows basic database scheme
![Database Scheme](doc/db.png)


## Task 1

For this git repository.
_Make sure to commit Your work after every completed task._

Install and configure application by running following command
```php console.php migrate_db```

You can run PHP build-in server by running following command
```php -S 0.0.0.0:8000 -t web/```

The web application is running at http://localhost:8000.

Now create `.gitignore` file appropriate for Your development environment.

## Task 2

As You may have noticed console command ```php console.php warm my_website``` is failing because it cannot access legacy library located in `lib` directory.
Fix this problem.

## Task 3

Modify application so that we can see and track time of last page visit.
This will require database modification, changes to cache warming process and changes to pages views.

## Task 4

Allow users to define multiple caching servers.
Each caching server has it's own unique IP address and can cache multiple websites.
You can assume that different users do not share caching servers.
This will require database modification, changes to cache warming process and changes on frontend.

## Task 5

Create new empty git repository.

In newly created repository create new application component that allows adding new websites and pages by importing [sitemap file](http://www.sitemaps.org/).
This functionality should be accessible via both command line and frontend.
