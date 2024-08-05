# Use the official PHP image as the base image
FROM php:7.4-cli

# Set the working directory
WORKDIR /usr/src/app

# Copy the current directory contents into the container at /usr/src/app
COPY . .

# Install any necessary dependencies
RUN apt-get update && apt-get install -y libcurl4-openssl-dev

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions if necessary
RUN docker-php-ext-install curl

# Command to run your PHP script
CMD ["php", "massiveTel.php"]
