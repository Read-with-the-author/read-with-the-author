# Read with the author

## Getting started

To run this project on your machine, you have to install Docker and Docker Compose.

After you have cloned this project to your machine:

1. `cd` to the root directory of the project
2. Run `docker-compose pull`
3. Run `docker-compose up -d` in the root of this project.

Then go to <http://localhost> in your browser and you should see the homepage.

Go to <http://localhost/admin-area/> to visit the Admin area. Here you can log in as user *admin* with password *test*.

In the Admin area you'll find a list of (fake) Leanpub invoice IDs, which you can use to sign up on the homepage as a regular user.

This project uses Mailhog to catch outgoing emails. Open <http://localhost:8025> to take a look at the emails that were sent by the application.

## Removing the project

If you no longer want to have this project installed on your machine, run `docker-compose down -v --rmi all`. Then you can safely delete the project directory.
