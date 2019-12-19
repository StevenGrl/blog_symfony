# blog_symfony

This blog is a project created by GRILLON Steven, as part of Professional License formation.

This is a blog with some security, you can create update and delete an article if you are logged in with the user that created the article.

You can see all article published or yours not necessarily published.

You can find all your articles in ```See my articles``` even they are not published

You can like articles that you didn't create and find them in your user interface as ``My Favorite Articles``

You can switch theme (Dark / Light)

System requirements
-------------------

* PHP 7.3.5 _(version in which the project was created, an earlier version (at least 7) will have to work)_

* Git https://git-scm.com/book/fr/v2/D%C3%A9marrage-rapide-Installation-de-Git

* Database SQLITE you may need to enable sqlite in your php.ini

* Composer https://getcomposer.org/doc/00-intro.md

* Symfony https://symfony.com/download


How To Use
----------

To clone and run this project, you'll need Git, Composer and Symfony. From your command line:

**Clone this repository**  
$ git clone https://github.com/StevenGrl/blog_symfony.git

**Go into the repository**  
$ cd blog_symfony

**Install dependencies**  
$ composer install

**Init Database**  
Make a file in var called data.db  
$ php bin/console doctrine:migration:migrate  

**Load fixtures**  
$ php bin/console doctrine:fixtures:load

**Launch PHP  Server** (DEV Only)    
$ symfony server:start
and then you can go on http://localhost:8000