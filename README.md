## Пример реализации связи Один-ко-Многим (у одного автора много книг, но каждую книгу написал только один автор)

1.  Creating Symfony Applications
```
composer create-project symfony/skeleton:"6.4.*" ./
symfony server:start
```
2.  Installation Doctrine ORM packages and make entiities and migrations
```
composer require --dev maker-bundle
composer require orm
composer require serializer validator
```
Modification of environment variables
Сгенерить сущности без связей, после создания сущностей добавить связи
```
symfony console make:entity Book\Author
symfony console make:migration
symfony console doctrine:migrations:migrate
```
3.  Creation of a dataset
```
composer require --dev orm-fixtures zenstruck/foundry
symfony console make:factory
symfony console doctrine:fixtures:load
```
4.  Creation of the api controller
```
symfony console make:controller --no-template BookController\AuthorController
```
