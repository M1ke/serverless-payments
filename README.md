# Serverless payments

This repository allows you to set up a serverles website, using AWS, to
accept payments via Stripe. All it requires installed on your machine is

* node >=12
* awscli
* docker

You will need AWS credentials available with relatively wide-ranging
permissions to create new resources and then manage them, however you
won't need to access existing resources in an AWS account that are
not managed by this repository.

## The website itself

To use the website we have to first deploy to Lambda or run locally 
using one of the sets of instructions below. The two are independent.

The website has two main views:

* Creating a payment - this is done from the "home" page (i.e. the root
  domain) and involves adding an amount and description.
* The payment view - once the user has created a payment a unique ID
  is created for that amount/description combination. Accessing this
  ID will allow someone to make a payment with the value and description
  fixed

The concept here is that whoever runs the payment site creates payments
and posts the unique links publicly. Anybody with a link can make a
payment with the required amount/description and will be emailed
a receipt by Stripe.

## Deploying an API to Amazon Lambda

The deployment to Lambda is controlled by `serverless.yml` and requires
the Serverless framework to be installed locally with:

```
npm install -g serverless
```

We then need to create a `.env.development` file from our `.env.sample`
file. The only essential key to set is `BUCKET_FRONT_END`, as this
defines the S3 bucket that serverless will create (and that the front
end will be deployed to later), but you can also add your own Stripe
test keys if you have an account.

A helper script exists to deploy a development version:

```
bin/deploy-dev-back
```

The first back-end deploy will take some time due to creating a CloudFront
distribution to serve content from. After the first deploy of a given
stage this delay will not occur again.

Once the back end is deployed we can run:

```
bin/deploy-dev-front
```

At the end of this we should see a URL to view our front end.

## Running locally

Before running we need to configure a local environment by copying
`.env.sample` to `.env.development.local`. At this stage of local
development there shouldn't be anything that needs changing, but
you can add your own Stripe test keys if you have an account.

(NB: I'll be using `nvm` to manage node versions, so typing `nvm use`
before any node commands will be necessary for each new terminal)

To run locally we need both Docker & docker-compose installed
as well as node v12 or greater.

```
docker-compose run composer composer install
npm i
docker-compose up -d web
npm run start
```

At this point the website should be accessible at
`http://localhost:3000/`. To test the API we can run
`curl -I http://localhost:8080/api/` to which we should see a 204
no content response.

## Future improvements

Some useful improvements from a user standpoint would be:

* Record user details of who made payments against incoming webhooks
* Dashboard to view created payments and user details
  * This would need some sort of simple authentication, possibly
    just a key set up in the .env file as a "password"

In terms of developer experience:

* Migrate complex blocks of CloudFormation code for Cloudfront,
  webhooks, queue processing to use getlift/lift plugin
* Tests are currently incomplete; would be good to ensure all tests can
  run, also Psalm, locally via composer
* GitHub actions at the very least to allow tests to run on the public
  repository
* Would be interesting if users who use the repository as a "template"
  repository would get a GitHub action setup to allow their deployment
  to be fully automated - would require committing .env files or
  otherwise linking secrets (only the Stripe private key is sensitive)
* Maybe some nice colours for the bin/scripts
