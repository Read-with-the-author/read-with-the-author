FROM php:7.4-alpine
VOLUME /srv/app
WORKDIR /srv/app
EXPOSE 80
# The built-in PHP webserver only responds to SIGINT, not to SIGTERM
STOPSIGNAL SIGINT
ENTRYPOINT ["php", "-S", "0.0.0.0:80", "src/LeanpubBookClub/Infrastructure/Leanpub/FakeServer/router.php"]
