# CakePHP Cacher plugin
The Cacher plugin enables transparent caching of all database queries that are using Model::find()

## Installation and usage
First download or checkout the code for the Cacher and put it in your _app/Plugin_ directory. The directory structure should look like this:

    /app
       /Plugin
          /Cacher
             /Models
                /Behaviors

The easiest way to do this is to directly clone the repository to the right location:

    $ cd app/Plugin/
    $ git clone git://github.com/markomarkovic/cakephp-plugin-cacher.git Cacher
    $ cd Cacher/
    $ git checkout 2.0

Put the code from _Model/AppModel.php_ into your _app/Model/AppModel.php_.

Make sure the plugin is loaded, in the _app/Config/bootstrap.php_ put:

    ...
    CakePlugin::load('Cacher');
    ...

Add the _Cacher.Cacheable_ to the _$actsAs_ in the model you wish to cache:

    ...
    public $actsAs = array('Cacher.Cacheable');
    ...

You can further configure each model to use a different cache configuration, just like you configure the cache engines in _app/Config/core.php_:

    ...
    public $actsAs = array(
        'Cacher.Cacheable' => array(
            'duration' => '+1 day'
        )
    );
    ...

Make sure that the _app/tmp_ directory is writable and that _Cache.disabled_ setting in _app/Config/core.php_ is __NOT__ true and you're ready to go. All the data that you fetch using Model::find() method is going to be transparently cached.

For example, ArticlesController:

    ...
    $articles = $this->Article->find('all', array('conditions' => array('Article.is_published' => true), 'order' => array('Article.publish_on' => 'DESC')));
    ...

if the query with these parameters is cached, data is returned from the cache, otherwise the data is read from the database and cache is updated.

## License
Released under The MIT License

## Credits
Inspired by [Simple way to memcache (almost) all database queries](http://bakery.cakephp.org/articles/view/simple-way-to-memcache-almost-all-database-queries) article by Molot

