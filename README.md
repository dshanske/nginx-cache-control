nginx-cache-control
===================

Nginx Cache Control Plugin without the Cache Purge Module

Some of the code is based on the code of other Nginx Cache Plugins, but this is designed to be a simple, lightweight implementation

== Specifically.... ==

Mark Jaquith came up with the basic design using a header that forces a dynamic
page load, and described it in a blog post in 2012.

http://markjaquith.wordpress.com/2012/05/15/how-i-built-have-baby-need-stuff/

An example of a php configuration file for Nginx can be found as example.conf

The timestamp code is based on the code used in Nginx Helper, which uses the 
nginx-cache-purge module.

https://github.com/rtCamp/nginx-helper

I reviewed Peter Molnar's WP-FFPC plugin, which uses a different caching technology. My taxonomy link retrieval came out of his code.

https://github.com/petermolnar/wp-ffpc

Looking at these plugins give me ideas for expanding this plugin, which is designed to be minimalist as muc as possible.

== DDOS Protection ==

Assuming this becomes a popular caching method, a DDOS attack could be
perpetrated if they start sending the X-Nginx-Cache-Purge:1 header to
slow refreshes. To prevent this, a simple solution is to not use 1
as the value.

To support this, you can set the value of the header to anything
as long as you do so in your Nginx configuration.

Add the following to your wp-config.php to customize. 

define( 'NGINX_PURGE_KEY', '123456' );

