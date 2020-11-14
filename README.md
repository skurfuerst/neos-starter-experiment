# Neos Starter and Demo Site

## Getting Started

### Contentpflege

```
cd generator
composer install
./flow starter:kickstart ../profiles/2020-09-a/fullProfile.json ../profiles/2020-09-a-full-instance
cd ../

cd profiles/2020-09-a-full-instance
docker-compose build --pull
docker-compose up -d
```


## What do we want?

We want to create an easy way to try out Neos and ensure people are getting started with best practices enabled.

- Create an online "Kickstarter" combined with a personalized demo instance
- something like https://start.spring.io/
- A) with the option to download your kickstarted distribution
- B) dditionally the kickstarted distribution can run online directly; as a Demo instance.

The constraints for A and B are different:

- A) should be as flexible as possible
- B) must be somehow restricted regarding the amount of packages to be installed, as it is running on our Neos infrastructure
- The demo instance must be quickly created (B)

## What do we NOT want

- We do not want to create something like symfony flex, as creating a kickstarter is way easier than creating such a system where we incrementally update files.


## Architecture Decisions

- Kickstarter implementation in JS or PHP? -> **Flow**
    - benefit of JS would be extensibility by the end-user of the deployed application (without deploying it himself)
    - however, we cannot trust what happens client-side; thus we have to use **PHP** on the server side - and I suggest to use **Flow** as basis as this is (obviously) well known in the community.
    - The kickstarter frontend can hopefully be copied from https://github.com/spring-io/start.spring.io/tree/master/start-client

- Security & Speed
    - We need to be reliably sure that no matter what the user selects in the kickstarter, a working distribution without fatal errors is created.
        - This is hard, as there are loads of multiplied-out variants with all the configuration options.
        - Thus, we cannot test every case up-front.
        - Thus, we cannot support arbitrary packages; and only "known"/whitelisted versions of a package. Otherwise, a patch-level release in a package might break our system.
        
    - How does the kickstarter create the distribution in a fast way?
        - running "composer install" is too slow, as this triggers resolving of packages if no composer.lock exists.
        - Additionally, lots of packages need to be downloaded which is slow as well.

    - SUGGESTION: let's start with a base composer.json/composer.lock, which contains ALL supported packages AT ONCE.
        - then, when the user has done the final selection of packages, we trigger `composer remove` to remove all packages which are not needed.
        - this is quicker than a `composer update`
        - we can test this combined distribution with some automated tests (checking for 500 errors in FE and BE).
        - we can pre-load all packages in a big Docker image for deployment, and on startup, remove these packages.

- kickstart on the server side
    - this can be modelled or adopted from Symfony Flex maybe:
        - config: https://github.com/symfony/recipes/blob/master/doctrine/doctrine-bundle/1.6/manifest.json
        - https://github.com/symfony/flex/tree/master/src/Configurator für recipes
    - What do we want to make configurable?
        - What packages are installed
        - What languages are configured
        - With or without example content?
        - A fitting README adjusted to these packages.
    


- Hosting the demo sites
    - After discussing with Robert, he and me decided to go pragmatic. This means:
        - we will not guarantee any uptimes; no loss of data; ...
        - the service is simply provided on a best effort basis.
        - the service won't be highly available; thus we won't have failover.
    - We use a single-server setup where docker is running. Both the Kickstarter and the demo sites are running on the same server, in different docker containers.
    - The online kickstarter creates the other docker images through the `docker` CLI.
    - NOTE: while the kickstarter creates a Dockerfile, this is NOT used in the hosted solution (because of package preloading)

## Process as it feels to the end-user

1. visit start.neos.io, select packages, languages, example content yes/no
2. two options:
    1. Download zip to run on your own
    2. Start a demo instance (we will continue with this path)
4. We run the kickstarter server-side
5. The demo instance will be created:
    - based on the pre-baked docker image which contains everything (so no `docker build` necessary)
    - with a mounted volume where the kickstarter output is placed (i.e. the generated site package and the README etc).
    - in an env variable, it is listed which packages should be removed on startup
    - the entrypoint script runs `composer remove` as stated above.
6. While the demo instance is created, we ask the user for creating an admin user and password.
    - We do this on an extra screen to bridge the time until the instance is up and running.
    - Additionally, we suggest that he hands over his email - and ask whether he wants to join the newsletter.
    - If he hands over his email, he will be able to download a content dump at the end of the 2 week test period when the kickstarted demo site is automatically removed.
7. When the docker container is up and running, it will be automatically accessible under a custom subdomain from our reverse proxy - we suggest to use `traefik` here, as it does exactly what we need there. We need a wildcard SSL certificate, as otherwise we will hit the 50-cert-per-day limit from LE. That's possible using the DNS challenge - see https://docs.traefik.io/https/acme/#wildcard-domains
8. We will then create the admin account for the end-user, and send a welcome email.
9. This mail contains a secured link to create & download the site export and the Distribution.
10. After 2 weeks, we create a final site export, we kill the site, ask for feedback via email, and send the site export link via email.
11. After another 4 weeks, we remove the site export fully on the server.

