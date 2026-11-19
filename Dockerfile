FROM php:8.3-apache

# Laravel の実行に必要なものと Composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# 依存パッケージのインストール（本番用）
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Apache: 公開フォルダを public/ にし、待ち受けポートを Render の PORT に合わせる
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN echo 'Listen ${PORT}' > /etc/apache2/ports.conf

# 起動スクリプト（Windows の改行コード CRLF が混じっていても動くよう変換）
COPY docker/entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

ENV PORT=10000
EXPOSE 10000
CMD ["/entrypoint.sh"]
