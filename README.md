# Serverless payments

This repository allows you to set up a serverles website, using AWS, to
accept payments via Stripe. All it requires installed on your machine is

* docker
* docker-compose
* node >=12
* awscli

You will need AWS credentials available with relatively wide-ranging
permissions to create new resources and then manage them, however you
won't need to access existing resources in an AWS account that are
not managed by this repository.

## Experimenting locally

Before running we need to configure a local environment by copying
`.env.sample` to `.env.development.local`. At this stage of local
development there shouldn't be anything that needs changing, but
you can add your own Stripe test keys if you have an account.

(NB: I'll be using `nvm` to manage node versions, so typing `nvm use`
before any node commands will be necessary for each new terminal)

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

On the website itself we should be able to make a payment by
following the form inputs.

## Deploying an API to Amazon Lambda

The deployment to Lambda is controlled by `serverless.yml` and requires
the Serverless framework to be installed locally with:

```
npm install -g serverless
```

We then need to create a `.env.development` file

A helper script exists to deploy a development version:

```
bin/deploy-dev-back
```

The first back-end deploy will take some time due to creating a CloudFront
distribution to serve content from. After the first deploy of a given
stage this delay will not occur again.

Once the back end is deployed we can run

```
bin/deploy-dev-front
```
