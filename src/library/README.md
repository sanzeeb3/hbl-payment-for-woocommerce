# PHPDemo
### Implement PACO Core API's like C# .net Sample Code
It's having implementation of below APIs with json & jose request both:
1. Payment
2. Inquiry
3. Void Request
4. Settlement
5. Refund

### Used Third-Party Libraries
1. PHP JWT Framework : https://github.com/web-token/jwt-framework
2. PHP HTTP client : https://github.com/guzzle/guzzle
3. PHP DateTime : https://packagist.org/packages/nesbot/carbon
4. PHP OpenSSL : https://www.php.net/manual/en/book.openssl.php

### Required PHP Min 8.1.0 Version
> php8.1.0 (https://www.php.net/releases/8.1/en.php)

> apache or nginx server to run php

### Install Apache Server
> sudo apt-get update 

> sudo apt-get install apache2

Check apache status : 
> sudo systemctl status apache2

### Install Composer
> sudo apt install composer

### Clone Repo :
 Clone project repo in var/www/html directory

 `git clone https://scm.2c2p.com/scm/paco/phpdemo.git`

### Install dependency
> php composer.phar install

### Update dependency
> php composer.phar update

### Run index.php file
http://localhost/phpdemo/index.php