## Development abandoned.

This plugin is no longer maintained. This has been a pet-project-turned-useful during the time that I've worked on it. This has been the first WordPress plugin I wrote, and it has been a learning experience.

## GeoIP Site Locations 0.4.4#

GeoIPSL is a WordPress plugin that detects user location based on IP or cookie 
information and redirects to the appropriate geo-targeted version of your site.

##Plugin Setup for Development##

Before you can start work with developing this plugin, you should have the 
following installed on your system:

1. [Composer](https://getcomposer.org/).
1. [Node.js](https://nodejs.org/)
1. [Grunt CLI](https://github.com/gruntjs/grunt-cli)

This plugin uses several PHP libraries. In order to build the plugin, follow the 
following steps.

1. Download this repo and extract it into the plugins/wp-geoip-site-locations/ 
directory of your WordPress install. Alternatively, you can just ```cd``` into 
the plugins directory clone this repository.
1. After following these step, you should be able to run ```composer install```. 
This should install all the dependencies.
1. Run ```composer dump-autoload -o``` to generate the auto-loaded files.
1. Run ```npm install``` to install all the node modules used.

At this point the plugin should be ready for use. You may optionally follow this 
step if you wish use a working copy of the plugin with a much much smaller 
number of files ( e.g., in production or on a remote site ):

1. Run ```grunt build``` to generate the clean plugin build files.
2. Use the generated ZIP file for uploading into WordPress.

##Roadmap to GeoIP Site Locations 1.0.0##

1. Add support for sub-directory subsites, instead of just sub-domain subsites.
1. Add support for location clustering/grouping. Presently only the root site 
serves as the main entry point before URL redirects occur. This is a problem 
because this will limit the plugin user to use one WordPress install for every 
domain s/he wishes to use. To address this, the plugin must allow subsites ( or 
location entries with associated URLs ) to be arbitrarily grouped together, with 
one URL in the group serving as the site entry point.
1. Total overhaul on the way the plugin stores location information. Currently 
everything is on the options table of the root site. Refer to (2) above.
1. Enable the plugin user to add external URLs on the list of sites. Presently 
you can only redirect to subsites within your WordPress install. Ideally the 
plugin users may also wish to redirect to an external geo-targetted site.
1. Add feature to "suggest" which subsite to remember and serve next tiem based 
on site visitors previous browsing behaviour.
1. Make the list of sites searchable.
1. Reduce the amount of loops needed for Vicenty's modified algorithm to 
converge. This will require the [implementation of Newton's Method as described 
in Karney's 2012 
paper](http://link.springer.com/content/pdf/10.1007%2Fs00190-012-0578-z).
1. Add integration with WP Engine's GeoIP service for Business Plan subscribers. 
Refer to their plugin [here](https://github.com/wpengine/geoip).

##License##

[GPLv2+](http://www.gnu.org/licenses/gpl-2.0.html)
