<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
global $db_config;
/*
|--------------------------------------------------------------------------
| SMARTY Configuration file
|--------------------------------------------------------------------------
|
*/

/*
|--------------------------------------------------------------------------
| Nom par défaut du répertoire des templates
|--------------------------------------------------------------------------
|
*/
$config['smarty_template_dir'] = 'views/'.$db_config['TEMPLATE'].'/tpl';

/*
|--------------------------------------------------------------------------
| Nom du répertoire où se trouvent les templates compilés
|--------------------------------------------------------------------------
|
*/
$config['smarty_compile_dir']  = '_compiled';

/*
|--------------------------------------------------------------------------
| Nom du répertoire où les caches des templates sont stockés
|--------------------------------------------------------------------------
|
*/
$config['smarty_cache_dir']  = '_cache';

/*
|--------------------------------------------------------------------------
| Nom du répertoire où les images du design sont stockés
|--------------------------------------------------------------------------
|
*/
$config['image_dir']  = 'views/'.$db_config['TEMPLATE'].'/img';

/*
|--------------------------------------------------------------------------
| Nom du répertoire où les CSS du design sont stockés
|--------------------------------------------------------------------------
|
*/
$config['css_dir']  = 'views/'.$db_config['TEMPLATE'].'/css';

/*
|--------------------------------------------------------------------------
| Nom du répertoire où les JS du design sont stockés
|--------------------------------------------------------------------------
|
*/
$config['js_dir']  = 'views/'.$db_config['TEMPLATE'].'/js';

/*
|--------------------------------------------------------------------------
| Nom du répertoire ROOT
|--------------------------------------------------------------------------
|
*/
$config['root_dir']  = 'views/'.$db_config['TEMPLATE'];


/*
|---------------------------------------------------------------------------------
| A chaque invocation de l'application PHP, Smarty fait un test pour voir 
| si le template courant a été modifié (date de dernière modification différente) 
| depuis sa dernière compilation. S'il a changé, le template est recompilé. 
| Si le template n'a pas encore été compilé, 
| il le sera quelque soit la valeur de ce réglage.
|---------------------------------------------------------------------------------
|
*/
$config['compile_check'] = false;

/*
|--------------------------------------------------------------------------
| Celà active la console de débogage. La console est une fenêtre 
| javascript qui vous informe des templates inclus et des variables 
| assignées depuis PHP.
|--------------------------------------------------------------------------
|
*/
$config['debugging'] = false;

/*
|--------------------------------------------------------------------------
| Ce paramètre demande à Smarty de mettre ou non en cache la sortie des 
| templates. Par défaut, ce réglage est à 0 (désactivé). Si vos templates 
| générent du contenu redondant, il est conseillé d'activer le cache. 
| Celà permettra un gain de performance conséquent. 
|--------------------------------------------------------------------------
|
*/
$config['caching'] = false;

/*
|--------------------------------------------------------------------------------------
| Il s'agit de la durée en secondes pendant laquelle un cache de template
| est valide. Une fois cette durée dépassée, le cache est regénéré. 
| Si vous souhaitez donner a certains templates leur propre durée de vie 
| en cache, vous pouvez le faire en réglant  $caching à 2, 
| puis $cache_lifetime à une unique valeur juste avant d'appeler display() ou fetch(). 
|--------------------------------------------------------------------------------------
|
*/
$config['cache_lifetime'] = '';	



/*
|--------------------------------------------------------------------------------------
| Smarty va créer des sous-dossiers dans les dossiers templates_c et 
| cache  si la variable $use_sub_dirs est défini à TRUE (Par défaut, vaut FALSE). 
| Dans un environnement où il peut y avoir potentiellement des centaines de 
| milliers de fichiers de créés, ceci peut rendre le système de fichiers plus rapide. 
|--------------------------------------------------------------------------------------
|
*/	
$config['use_sub_dirs'] = true;


?>