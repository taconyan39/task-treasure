FROM php:8.5-apache

# 必要な部品（データベース接続やLaravel必須の機能）を入れるの
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_pgsql zip mbstring xml bcmath

# Laravelが正しく画面を出せるように設定を変更するの
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 便利なツール（Composer）を持ってくるの
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# アプリケーションのファイルをすべて箱の中に入れるの
COPY . /var/www/html

# ファイルの権限を整えるの
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Laravelの裏方の部品を組み立てるの（細かい部品のチェックはスキップするのよ）
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# サーバーの入り口（ポート80）を開けるの
EXPOSE 80