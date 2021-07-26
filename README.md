# backend-case-study-marti-vicens

Hi and welcome to the case study for a backend position at the Seller Tribe. This case study is intended to give you a glimpse of the kind of work you will be doing here at MetroMarkets.

Your goal is to implement a small API that simulates how sellers can define their products. For this, there are 2 tasks to develop:

- Database design of all tables that you will need to implement the product API.
- Product API implementation.

The only requirements that this API must meet are to use Symfony framework and PHP 7.2+

## What is expected?

- We will have a look at every file you deliver, so be careful with the details. This project is the chance you have to show us what you know. In this sense, we will assume that everything you deliver is something you are proud of.
- We are going to check exhaustively your code looking for best practices and habits.
- We expect to receive a dockerized project which we can run locally with a docker command. We are going to build and run your solution from the docker file given. Make sure it works!

## Context

We need to implement a new API for seller products in our MarketPlace. The current API has become deprecated and we are looking for ideas in order to implement a new one. This product API allows sellers to define which items they want to sell in the marketplace, so it is important that they are able to define all the attributes of these products.

## Tasks

### Database Model

Create database structure that will be able to store all the necessary data to respond the API requests. Feel free to use any database you wish.

- Deliver an entity map with all the relations in database.
- Deliver an script to be able to initialize the database with some dummy data.

### REST API

Implement a full rest API for the next actions:

- Get all their products
- Add a new product
- Remove a product
- Update a product

In addition, you are free to define which important attributes should be defined by the seller when creating or updating a product (ie. name, color, width...). Consider applying a filtering, sorting, and pagination strategy to the product endpoint if is needed.

You should include some unit and functional tests (at least, for one of the endpoints).

### Extra bonus

We would like to check that you like learning new languages, libraries… So if you are a Symfony and PHP expert, just try to use any library (ie. code quality, code metrics…) that you haven’t used before, just for fun! And let us know.

# How should it be delivered?

You must update this repository with your solution. Let us know when it is ready!

Be creative, don’t limit yourself to this definition and include everything you think it could add value for you to be the chosen one.

**This case study is your presentation card. We will consider this code as the best work you can do.**
