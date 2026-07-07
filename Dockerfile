FROM php:8.2-apache

# 必要な部品（データベース接続用など）を入れるの
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql zip

# Laravelが正しく画面を出せるように設定を変更するのよ
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 便利なツール（Composer）を持ってくるの
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# あなたの作ったタスクトレジャーのファイルを全部箱FROM php:8.2-apache

# 必要な部品（データベース接続やLaravel必須の機能）を入れる
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_pgsql zip mbstring xml bcmath

# Laravelが正しく画面を出せるように設定を変更する
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 便利なツール（Composer）を持ってくる
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# アプリケーションのファイルをすべて箱の中に入れる
COPY . /var/www/html

# ファイルの権限を整える
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Laravelの裏方の部品を組み立てる
RUN composer install --optimize-autoloader --no-dev

# サーバーの入り口（ポート80）を開ける
EXPOSE 80の中に入れるのよ
COPY . /var/www/html

# ファイルの権限を整えるの
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Laravelの裏方の部品を組み立てるの
RUN composer install --optimize-autoloader --no-dev

# 魔法の箱の入り口（ポート80）を開けるのよ
EXPOSE 80