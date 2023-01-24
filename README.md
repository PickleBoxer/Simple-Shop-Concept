Simple Shop Concept
==========

Simple Shop Concept using Symfony

Requirements
------------

- PHP 8.1+
- [Composer](https://getcomposer.org/download)
- [Symfony CLI](https://symfony.com/download)
- [Docker & Docker compose](https://docs.docker.com/get-docker)

Getting started
---------------

**Cloning the repository**

```
$ git clone https://github.com/PickleBoxer/Simple-Shop-Concept
$ cd Simple-Shop-Concept
```

**Installing dependencies**

```
$ composer install
```

**Creating the Database**

```
$ php bin/console doctrine:database:create --if-not-exists
```

**execute a migration in database **

```
$ symfony console doctrine:migrations:migrate
```

**Loading Data Fixtures**

```
$ symfony console doctrine:fixtures:load
```

**Enabling TLS**

```
$ symfony server:ca:install
```

**OPTIONAL Local Domain Names**

- [Installed the local proxy](https://symfony.com/doc/current/setup/symfony_server.html#local-domain-names)

```
$ symfony proxy:start

$ symfony proxy:domain:attach my-domain
```

If you have installed the local proxy as explained in the previous section, you can now browse https://my-domain.wip to access your local project with the new custom domain.

**Launching the Local Web Server**

```
$ symfony server:start -d
```

**Open the website**

```
$ symfony open:local
```

**Stop the Web Server**

```
$ symfony server:stop
```