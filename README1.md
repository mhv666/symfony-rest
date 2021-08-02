## Download

```shell
mkdir symfony-docker
cd symfony-docker
git clone https://github.com/MM-seller-tribe/backend-case-study-marti-vicens.git
```

## Installing / Getting started

A quick setup you need to get the container up and runing.

```shell
make build
make run
make composer-install
```

## Building

If after that the the project still not runing , you can try this command righ here:

```shell

make prepare
```

This will try the steps below, and execute migrate and migrations.

## Some features i wish i could implement

- Advance filtering:
  feature to filter by less than, greater than etc..
  eg: price:gt= 100
- jmeter:
  used to simulate a heavy load on a server, group of servers, network or object to test its strength.
- Auth: with bearer token

## Features

Here are some features of this API

#### Swagger

```
http://localhost:300/api/doc
```

#### LIST

```
http://localhost:300/api/products
```

- Search by:

* q : search every row that match 'name'
  eg: q=pi -> results = pizza, pi, apio
* per_page: specify the number of items for this request
* entity_field: you can specify every field of the entity and will
  match the result eg:
  color=blue --> will return all objects of blue
* fields: you can specify wich fields you want the api to return eg:
  fields=name,color --> will return items only with this fields
* page: page you would like to retrive

#### get

```
http://localhost:300/api/products/80
```

#### post

```
POST URL http://localhost:300/api/products

{
"name": "placeat",
"description": "Qui optio consectetur ad ullam perspiciatis velit mollitia consequatur reprehenderit soluta quisquam qui numquam temporibus aperiam harum sed nesciunt quia sunt et et aut et.",
"image": "/tmp/d63fde10266c48b744b78e2f2762ef71.png",
"color": "teal",
"merchant": 228,
"category": 130,
"price": 290.28,
"ean13": "6938832514614",
"stock": 3,
"tax_percentage": 2
}
```

#### delete

```
http://localhost:300/api/products/81
```

#### PUT

```
PUT http://localhost:300/api/products/81

{
"name": "eteee",
"description": "Qui optio consectetur ad ullam perspiciatis velit mollitia consequatur reprehenderit soluta quisquam qui numquam temporibus aperiam harum sed nesciunt quia sunt et et aut et.",
"image": "/tmp/d63fde10266c48b744b78e2f2762ef71.png",
"color": "teal",
"merchant": 228,
"category": 130,
"price": 290.28,
"ean13": "6938832514614",
"stock": 3,
"tax_percentage": 2
}
```

## composer require packages

-symfony/orm-pack
-symfony/maker-bundle
-symfony/maker-bundle
-friendsofsymfony/rest-bundle
-symfony/validator
-twig
-doctrine/annotations
-symfony/test-pack
-symfony/phpunit-bridge
-composer require nelmio/api-doc-bundle
-symfony/asset
