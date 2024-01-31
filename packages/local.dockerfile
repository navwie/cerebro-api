ARG DOCKER_SOURCE="public.ecr.aws/docker/library/"
ARG BASE_IMAGE="php"
ARG BASE_IMAGE_TAG="8.2-fpm-alpine"
ARG BASE_DIR="/var/www"
ARG CONTAINER_USER="nobody"
ARG SOURCE_DIR="cerebro"
ARG APP_PORT=80

FROM ${DOCKER_SOURCE}${BASE_IMAGE}:${BASE_IMAGE_TAG} as node_builder

ARG CONTAINER_USER
ARG SOURCE_DIR

USER root

WORKDIR /build

COPY ${SOURCE_DIR}/public /build/public
COPY ${SOURCE_DIR}/resources /build/resources
COPY ${SOURCE_DIR}/package.json /build
COPY ${SOURCE_DIR}/package-lock.json /build
COPY ${SOURCE_DIR}/vite.config.js /build
COPY ${SOURCE_DIR}/tailwind.config.js /build

RUN apk add --no-cache \
    nodejs \
    npm && \
    npm install && \
    npm run build && \
    npm prune && \
    npm cache clean --force

FROM ${DOCKER_SOURCE}${BASE_IMAGE}:${BASE_IMAGE_TAG} as composer_builder

ARG CONTAINER_USER
ARG SOURCE_DIR

USER root

WORKDIR /build

COPY ${SOURCE_DIR}/app /build/app
COPY ${SOURCE_DIR}/bootstrap /build/bootstrap
COPY ${SOURCE_DIR}/config /build/config
COPY ${SOURCE_DIR}/storage /build/storage
COPY ${SOURCE_DIR}/routes /build/routes
COPY ${SOURCE_DIR}/composer.json /build
COPY ${SOURCE_DIR}/composer.lock /build
COPY ${SOURCE_DIR}/artisan /build

RUN apk add --no-cache \
    curl \
    libpng-dev \
    libzip-dev \
    zlib-dev \
    git \
    ca-certificates \
    gnupg \
    libjpeg \
    libjpeg-turbo-dev \
    libpng \
    libjpeg-turbo \
    freetype-dev \
    libpng-dev \
    icu \
    icu-dev \
    tzdata \
    $PHPIZE_DEPS && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ --with-jpeg && \
    docker-php-ext-install calendar gd zip pdo pdo_mysql mysqli exif intl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --optimize-autoloader --no-interaction --no-progress

FROM ${DOCKER_SOURCE}${BASE_IMAGE}:${BASE_IMAGE_TAG}

ARG BASE_DIR
ARG CONTAINER_USER
ARG SOURCE_DIR
ARG APP_PORT
ENV APP_PORT=${APP_PORT}
ARG GITHUB_SHA
ENV GITHUB_SHA ${GITHUB_SHA}

USER root

WORKDIR ${BASE_DIR}

RUN apk upgrade --no-cache --update-cache \
    && apk add --no-cache \
    curl \
    wget \
    libjpeg \
    libjpeg-turbo-dev \
    libjpeg-turbo \
    libpng \
    libpng-dev \
    libzip-dev \
    freetype-dev \
    icu \
    icu-dev \
    zlib-dev \
    imagemagick \
    imagemagick-dev \
    git \
    ca-certificates \
    jq \
    nodejs \
    npm \
    gnupg \
    nginx \
    supervisor \
    supercronic \
    mc \
    tzdata \
    pcre-dev \
    $PHPIZE_DEPS \
    gcc \
    make \
    g++ \
    linux-headers \
    && pecl install redis imagick xdebug \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ --with-jpeg \
    && docker-php-ext-install calendar gd zip pdo pdo_mysql mysqli exif intl \
    && docker-php-ext-enable redis.so imagick \
    && docker-php-source delete \
    && apk del \
    libpng-dev \
    zlib-dev \
    imagemagick-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    pcre-dev \
    $PHPIZE_DEPS \
    && rm -rf /tmp/pear html \
    && mkdir -p ${BASE_DIR} \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini

COPY --chown=${CONTAINER_USER} ${SOURCE_DIR} ${BASE_DIR}

COPY --chown=${CONTAINER_USER} --from=node_builder /build/node_modules ${BASE_DIR}/node_modules
COPY --chown=${CONTAINER_USER} --from=node_builder /build/public ${BASE_DIR}/public
COPY --chown=${CONTAINER_USER} --from=composer_builder /build/vendor ${BASE_DIR}/vendor

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
        composer install --optimize-autoloader --no-interaction --no-progress

COPY php_config/php.ini-production /usr/local/etc/php/php.ini
COPY php_config/php-fpm.www.conf /usr/local/etc/php-fpm.d/www.conf

COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/local.conf /etc/nginx/conf.d/default.conf

COPY supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN sed -i "s,php-fpm -F,php-fpm -F --allow-to-run-as-root,g" /etc/supervisor/conf.d/supervisord.conf

EXPOSE ${APP_PORT}

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# isn't working with stargz
# HEALTHCHECK --interval=60s --timeout=10s --retries=2 \
#     CMD wget --spider --no-verbose --tries=1 127.0.0.1:${APP_PORT}/health || exit 1
