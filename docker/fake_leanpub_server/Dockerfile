FROM php:7.4-alpine
VOLUME /srv/app
WORKDIR /srv/app
ENTRYPOINT ["php", "-S", "0.0.0.0:80", "src/LeanpubBookClub/Infrastructure/Leanpub/FakeServer/router.php"]
