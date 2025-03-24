# Use official PHP image with Apache
FROM php:8.2-apache

# Copy application files into the container
COPY . /var/www/html/

# Set permissions (optional)
RUN chown -R www-data:www-data /var/www/html

# Enable Apache mod_rewrite (optional but useful)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80
