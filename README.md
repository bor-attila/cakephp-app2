# CakePHP Application Skeleton 2

Improved version of the CakePHP official skeleton app for creating applications with [CakePHP](https://cakephp.org) 4.x.

The CakePHP and the CakePHP official app itself does not contain any third-party tools, frameworks, bundlers, etc ...
with a very good reason. After many deployed CakePHP app, I decided to build a template with the most used plugins/tools by me.

The framework source code can be found here: [cakephp/cakephp](https://github.com/cakephp/cakephp).

The default skeleton application can be found here: [cakephp/app](https://github.com/cakephp/app).

## Update

Since this skeleton (like the official one) is a starting point for your application and various files
would have been modified as per your needs, there isn't a way to provide automated upgrades, so you have to do any
updates manually.

## Installation

1. Download `composer create-project --prefer-dist bor-attila/cakephp-app2:dev-main [directory]`.
2. Start your webserver.

You can now either use your machine's webserver, or you can use Vagrant

```bash
vagrant up
```

The `https://127.0.0.1:2442` is the main page

The `http://127.0.0.1:8025` is the mailhog address (to see the outgoing emails)

The `https://127.0.0.1:2442/pma` is the phpmyadmin

## Configuration

Read and edit the environment specific `config/app_local.php` and setup the
`'Datasources'` and any other configuration relevant for your application.
Other environment agnostic settings can be changed in `config/app.php` or
in environment variables.

## Changes

This repository contains the following changes compared to [CakePHP App](https://github.com/cakephp/app):

### Additional environment requirements

* PHP APCu

### New dependencies

* Added [Authentication plugin](https://book.cakephp.org/authentication/2/en/index.html)
* Added [Authorization plugin](https://book.cakephp.org/authorization/2/en/index.html)
* Added [dotenv](https://github.com/josegonzalez/php-dotenv) for development
* Added [PHP to Javascript](https://github.com/bor-attila/cakephp-ptj)
* Added __friendsofcake/bootstrap-ui__ as suggested package
* Added __burzum/cakephp-service-layer__ as suggested package

### Changes in _webroot_

* Added empty __scss__ folder
* The default __css__ files was removed (milligram, normalize, etc ...)
* The __font__ directory was renamed to __fonts__ and all fonts was removed
* All default images from __img__ directory was removed
* The default __favicon.ico__ was replaced. All default favicon was added (just use
  [Favicon generator](https://www.favicon-generator.org) to generate favicons and replace the __img/favicon__ content)
* Added default config for [ROLLUP](https://rollupjs.org/)
* Added default config for [Babel](https://babeljs.io/)

### General changes

* The default template was modified
* The login points to _Users::login_, and logout points to _Users::logout_
* The Authorization plugin was added to middleware queue with *requireAuthorizationCheck* set to *false*
* The Authentication plugin was added to middleware queue
* Added Css helper
* Added Javascript helper
* Added Stylesheet helper
* Added Vagrant support

### Configuration changes

* The default __Email__ config *headerCharset* and *charset* was set to *utf-8*
* The __EmailTransport__ changed to *SmtpTransport*, and username and password was set to empty and port changed
  to 1025 to use MailHog
* The default session handler was changed to *cake* cookie name was changed to *s*
* The logging was changed to __ConsoleLog__
* The default cache engine was changed to __APCu__
* The **app_secret.php** file from config directory is automatically parsed and loaded if exists
* **app_secret.php** added to .gitignore
* _webroot/node_modules_ added to .gitignore
* All webroot/css/*.min.css files added to .gitignore
* All webroot/js/*.min.js files added to .gitignore

### Environment variables

* The .env file from config directory is loaded does not matter if the **APP_NAME** is provided or not
* The .env files overwrites the previously set configurations
* Added **APP_CACHE_CONFIG** (default: false) and **APP_CACHE_CONFIG_TIME** (default: 600) environment variables to
  cache the loaded configuration
* The default cache config prefixes now uses **APP_NAME** variable instead of 'myapp', however the 'myapp' the default
  value
* The **APP_ADDITIONAL_CONFIG** added. Must be a full path to a valid config file. file from config directory is
  automatically parsed if exists. If you are planning to use this feature, caching is highly recommended.
* Added **DEBUG_KIT_ENABLED** (default: true). Allows you to control conditionally loading the plugin
* Added **DEBUG_KIT_FORCE_ENABLE** (default: false). Allows you to use DebugKit on non-whitelisted tlds.

__Warning__: NEVER upload .env file to production, set all variables from other source
(docker-compose yaml file, webserver config, etc ...)

__Warning__: The **APP_NAME** should match to `[a-z_]+` regex

__Warning__: In development since the .env file is cached the **APP_NAME**, and the **APP_CACHE_CONFIG** MUST be set
earlier (by the environment/webserver). If you are using your own webserver you can set these in apache2:

SetEnv APP_NAME myapp

SetEnv APP_CACHE_CONFIG true

SetEnv APP_CACHE_CONFIG_TIME 0

---

## Helpers

### CSS helper

In _View\AppView.php_ add this to the _initialize_ method for expl:

```php
$this->loadHelper('Css');
```

OR

```php
$this->loadHelper('Css', [
    'storage' => [
        'body' => ['bodyclass'],
    ]
]);
```

The CSS helper is just an array manipulation. In the container you can store class names.

```
add(string $container, string $class, ?string $overwrite = null): bool
remove(string $container, string $class): bool
has(string $container, string $class): bool
get(string $container): string
```

```
<html>
    <head>
    </head>
    <body <?= $this->Css->get(); ?>>

        //In the tempalte
        $this->Css->add('body', 'green');

        //Or conditionally
        if (true) {
            $this->Css->add('body', 'red', 'green');// the green will be replaced with red
            $this->Css->remove('body', 'red');// or remove
        }
    </body>
</html>
```

### StyleSheet helper

In _View\AppView.php_ add this to the _initialize_ method:

```php
$this->loadHelper('Stylesheet', [
    'cache' => 'default'
]);
```

The __StyleSheet helper__ helps to load CSS file content and inject it directly into the body.
These methods search for specified CSS files, opens, creates a style tag and stores it into cache (if it's set).

```
global(array $stylesheets = []): string
```
Returns the global stylesheet's content. Automatically searches for the css/style[.hash]?[.min]?.css
You can add more CSS files as parameter.

```
local(): string
```
Returns the local stylesheet's content. Automatically searches for:
* css/{prefix}-{controller}-{action}[.hash]?[.min]?.css
* css/{controller}-{action}[.hash]?[.min]?.css if there is no prefix.

```
inline(string $name): string
```
Returns the given stylesheet's content. Automatically searches for the css/{name}[.hash]?[.min]?.css

### Javascript helper

In _View\AppView.php_ add this to the _initialize_ method:

```php
$this->loadHelper('Javascript', [
    'cache' => 'default'
]);
```

How to use:

```
<html>
    <head>
    </head>
    <body>
        ....
        <?= $this->Html->script($this->Javascript->files('main', 'debug', 'awesome')); ?>
        <!--
            This returns
            <script src="js/main.laknsdn78t7f34t79.min.js" />
            <script src="js/debug.sanibiobrevoybowueb.min.js" />
            <script src="js/awesome.wqojndiqwd766686.min.js" />
            or even
            <script src="js/main.min.js" />
            <script src="js/awesome.min.js" />
        -->
    </body>
</html>
```

The __Javascript__ and __Stylesheet__ helpers not clears caches. After you a rebuild the project, you must do it yourself.

If you are using kubernetes/docker then memory cache isn't a problem, in other cases you can use FileCache engine.

---

## Front-end development

Your frontend development directory is the: `webroot` directory. Should look like this:

```
+-- css
+-- scss
|   +-- style.scss
+-- plugins
+-- js
|   +-- src
|       +-- components
|       +-- mixins
|       +-- store
|       +-- main.app.js
|   +-- static
|       +-- script.js
+-- babel.config.json
+-- rollup.config.js
+-- packages.json
```

The `css` folder contains the compiled stylesheets.

The `scss` folder contains the stylesheet source code.

The `plugins` folder contains static production ready third party libraries (eg. bootstrap, axios, select2).

The `js` folder contains the compiled javascript files.

The `js/src` folder contains javascript app(!) source files - vue, react etc... Eg: x.app.js, y.app.js.

The `js/src/components` folder contains javascript app components source files.

The `js/src/mixins` folder contains javascript reusable components.

The `js/static` folder contains javascript source code that can be included directly into page ('old way').

To install all dependencies just run: `yarn install` in the **webroot** directory

This will install every dependency for your basic setup.

Your vagrant machine contains all frontend development tool what you need (yarn, npm, mysql, etc ..)

To see every tool just check the [fatbox](https://app.vagrantup.com/atee/boxes/fatbox) readme's.

### Working with stylesheets

#### Production

When you run `yarn rollup:scss:build` all sass files from scss folder __which starts with a letter__ (^[a-zA-Z])
will be compiled into CSS and minimized.

For example:
* __scss/style.scss__ -> __css/style.min.css__
* __scss/mystyle.scss__ -> __css/mystyle.min.css__
* __scss/\_variables.scss__ remains untouched (ofc if you included in your __style.scss__ then will be compiled)

__WARNING__: All *.min.css files are git ignored. You should use your own CI/CD for build.

#### Development

When you run `yarn rollup:scss:watch` all sass files from scss folder __which starts with a letter__ (^[a-zA-Z])
will be compiled into CSS, and the sass compiler will listen to file changes.

### Working with Javascript

You can use `yarn rollup:js:build` for production and `yarn rollup:js:watch` to development.
`yarn rollup:js:clean` will remove all compiled minified js file.

This package contains a basic rollup configuration and a basic babel configuration for modern JS development.

## Known issues

With vagrant can happen that the `yarn rollup:scss:watch` and `yarn rollup:js:watch` does not listen to file changes,
and does not re-trigger the build on save.

You can try:

* In case of SCSS to use [--poll](https://sass-lang.com/documentation/cli/dart-sass#poll) option. Just run
  `yarn rollup:scss:watch --poll` instead of `yarn rollup:scss:watch`
* In case of Rollup you can disabled [fsEvents](https://rollupjs.org/guide/en/#error-emfile-too-many-open-files)

Alternatively you can try to use [winnfsd](https://github.com/winnfsd/vagrant-winnfsd) instead of native vbox mount
option.
