#WordPress Setup

WordPress Setup is a script you can literally drop into your development environment (or live setup) that will, once run, 
automatically download the latest version of WordPress and create the database for you, so you can skip straight
on to installing WordPress.

## Requirements

* PHP 5 or greater
* MySQL 4.1.2 or greater
* MySQL user with "CREATE DATABASE" privileges

## Usage

* Download and extract
* Put the wp-setup.php script in your (development) root directory
* Navigate to the script in your web browser
* Insert the required information and sit back and wait for the magic to happen
* Done

## FAQ

### Can't I just use WP Multisite

Yes of course. But sometimes your development environment might restrict you from using WP multisite.
For example if you use localhost with a port number (http://localhost:8080) you won't be able to install
WP Multisite. 

Or maybe you just want to use sepearte installations for each WP site. WordPress Setup makes that easy.

## License

WordPress Setup is released under the MIT license.